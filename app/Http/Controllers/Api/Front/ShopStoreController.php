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
use App\Models\Front\ShopNews;
use App\Models\Front\ShopAttributeGroup;
use App\Http\Resources\Front\CategoryCollection;
use App\Models\Front\ShopProductFlashSale;
use Illuminate\Http\Request;
use App\Http\Resources\Front\ProductCollection;
use App\Http\Resources\Front\ProductFlashSaleCollection;
use App\Http\Resources\Front\BlogsCollection;
use Cache;

class ShopStoreController extends Controller
{   

    /**
     * Get info homepage cache
     *
     * @return  [mix]
     */
    public function index(Request $request) 
    {
        $storeId = $request->header('x-store');
        $flash_sale = $request->flash_sale ?? false;
        $sale = $request->sale ?? false;
        $top = $request->top ?? false;
        $top_rated = $request->top_rated ?? false;
        $most_view = $request->most_view ?? false;
        $most_buy = $request->most_buy ?? false;
        $latest = $request->latest ?? false;

        $instance = (new ShopProduct)->setStore($storeId);
        $data = [];
        if (!Cache::has($storeId.'_cache_product_special_'.lc_get_locale())) {
            if ($flash_sale) {
                $flashSaleProducts = (new ShopProductFlashSale)->getAllProductFlashSale(['store_id'=>$storeId]);
                $data['flashSaleProducts'] = ProductFlashSaleCollection::collection($flashSaleProducts);
            }
            if ($latest) {
                $latestProducts = $instance->setLimit(lc_config('product_latest',$storeId))->getData();
                $data['latestProducts'] = ProductCollection::collection($latestProducts);
            }

            if ($sale) {
                $saleProducts = $instance->getProductPromotion(1)->setLimit(lc_config('product_sale',$storeId))->getData();
                $data['saleProducts'] = ProductCollection::collection($saleProducts);
            }

            if ($top) {
                $topProducts = $instance->getProductPromotion(0)->getProductTop(1)->setLimit(lc_config('product_top',$storeId))->getData();
                $data['topProducts'] = ProductCollection::collection($topProducts);
            }

            if ($top_rated) {
                $topRatedProducts = $instance->getProductPromotion(0)
                                             ->getProductTop(0)
                                             ->getProductTopRated(1)
                                             ->setLimit(lc_config('product_top_rated',$storeId))->getData();
                $data['topRatedProducts'] = ProductCollection::collection($topRatedProducts);
            }

            if ($most_view) {
                $mostViewProducts = $instance->getProductPromotion(0)
                                              ->getProductTop(0)
                                              ->getProductMostView(1)
                                              ->setLimit(lc_config('product_most_view',$storeId))->getData();
                $data['mostViewProducts'] = ProductCollection::collection($mostViewProducts);
            }

            if ($most_buy) {
                $mostBuyProducts = $instance->getProductPromotion(0)
                                              ->getProductTop(0)
                                              ->getProductMostView(0)
                                              ->getProductMostBuy(1)
                                              ->setLimit(lc_config('product_most_view',$storeId))->getData();
                $data['mostBuyProducts'] = ProductCollection::collection($mostBuyProducts);
            }

            $blogs  = (new ShopNews)->setStore($storeId)
                                    ->setLimit(lc_config('news_list'))
                                    ->setSort(['id', 'desc'])
                                    ->getData();
            $data['blogs'] = BlogsCollection::collection($blogs);

            lc_set_cache($storeId.'_cache_product_special_'.lc_get_locale(), $data,$storeId);
        }
        $data = Cache::get($storeId.'_cache_product_special_'.lc_get_locale());
        return response()->json(new JsonResponse($data), Response::HTTP_OK);
    }

    /**
     * Get info store init store
     *
     * @return  [infoStore]
     */
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
        $brands = ShopBrand::where([['store_id',$storeId],['status',1]])->get();
        $banner = ShopBanner::where([['store_id',$storeId],['status',1]])->get();
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

        $data['display']['banner']          = $banner;
        $data['display']['product']         = Config::getListConfigByCode(['storeId' => $storeId,'code' => 'display_config','keyBy' => 'key']);

        return response()->json(new JsonResponse($data, ''), Response::HTTP_OK);
    }
}
