<?php
namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopProduct;
use App\Models\Front\ShopProductDescription;
use App\Models\Front\ShopAttributeGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;
use App\Http\Resources\Front\ProductCollection;
use App\Http\Resources\Front\ProductRelatedCollection;
use Cache;

class ShopProductController extends Controller
{
    public function __construct()
    {
        // parent::__construct();
    }
    
    /**
     * Process front all products
     *
     * @param [type] ...$params
     * @return void
     */
    public function allProductsProcessFront(...$params) 
    {
        if (config('app.seoLang')) {
            $lang = $params[0] ?? '';
            bc_lang_switch($lang);
        }
        return $this->_allProducts();
    }

    /**
     * All products
     * @return [view]
     */
    private function index()
    {
        $sortBy = 'sort';
        $sortOrder = 'asc';
        $filter_sort = request('filter_sort') ?? 'id_desc';
        $filter_price = request('filter_price') ?? '';
        $filter_keyword = request('filter_keyword') ?? '';
        $filter_attribute = request('filter_attribute') ?? '';
        $filter_category = request('categoryId') ?? '';
        
        $filterArrSort = [
            'price_desc' => ['price', 'desc'],
            'price_asc' => ['price', 'asc'],
            'sort_desc' => ['sort', 'desc'],
            'sort_asc' => ['sort', 'asc'],
            'id_desc' => ['id', 'desc'],
            'id_asc' => ['id', 'asc'],
        ];
        $products = (new ShopProduct);
        $price_max = $products->max('price');
        $filterArrPrice = [0,$price_max];

        if (array_key_exists($filter_sort, $filterArrSort)) {
            $sortBy = $filterArrSort[$filter_sort][0];
            $sortOrder = $filterArrSort[$filter_sort][1];
        }  
        if ($filter_price) {
            $filter_price = explode('-', request('filter_price'));
            $price_min = bc_convert_price_to_origin($filter_price[0]);
            $price_max = bc_convert_price_to_origin($filter_price[1]);
            $products = $products->setPriceBetween($price_min,$price_max);
        }  
        if ($filter_keyword) {
            $products = $products->setKeyword($filter_keyword);
        }
        if ($filter_keyword) {
            $products = $products->setKeyword($filter_keyword);
        }
        if ($filter_attribute) {
            $products = $products->setAttributes($filter_attribute);
        }        
        $products = $products
            ->setLimit(bc_config('product_list'))
            ->setPaginate()
            ->setSort([$sortBy, $sortOrder])
            ->getData();
    
        $filterArrKeyword = [];
        if (Cache::has(session('adminStoreId').'_cache_keyword_products_'.bc_get_locale())){
            $filterArrKeyword = Cache::get(session('adminStoreId').'_cache_keyword_products_'.bc_get_locale());
        }else{
            $ArrKeyword = ShopProductDescription::select('keyword')->where('lang', bc_get_locale())->get()->pluck('keyword')->toArray();
            $ArrKeyword = array_unique($ArrKeyword);
            foreach ($ArrKeyword as $value) {
                $keys = explode(',', $value);
                foreach ($keys as $key) {
                    if (!in_array($key,$filterArrKeyword)) {
                        array_push($filterArrKeyword, $key);
                    }
                }
            }
            bc_set_cache(session('adminStoreId').'_cache_keyword_products_'.bc_get_locale(), $filterArrKeyword);
        }

        $filterArrAttribute = [];
        if (Cache::has(session('adminStoreId').'_cache_attributes_products_'.bc_get_locale())){
            $filterArrAttribute = Cache::get(session('adminStoreId').'_cache_attributes_products_'.bc_get_locale());
        }else{
            $allAttributes = ShopAttributeGroup::with('attributeDetails')->where('picker',1)->get()->pluck('attributeDetails')[0]->pluck('code')->toArray();
            foreach (array_filter($allAttributes) as $key => $att) {
                foreach (explode(',', $att) as $key => $vatt) {
                    $filterArrAttribute[] = $vatt;
                }
            }
            $filterArrAttribute = array_unique($filterArrAttribute);
        }
        
        if(request()->ajax()){
            bc_check_view($this->templatePath . '.Common.isotope_grid');
            return view($this->templatePath . '.Common.isotope_grid',['products'=> $products])->render();   
        }

        bc_check_view($this->templatePath . '.Shop.index');
        return view(
            $this->templatePath . '.Shop.index',
            array(
                'title'            => trans('front.all_product'),
                'keyword'          => '',
                'description'      => '',
                'products'         => $products,
                'layout_page'      => 'shop_product_list',
                'filterArrSort'    => $filterArrSort,
                'filter_sort'      => $filter_sort,
                'filterArrPrice'   => $filterArrPrice,
                'filter_price'     => $filter_price ? implode('-', $filter_price) : '',
                'filterArrKeyword' => $filterArrKeyword,
                'filter_keyword'   => $filter_keyword,
                'filterArrAttribute' => $filterArrAttribute,
                'filter_attribute'   => $filter_attribute,
            )
        );
    }

    /**
     * Process front product detail
     *
     * @param [type] ...$params
     * @return void
     */
    public function show(Request $request,$alias) 
    {
        if (config('app.seoLang')) {
            $lang = $request->header('x-localization');
            $store = $request->header('x-store') ?? 10;
            $alias = $alias ?? '';
            lc_lang_switch($lang);
        } else {
            $store = $request->header('x-store') ?? 10;
            $alias = $alias ?? '';
        }
        return $this->_productDetail($alias,'alias',$store);
    }

    /**
     * Get product detail
     *
     * @param   [string]  $alias      [$alias description]
     * @param   [string]  $type     [$type id or alias or sku]
     * @param   [string]  $storeId  [$storeCode description]
     * @param   [string]  $view  [$view check detail view or quick view]
     *
     * @return  [mix]
     */
    private function _productDetail($alias,$type, $storeId,$view = '.Product.detail')
    {
        $product = (new ShopProduct)->getDetail($alias, $type, $storeId);
        if ($product && $product->status && (!lc_config('product_stock', $storeId) || lc_config('product_display_out_of_stock', $storeId) || $product->stock > 0)) {
            //Update last view
            $product->view += 1;
            $product->date_lastview = date('Y-m-d H:i:s');
            $product->save();
            //End last viewed

            //Product relation by categories
            $categories = $product->categories->keyBy('id')->toArray();
            $arrCategoriId = array_keys($categories);
            $productRelation = (new ShopProduct)
                ->setStore($storeId)
                ->getProductToCategory($arrCategoriId)
                ->setLimit(lc_config('product_relation', $storeId))
                ->setRandom()
                ->getData()
                ->where('id','<>',$product->id);
            //End Product relation by categories

            //Product last view
                // $arrlastView = empty(\Cookie::get('productsLastView')) ? array() : json_decode(\Cookie::get('productsLastView'), true);
                // $arrlastView[$product->id] = date('Y-m-d H:i:s');
                // arsort($arrlastView);
                // \Cookie::queue('productsLastView', json_encode($arrlastView), (86400 * 30));
            //End product last view
            $prev_product = (new ShopProduct)->setStore($storeId)->getData()->where('id', '<', $product->id)->first();
            if ($prev_product) {
                $prev_product = new ProductCollection($prev_product);
            }

            $next_product = (new ShopProduct)->setStore($storeId)->getData()->where('id', '>', $product->id)->first();
            if ($next_product) {
                $next_product = new ProductCollection($next_product);
            }
            
            $data['prevProduct'] = $prev_product;
            $data['nextProduct'] = $next_product;
            $data['product'] = new ProductCollection($product);
            $data['relatedProducts'] = new ProductRelatedCollection($productRelation);
            return response()->json(new JsonResponse($data), Response::HTTP_OK);
        } else {
            return response()->json(new JsonResponse([]), Response::HTTP_OK);
        }
    }
    public function productDetailQuickViewProcess(Request $request)
    {
        return $this->_productDetail($request->id,'', $request->storeId,'.Common.product_detail')->render();
    }
}
