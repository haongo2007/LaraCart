<?php

namespace App\Http\Middleware\Admin;

use Closure;
use Session;
use App\Models\Admin\Admin;

class AdminTheme
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
        if (!Session::has('adminTheme')) {
            $adminTheme = (Admin::user())?Admin::user()->theme : '';
        } else {
            $adminTheme = session('adminTheme');
        }
        $currentTheme = in_array($adminTheme, config('admin.theme')) ? $adminTheme : config('admin.theme_default');
        session(['adminTheme' => $currentTheme]);
        config(['admin.theme_default' => $currentTheme]);
        return $next($request);
    }
}
