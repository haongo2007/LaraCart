<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Admin\Role;
use App\Http\Resources\RoleCollection;
use App\Helper\JsonResponse;
use Validator;
/**
 * Class RoleController
 *
 * @package App\Http\Controllers\Api
 */
class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $searchParams = request()->all();
        $data = (new Role)->getRolesListAdmin($searchParams);
        return RoleCollection::collection($data)->additional(['message' => 'Successfully']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = request()->all();
        $validator = Validator::make($data, [
            'name' => 'required|string|max:50|unique:"'.Role::class.'",name',
            'slug' => 'required|regex:/(^([0-9A-Za-z\._\-]+)$)/|unique:"'.Role::class.'",slug|string|max:50|min:3',
        ], [
            'slug.regex' => trans('admin.role.slug_validate'),
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }

        $dataInsert = [
            'name' => $data['name'],
            'slug' => $data['slug'],
        ];
        $role = Role::createRole($dataInsert);
        $permissions = $data['permissions'] ?? [];
        //Insert permission
        if ($permissions) {
            $role->permissions()->attach($permissions);
        }
        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  Role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        return new RoleCollection($role);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Role $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $data = request()->all();
        $validator = Validator::make($data, [
            'name' => 'required|string|max:50|unique:"'.Role::class.'",name,' . $role->id . '',
            'slug' => 'required|regex:/(^([0-9A-Za-z\._\-]+)$)/|unique:"'.Role::class.'",slug,' . $role->id . '|string|max:50|min:3',
        ], [
            'slug.regex' => trans('role.slug_validate'),
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }

        $dataUpdate = [
            'name' => $data['name'],
            'slug' => $data['slug'],
        ];
        $role->update($dataUpdate);

        $permissions = $data['permissions'] ?? [];

        $role->permissions()->detach();
        //Insert permission
        if ($permissions) {
            $role->permissions()->attach($permissions);
        }

        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $arrID = explode(',', $id);
        $arrID = array_diff($arrID, LC_GUARD_ROLES);
        Role::destroy($arrID);
        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }

}