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
        $storeId = request()->header('x-store');
        if (!$storeId) {
            $domain = lc_process_domain_store(basename(request()->headers->get('referer')));
            $arrDomain = ShopStore::getDomainPartner();
            $storeId = array_search($domain, $arrDomain);
        }
    	$data = [];
        $data['store'] = ShopStore::with('descriptionsCurrentLang')->find($storeId);
        $data['languages'] = ShopLanguage::where('store_id',$storeId)->get();
        $data['currencies'] = ShopCurrency::where('store_id',$storeId)->get();
        $data['slider'] = ShopBanner::where([['store_id',$storeId],['type','slider_home'],['status',1]])->get();
        $data['brands'] = ShopBrand::where([['store_id',$storeId],['status',1]])->get();
        $data['banner'] = ShopBanner::where([['store_id',$storeId],['type','banner_home'],['status',1]])->get();
        $data['categories'] = new CategoryCollection(ShopCategory::where([['store_id',$storeId],['parent',0]])->get());
        return response()->json(new JsonResponse($data, ''), Response::HTTP_OK);
    }
}
