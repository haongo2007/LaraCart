<?php
namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopStore;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;

class ShopStoreController extends Controller
{
    public function getInfo()
    {
        $domain = lc_process_domain_store(basename(request()->headers->get('referer')));
        $arrDomain = ShopStore::getDomainPartner();
        if (!in_array($domain, $arrDomain) && lc_config_global('domain_strict')) {
            return response()->json(new JsonResponse([], 'Access denied'), Response::HTTP_FORBIDDEN);
        }else{
        	$key = array_search($domain, $arrDomain);
        	$data = ShopStore::with('descriptionsCurrentLang')->find($key);
            return response()->json(new JsonResponse($data, ''), Response::HTTP_OK);
        }
    }
}
