<?php

namespace App\Http\Middleware\Front;

use Closure;
use App\Models\Front\ShopStore;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
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
        //Check domain exist
        $domain = lc_process_domain_store(basename(request()->headers->get('referer')));
        $arrDomain = ShopStore::getDomainPartner();
        if (!in_array($domain, $arrDomain) && lc_config_global('domain_strict',$arrDomain[$domain])) {
            return response()->json(new JsonResponse([], 'Access denied'), Response::HTTP_FORBIDDEN);
        }
        $currentStore = $request->header('x-store');
        if (!$currentStore) {
            $referer = request()->headers->get('referer');
            if (!$referer) {
                $referer = request()->header()['origin'][0];
                $request->headers->set('referer', $referer);
            }
            $domain = lc_process_domain_store(basename($referer));
            $storeId = array_search($domain, $arrDomain);
            $request->headers->set('x-store', $storeId);
        }
        return $next($request);
    }
}
