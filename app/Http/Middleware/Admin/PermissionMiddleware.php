<?php

namespace App\Http\Middleware\Admin;

use App\Models\Admin\Admin;
use App\Helper\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;

class PermissionMiddleware
{
    /**
     * @var string
     * Example midleware roles admin.permission:allow,administrator,editor
     */
    protected $middlewarePrefix = 'admin.permission:';

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param array                    $args
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next, ...$args)
    {
        if (!Admin::user()) {
            return $next($request);   
        }
        if (!empty($args) || $this->shouldPassThrough($request) || Admin::user()->isAdministrator()) {
            return $next($request);
        }

        // Allow access route
        if ($this->routeDefaultPass($request)) {
            return $next($request);
        }

        //Check middware in route
        if ($this->checkRoutePermission($request)) {
            return $next($request);
        }

        //Group view all
        // this group can view all path, but cannot change data
        if (Admin::user()->isViewAll()) {
            if ($request->method() == 'GET'
                && !collect($this->viewWithoutToMessage())->contains($request->path())
                && !collect($this->viewWithout())->contains($request->path())
            ) {
                return $next($request);
            } else {
                if (!request()->ajax()) {
                    if (collect($this->viewWithoutToMessage())->contains($request->path())) {
                        return Permission::error();
                    }
                        return Permission::error();
                } else {
                    if (collect($this->viewWithoutToMessage())->contains($request->path())) {
                        return Permission::error();
                    }
                }
            }
        }
        if (!Admin::user()->allPermissions()->first(function ($modelPermission) use ($request) {
            return $modelPermission->passRequest($request);
        })) {
            if (!request()->ajax()) {
                return response()->json(new JsonResponse([], 'Access denied'), Response::HTTP_FORBIDDEN);
            } else {
                return Permission::error();
            }
        }
        return $next($request);
    }

    /**
     * If the route of current request contains a middleware prefixed with 'admin.permission:',
     * then it has a manually set permission middleware, we need to handle it first.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function checkRoutePermission(Request $request)
    {
        if (!$middleware = collect($request->route()->middleware())->first(function ($middleware) {
            return Str::startsWith($middleware, $this->middlewarePrefix);
        })) {
            return false;
        }
        $args = explode(',', str_replace($this->middlewarePrefix, '', $middleware));

        $method = array_shift($args);

        if (!method_exists(Permission::class, $method)) {
            throw new \InvalidArgumentException("Invalid permission method [$method].");
        }

        call_user_func_array([Permission::class, $method], [$args]);

        return true;
    }

    /**
     * Determine if the request has a URI that should pass through verification.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function shouldPassThrough($request)
    {
        $routePath = $request->path();
        $exceptsPAth = [
            LC_ADMIN_AUTH.'/'.LC_ADMIN_PREFIX . '/auth/login',
            LC_ADMIN_AUTH.'/'.LC_ADMIN_PREFIX . '/auth/logout',
        ];
        return in_array($routePath, $exceptsPAth);
    }

    /*
    Check route defualt allow access
    */
    public function routeDefaultPass($request)
    {
        $routeName = $request->route()->getName();
        $allowRoute = ['admin.deny', 'admin.deny_single', 'admin.locale', 'admin.home', 'admin.theme','admin_store.switch', 'admin.data_not_found'];
        return in_array($routeName, $allowRoute);
    }

    public function viewWithout()
    {
        return [
            // Array item in here
        ];
    }

    /**
     * Send page deny as meeasge
     *
     * @return  [type]  [return description]
     */
    public function viewWithoutToMessage()
    {
        return [
            LC_ADMIN_AUTH.'/'.LC_ADMIN_PREFIX . '/uploads/delete',
            LC_ADMIN_AUTH.'/'.LC_ADMIN_PREFIX . '/uploads/newfolder',
            LC_ADMIN_AUTH.'/'.LC_ADMIN_PREFIX . '/uploads/domove',
            LC_ADMIN_AUTH.'/'.LC_ADMIN_PREFIX . '/uploads/rename',
            LC_ADMIN_AUTH.'/'.LC_ADMIN_PREFIX . '/uploads/resize',
            LC_ADMIN_AUTH.'/'.LC_ADMIN_PREFIX . '/uploads/doresize',
            LC_ADMIN_AUTH.'/'.LC_ADMIN_PREFIX . '/uploads/cropimage',
            LC_ADMIN_AUTH.'/'.LC_ADMIN_PREFIX . '/uploads/crop',
            LC_ADMIN_AUTH.'/'.LC_ADMIN_PREFIX . '/uploads/move',
        ];
    }

}
