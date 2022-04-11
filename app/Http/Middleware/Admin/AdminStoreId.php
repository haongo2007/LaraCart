<?php

namespace App\Http\Middleware\Admin;

use Closure;
use Session;
use App\Models\Admin\Admin;
use Illuminate\Support\Facades\Auth;
use App\Helper\Permission;

class AdminStoreId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$args)
    {
        if (!empty($args) || $this->shouldPassThrough($request)) {
            return $next($request);
        }
        $currentStore = $request->header('x-store');
        if (!is_array($currentStore)) {
            $currentStore = json_decode($currentStore);
            if (!is_array($currentStore)) {
                $currentStore = [$currentStore];
            }
        }
        $currentStore = array_filter($currentStore);
        $request->headers->set('x-store', $currentStore);
        if ($currentStore) {
            $arrStoreId = Admin::user()->listStoreId();
            if (array_intersect($currentStore, $arrStoreId)) {
                return $next($request);
            }else{
                return Permission::error();
            }
        }else{
            if (Admin::user()->isAdministrator()) {
                return $next($request);
            }
            return Permission::error();
        }
        return $next($request);
    }

    protected function shouldPassThrough($request)
    {
        $routePath = $request->path();
        $exceptsPAth = [
            LC_ADMIN_AUTH.'/'.LC_ADMIN_PREFIX . '/auth/info',
            LC_ADMIN_AUTH.'/'.LC_ADMIN_PREFIX . '/auth/logout',
        ];
        return in_array($routePath, $exceptsPAth);
    }
}
