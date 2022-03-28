<?php

namespace App\Http\Middleware;

use App\Models\Front\ShopLanguage;
use Closure;
use Session;

class Localization
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
        //Set language
        $currentLocale = $request->header('x-localization');
        if ($currentLocale) {
            app()->setLocale($currentLocale);   
        }
        //End language
        return $next($request);
    }
}
