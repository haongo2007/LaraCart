<?php
namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopStore;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;
use App\Models\Front\ShopLanguage;
use App\Models\Admin\Config;
use App\Models\Front\ShopCurrency;
use App\Models\Front\ShopCategory;
use App\Models\Front\ShopBanner;
use App\Models\Front\ShopBrand;
use App\Http\Resources\Front\CategoryCollection;

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
            $data['slider'] = ShopBanner::where([['store_id',$key],['type','slider_home'],['status',1]])->get();
            $data['brands'] = ShopBrand::where([['store_id',$key],['status',1]])->get();
            $data['banner'] = ShopBanner::where([['store_id',$key],['type','banner_home'],['status',1]])->get();
            $data['categories'] = new CategoryCollection(ShopCategory::where([['store_id',$key],['parent',0]])->get());
            $data['config'] = Config::where('store_id',$key)->get();
            return response()->json(new JsonResponse($data, ''), Response::HTTP_OK);
        }
    }
}
