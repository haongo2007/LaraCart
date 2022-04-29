<?php

namespace App\Http\Middleware\Front;

use Closure;
use App\Models\Front\ShopStore;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;

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
        // if (lc_config_global('MultiVendorPro') || lc_config_global('MultiStorePro')) {
        //     //Check domain exist
        //     $domain = lc_process_domain_store(basename(request()->headers->get('referer')));
        //     $arrDomain = ShopStore::getDomainPartner();
        //     if (!in_array($domain, $arrDomain) && lc_config_global('domain_strict')) {
        //         return response()->json(new JsonResponse([], 'Access denied'), Response::HTTP_FORBIDDEN);
        //     }
        //}
        return $next($request);
    }
}
