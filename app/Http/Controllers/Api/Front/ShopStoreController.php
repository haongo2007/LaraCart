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
use App\Models\Front\ShopProduct;
use App\Models\Front\ShopAttributeGroup;
use App\Http\Resources\Front\CategoryCollection;

class ShopStoreController extends Controller
{   
    public function getInfo()
    {
        $storeId = request()->header('x-store');
    	$data = [];
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
        $maximumPrice = ShopProduct::where('store_id',$storeId)->max('price');

        $attributes = ShopAttributeGroup::with(['attributeDetails' => function ($query=''){   
            $query->with('activePalette');
            $query->groupBy('name');
        }])->where('store_id',$storeId)->get();

        $languages = ShopLanguage::getListActive($storeId);
        $currencies = ShopCurrency::getListActive($storeId);
        $slider = ShopBanner::where([['store_id',$storeId],['type','slider_home'],['status',1]])->get();
        $brands = ShopBrand::where([['store_id',$storeId],['status',1]])->get();
        $banner = ShopBanner::where([['store_id',$storeId],['type','banner_home'],['status',1]])->get();
        $categories = new CategoryCollection(ShopCategory::where([['store_id',$storeId],['parent',0]])->get());
        $info = ShopStore::with('descriptionsCurrentLang')->find($storeId);

        $data['shop']['info']               = $info;
        $data['shop']['attributes']         = $attributes;
        $data['shop']['max_price_product']  = $maximumPrice;
        $data['shop']['categories']         = $categories;
        $data['shop']['brands']             = $brands;

        $data['localized']['currencies']    = $currencies;
        $data['localized']['languages']     = $languages;

        $data['method']['shipping']         = $shippingMethod;
        $data['method']['payment']          = $paymentMethod;

        $data['display']['slider']          = $slider;
        $data['display']['banner']          = $banner;
        $data['display']['product']         = Config::getListConfigByCode(['storeId' => $storeId,'code' => 'display_config','keyBy' => 'key']);

        return response()->json(new JsonResponse($data, ''), Response::HTTP_OK);
    }
}
