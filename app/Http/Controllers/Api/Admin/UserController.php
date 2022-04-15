<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionCollection;
use App\Http\Resources\UserCollection;
use App\Helper\JsonResponse;
use App\Models\Admin\Permission;
use App\Models\Admin\Role;
use App\Models\Admin\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Validator;

/**
 * Class UserController
 *
 * @package App\Http\Controllers\Api
 */
class UserController extends Controller
{
    const ITEM_PER_PAGE = 15;

    /**
     * Display a listing of the user resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|ResourceCollection
     */
    public function index(Request $request)
    {
        $searchParams = request()->all();
        $data = (new User)->getUsersListAdmin($searchParams);
        return UserCollection::collection($data)->additional(['message' => 'Successfully']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            array_merge(
                $this->getValidationRules(),
                [
                    'password' => ['required', 'min:6'],
                ]
            )
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {
            $params = $request->all();
            $user = User::create([
                'name' => $params['fullname'],
                'email' => $params['email'],
                'phone' => $params['phone'],
                'password' => Hash::make($params['password']),
            ]);
            $roles = $params['roles'] ?? [];
            $permissions = $params['permissions'] ?? [];
            $stores = $params['stores'] ?? [];
            
            if ($roles) {
                $user->roles()->attach($roles);
            }
            if ($permissions) {
                $user->permissions()->attach($permissions);
            }
            if ($stores) {
                $user->stores()->attach($stores);
            }


            return response()->json(new JsonResponse([]), Response::HTTP_OK);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  User $user
     * @return UserCollection|\Illuminate\Http\JsonResponse
     */
    public function show(Request $request,User $user)
    {
        return new UserCollection($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User    $user
     * @return UserCollection|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user)
    {
        $currentUser = Auth::user();
        if ($user === null) {
            return response()->json(['error' => 'User not found'], 404);
        }
        if ($user->isAdmin()) {
            if ($currentUser->id != $user->id) {
                return response()->json(['error' => 'Admin can not be modified'], 403);
            }
        }
        if (!$currentUser->isAdmin()
            && $currentUser->id !== $user->id
            && !$currentUser->hasPermission(\App\Larviu\Acl::PERMISSION_USER_MANAGE)
        ) {
            return response()->json(['error' => 'Permission denied'], 403);
        }

        $validator = Validator::make($request->all(), $this->getValidationRules(false));
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {
            $email = $request->get('email');
            $found = User::where('email', $email)->first();
            if ($found && $found->id !== $user->id) {
                return response()->json(['error' => 'Email has been taken'], 403);
            }

            $user->name = $request->get('name');
            $user->email = $email;
            $user->save();
            return new UserCollection($user);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User    $user
     * @return UserCollection|\Illuminate\Http\JsonResponse
     */
    public function updatePermissions(Request $request, User $user)
    {
        if ($user === null) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($user->isAdmin()) {
            return response()->json(['error' => 'Admin can not be modified'], 403);
        }

        $permissionIds = $request->get('permissions', []);
        $rolePermissionIds = array_map(
            function($permission) {
                return $permission['id'];
            },

            $user->getPermissionsViaRoles()->toArray()
        );

        $newPermissionIds = array_diff($permissionIds, $rolePermissionIds);
        $permissions = Permission::allowed()->whereIn('id', $newPermissionIds)->get();
        $user->syncPermissions($permissions);
        return new UserCollection($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->isAdministrator()) {
            return response()->json(new JsonResponse([],'Ehhh! Can not delete admin user'), Response::HTTP_BAD_REQUEST);
        }
        try {
            $user->delete();
        } catch (\Exception $ex) {
            response()->json(['error' => $ex->getMessage()], 403);
        }

        return response()->json(null, 204);
    }

    /**
     * Get permissions from role
     *
     * @param User $user
     * @return array|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function permissions(User $user)
    {
        try {
            return new JsonResponse([
                'user' => PermissionCollection::collection($user->getDirectPermissions()),
                'role' => PermissionCollection::collection($user->getPermissionsViaRoles()),
            ]);
        } catch (\Exception $ex) {
            response()->json(['error' => $ex->getMessage()], 403);
        }
    }

    /**
     * @param bool $isNew
     * @return array
     */
    private function getValidationRules($isNew = true)
    {
        return [
            'fullname' => 'required',
            'phone' => 'required',
            'email' => $isNew ? 'required|email|unique:'.User::class : 'required|email',
            'stores' => ['array','required'],
            'roles' => [
                'required',
                'array'
            ],
        ];
    }
    /**
     * @param bool $idUser
     * @return array
     */
    public function updateAvatar(Request $request)
    {
        $user = Auth::user();        
        $path = 'user/avatar/'.$user->id;
        $fileName = $request->file('avatar')->hashName();
        $request->file('avatar')->storeAs(
            $path,$fileName
        );
        $url = 'api/'.config('const.LC_ADMIN_PREFIX').'/getFile?disk='.env('FILESYSTEM_DRIVER', 'local').'&path='.urlencode($path.'/'.$fileName);
        $user->avatar = $url;
        $user->save();
        return response()->json(['data' => ['status' => 'success','avatar' => $url] ], 200);
    }
    /**
     * @param bool $path
     * @return a file
     */
    public function getFileFromS3(Request $request)
    {
        $image = Image::make(Storage::disk($request->input('disk'))->get(urldecode($request->input('path'))));
        if ($request->input('w') || $request->input('h')) {
            $w = $request->input('w') ? $request->input('w') : null;
            $h = $request->input('h') ? $request->input('h') : null;
            $image->resize($w, $h,function ($constraint) use ($w,$h){
                if (!$w || !$h) {
                    $constraint->aspectRatio();
                }
            });
        }
        return $image->response();
    }
}