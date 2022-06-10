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
    	$data = [];
        $data['store'] = ShopStore::with('descriptionsCurrentLang')->find($storeId);
        $shippingMethod = [];
        $paymentMethod = [];
        if (!lc_config('shipping_off',$storeId)) {
            $where = ['code' => 'Shipping','storeId' => $storeId];
            $moduleShipping = Config::getListConfigByCode($where);
            $sourcesShipping = lc_get_all_plugin('shipping');
            foreach ($moduleShipping as $module) {
                if (array_key_exists($module['key'], $sourcesShipping)) {
                    $moduleClass = lc_get_class_plugin_config('shipping', $module['key']);
                    $shippingMethod[$module['key']] = (new $moduleClass)->getData();
                }
            }
        }
        if (!lc_config('payment_off',$storeId)) {
            $where = ['code' => 'Payment','storeId' => $storeId];
            $modulePayment = Config::getListConfigByCode($where);
            $sourcesPayment = lc_get_all_plugin('payment');
            foreach ($modulePayment as $module) {
                if (array_key_exists($module['key'], $sourcesPayment)) {
                    $moduleClass = $sourcesPayment[$module['key']].'\AppConfig';
                    $paymentMethod[$module['key']] = (new $moduleClass)->getData();
                }
            }   
        }
        $data['store']['shipping_method'] = $shippingMethod;
        $data['store']['payment_method'] = $paymentMethod;
        $data['languages'] = ShopLanguage::getListActive($storeId);
        $data['currencies'] = ShopCurrency::getListActive($storeId);
        $data['slider'] = ShopBanner::where([['store_id',$storeId],['type','slider_home'],['status',1]])->get();
        $data['brands'] = ShopBrand::where([['store_id',$storeId],['status',1]])->get();
        $data['banner'] = ShopBanner::where([['store_id',$storeId],['type','banner_home'],['status',1]])->get();
        $data['categories'] = new CategoryCollection(ShopCategory::where([['store_id',$storeId],['parent',0]])->get());
        return response()->json(new JsonResponse($data, ''), Response::HTTP_OK);
    }
}
