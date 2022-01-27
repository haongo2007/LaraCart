<?php

namespace App\Http\Middleware\Front;

use Closure;
use App\Models\Front\ShopStore;

class CheckDomain
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
        if (lc_config_global('MultiVendorPro') || lc_config_global('MultiStorePro')) {
            //Check domain exist
            $domain = lc_process_domain_store(url('/'));
            $arrDomain = ShopStore::getDomainPartner();
            if (!in_array($domain, $arrDomain) && lc_config_global('domain_strict') && config('app.storeId') != BC_ID_ROOT) {
                echo view('deny_domain')->render();
                exit();
            }
        }
        return $next($request);
    }
}
