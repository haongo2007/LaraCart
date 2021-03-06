<?php
namespace App\Http\Controllers\Api\Admin;

use App\Models\Admin\Permission;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Http\Resources\PermissionCollection;
use App\Helper\JsonResponse;
use Validator;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


class PermissionsController extends Controller
{
    public function index()
    {
        if (!Cache::has('cache_permission')) {
            $searchParams = request()->all();
            lc_set_cache('cache_permission', (new Permission)->getPermissionsListAdmin($searchParams),session('adminStoreId'));
        }
        $data = Cache::get('cache_permission');
        return PermissionCollection::collection($data)->additional(['message' => 'Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param  Permission $permission
     * @return PermissionCollection|\Illuminate\Http\JsonResponse
     */
    public function show(Request $request,Permission $permission)
    {
        return new PermissionCollection($permission);
    }
    /**
    * Post create new item in admin
    * @return [type] [description]
    */
    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required|string|max:50|unique:"'.Permission::class.'",name',
            'slug' => 'required|regex:/(^([0-9A-Za-z\._\-]+)$)/|unique:"'.Permission::class.'",slug|string|max:50|min:3',
        ], [
            'slug.regex' => trans('permission.slug_validate'),
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }

        $dataInsert = [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'http_uri' => implode(',', ($data['http_uri'] ?? [])),
        ];

        $permission = Permission::createPermission($dataInsert);

        return response()->json(new JsonResponse(), Response::HTTP_OK);

    }

/**
 * update status
 */
    public function update(Request $request,$id)
    {
        $permission = Permission::find($id);
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required|string|max:50|unique:"'.Permission::class.'",name,' . $permission->id . '',
            'slug' => 'required|regex:/(^([0-9A-Za-z\._\-]+)$)/|unique:"'.Permission::class.'",slug,' . $permission->id . '|string|max:50|min:3',
        ], [
            'slug.regex' => trans('permission.slug_validate'),
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }

        $dataUpdate = [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'http_uri' => implode(',', ($data['http_uri'] ?? [])),
        ];
        $permission->update($dataUpdate);
        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }

/*
Delete list Item
Need mothod destroy to boot deleting in model
 */
    public function destroy($permissions)
    {
        $arrID = explode(',', $permissions);
        Permission::destroy($arrID);
        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }

    public function without()
    {
        $prefix = LC_ADMIN_AUTH.'/'.LC_ADMIN_PREFIX;
        return [
            $prefix . '/auth/login',
            $prefix . '/auth/logout',
            $prefix . '/auth/info',
            $prefix . '/forgot',
            $prefix . '/deny',
            $prefix . '/locale',
            $prefix . '/sanctum',
        ];
    }

    public function getAllPath()
    {
        $routes = Route::getRoutes();
        $routeAdmin = [];
        foreach ($routes as $route) {
            if (Str::startsWith($route->uri(), LC_ADMIN_AUTH.'/'.LC_ADMIN_PREFIX)) {
                $prefix = LC_ADMIN_AUTH.'/'.LC_ADMIN_PREFIX;

                $routeAdmin[$prefix] = [
                    'uri'    => 'ANY::' . $prefix . '/*',
                    'name'   => $prefix . '/*',
                    'method' => 'ANY',
                ];
                foreach ($route->methods as $key => $method) {
                    if ($method != 'HEAD' && $method != 'PATCH' && !collect($this->without())->first(function ($exp) use ($route) {
                        return Str::startsWith($route->uri, $exp);
                    })) {
                        $prf = explode('/', $route->uri)[2];
                        $routeAdmin[$prf][] = [
                            'uri'    => $method . '::' . $route->uri,
                            'name'   => $route->uri,
                            'method' => $method,
                        ];
                    }

                }
            }elseif (Str::startsWith($route->uri(), 'file-manager')) {
                $routeAdmin['file-manager'] = [
                    'uri'    => 'ANY::file-manager/*',
                    'name'   => 'file-manager/*',
                    'method' => 'ANY',
                ];
            }

        }
        return response()->json(new JsonResponse($routeAdmin), Response::HTTP_OK);

    }
}
