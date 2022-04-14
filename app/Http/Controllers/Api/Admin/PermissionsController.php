<?php
namespace App\Http\Controllers\Api\Admin;

use App\Models\Admin\Permission;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Http\Resources\PermissionCollection;
use Validator;

class PermissionsController extends Controller
{

    public $routeAdmin;

    public function __construct()
    {
        
        // $routes = app()->routes->getRoutes();

        // foreach ($routes as $route) {
        //     if (Str::startsWith($route->uri(), LC_ADMIN_PREFIX)) {
        //         $prefix = LC_ADMIN_PREFIX?$route->getPrefix():ltrim($route->getPrefix(),'/');
        //         $routeAdmin[$prefix] = [
        //             'uri'    => 'ANY::' . $prefix . '/*',
        //             'name'   => $prefix . '/*',
        //             'method' => 'ANY',
        //         ];
        //         foreach ($route->methods as $key => $method) {
        //             if ($method != 'HEAD' && !collect($this->without())->first(function ($exp) use ($route) {
        //                 return Str::startsWith($route->uri, $exp);
        //             })) {
        //                 $routeAdmin[] = [
        //                     'uri'    => $method . '::' . $route->uri,
        //                     'name'   => $route->uri,
        //                     'method' => $method,
        //                 ];
        //             }

        //         }
        //     }

        // }

        // $this->routeAdmin = $routeAdmin;
    }

    public function index()
    {
        $searchParams = request()->all();
        $data = (new Permission)->getPermissionsListAdmin($searchParams);
        return PermissionCollection::collection($data)->additional(['message' => 'Successfully']);
    }

/**
 * Form create new item in admin
 * @return [type] [description]
 */
    public function create()
    {
        $data = [
            'title' => trans('permission.admin.add_new_title'),
            'subTitle' => '',
            'title_description' => trans('permission.admin.add_new_des'),
            'icon' => 'fa fa-plus',
            'permission' => [],
            'routeAdmin' => $this->routeAdmin,
            'url_action' => lc_route_admin('admin_permission.create'),

        ];

        return view($this->templatePathAdmin.'Auth.permission')
            ->with($data);
    }

/**
 * Post create new item in admin
 * @return [type] [description]
 */
    public function postCreate()
    {
        $data = request()->all();
        $dataOrigin = request()->all();
        $validator = Validator::make($dataOrigin, [
            'name' => 'required|string|max:50|unique:"'.Permission::class.'",name',
            'slug' => 'required|regex:/(^([0-9A-Za-z\._\-]+)$)/|unique:"'.Permission::class.'",slug|string|max:50|min:3',
        ], [
            'slug.regex' => trans('permission.slug_validate'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $dataInsert = [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'http_uri' => implode(',', ($data['http_uri'] ?? [])),
        ];

        $permission = Permission::createPermission($dataInsert);

        return redirect()->route('admin_permission.index')->with('success', trans('permission.admin.create_success'));

    }

/**
 * Form edit
 */
    public function edit($id)
    {
        $permission = Permission::find($id);
        if ($permission === null) {
            return 'no data';
        }
        $data = [
            'title' => trans('permission.admin.edit'),
            'subTitle' => '',
            'title_description' => '',
            'icon' => 'fa fa-edit',
            'permission' => $permission,
            'routeAdmin' => $this->routeAdmin,
            'url_action' => lc_route_admin('admin_permission.edit', ['id' => $permission['id']]),
        ];
        return view($this->templatePathAdmin.'Auth.permission')
            ->with($data);
    }

/**
 * update status
 */
    public function postEdit($id)
    {
        $permission = Permission::find($id);
        $data = request()->all();
        $dataOrigin = request()->all();
        $validator = Validator::make($dataOrigin, [
            'name' => 'required|string|max:50|unique:"'.Permission::class.'",name,' . $permission->id . '',
            'slug' => 'required|regex:/(^([0-9A-Za-z\._\-]+)$)/|unique:"'.Permission::class.'",slug,' . $permission->id . '|string|max:50|min:3',
        ], [
            'slug.regex' => trans('permission.slug_validate'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
//Edit

        $dataUpdate = [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'http_uri' => implode(',', ($data['http_uri'] ?? [])),
        ];
        $permission->update($dataUpdate);
//
        return redirect()->route('admin_permission.index')->with('success', trans('permission.admin.edit_success'));

    }

/*
Delete list Item
Need mothod destroy to boot deleting in model
 */
    public function deleteList()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => trans('admin.method_not_allow')]);
        } else {
            $ids = request('ids');
            $arrID = explode(',', $ids);
            Permission::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    public function without()
    {
        $prefix = LC_ADMIN_PREFIX?LC_ADMIN_PREFIX.'/':'';
        return [
            $prefix . 'login',
            $prefix . 'logout',
            $prefix . 'forgot',
            $prefix . 'deny',
            $prefix . 'locale',
            $prefix . 'uploads',
        ];
    }

}
