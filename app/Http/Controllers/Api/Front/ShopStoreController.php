<?php
namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopStore;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;
use App\Models\Front\ShopLanguage;
use App\Models\Front\ShopCurrency;
use App\Models\Front\ShopCategory;

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
        	$data = [];
            $data['store'] = ShopStore::with('descriptionsCurrentLang')->find($key);
            $data['languages'] = ShopLanguage::where('store_id',$key)->get();
            $data['currencies'] = ShopCurrency::where('store_id',$key)->get();
            $data['categories'] = ShopCategory::with('descriptionsWithLangDefault')->where([['store_id',$key],['parent',0]])->get();
            return response()->json(new JsonResponse($data, ''), Response::HTTP_OK);
        }
    }
}
