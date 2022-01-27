<?php

namespace App\Http\Middleware\Admin;

use Closure;
use Session;
use App\Models\Admin\Admin;
use Illuminate\Support\Facades\Auth;

class AdminStoreId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Admin::user()) {
            session()->forget('adminStoreId');
            $allStoreId = \App\Models\Admin\Store::getArrayStoreId();
            if (!Session::has('adminStoreId') || !in_array(session('adminStoreId'), $allStoreId)) {
                //Get array list store id of user
                $arrStoreId = Admin::user()->listStoreId();
                //Compare with list Id store
                $fillterStoreId = array_intersect($arrStoreId, $allStoreId);
                if ($fillterStoreId) {
                    $adminStoreId = min($fillterStoreId);
                    session(['adminStoreId' => $adminStoreId]);
                } else {
                    session(['adminStoreId' => 0]);
                    //Check access vendor admin
                    if (function_exists('lc_vendor_check_access_vendor_admin')) {
                        lc_vendor_check_access_vendor_admin();
                    }
                }
            }
        } else {
            session()->forget('adminStoreId');
        }
        return $next($request);
    }
}
