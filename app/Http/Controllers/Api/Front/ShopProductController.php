<?php
namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopProduct;
use App\Models\Front\ShopCategory;
use App\Models\Front\ShopBrand;
use App\Models\Front\ShopProductDescription;
use App\Models\Front\ShopAttributeGroup;
use App\Models\Front\ShopProductAttribute;
use App\Models\Front\ShopProductFlashSale;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;
use App\Http\Resources\Front\ProductCollection;
use App\Http\Resources\Front\ProductRelatedCollection;
use App\Http\Resources\Front\ProductFlashSaleCollection;
use Cache;

class ShopProductController extends Controller
{
    public function __construct()
    {
        // parent::__construct();
    }
    
    /**
     * All products
     * @return [view]
     */
    public function index(Request $request)
    {
        $storeId = $request->header('x-store');
        $params = $request->all();
        $params['storeId'] = $storeId;
        $products = (new ShopProduct)->getProductList($params);
        return ProductCollection::collection($products);
    }

    /**
     * Get product detail
     *
     * @param   [string]  $alias      [$alias description]
     * @param   [string]  $type     [$type id or alias or sku]
     * @param   [string]  $storeId  [$storeCode description]
     *
     * @return  [mix]
     */
    public function show(Request $request,$alias) 
    {
        $storeId = $request->header('x-store');
        $product = (new ShopProduct)->getDetail($alias, 'alias', $storeId);
        //Update last view
        $product->view += 1;
        $product->date_lastview = date('Y-m-d H:i:s');
        $product->save();
        //End last viewed
        
        //Product relation by categories
        $productRelation = (new ShopProduct)->setStore($storeId);

        $prev_product = $productRelation->getData()->where('id', '<', $product->id)->first();
        if ($prev_product) {
            $prev_product = new ProductCollection($prev_product);
        }

        $next_product = $productRelation->getData()->where('id', '>', $product->id)->first();
        if ($next_product) {
            $next_product = new ProductCollection($next_product);
        }

        $categories = $product->categories->keyBy('id')->toArray();
        $arrCategoriId = array_keys($categories);
        
        $productRelation = $productRelation
        ->getProductToCategory($arrCategoriId)
        ->setLimit(lc_config('product_relation', $storeId))
        ->setRandom()
        ->getData()
        ->where('id','<>',$product->id);
        $data['prevProduct'] = $prev_product;
        $data['nextProduct'] = $next_product;
        $data['product'] = new ProductCollection($product);
        $data['relatedProducts'] = new ProductRelatedCollection($productRelation);
        return response()->json(new JsonResponse($data), Response::HTTP_OK);
    }

    /**
     * Get product special
     *
     * @return  [mix]
     */
    public function special(Request $request) 
    {
        $storeId = $request->header('x-store');
        $flash_sale = $request->flash_sale ?? false;
        $most_buy = $request->most_buy ?? false;
        $most_view = $request->most_view ?? false;
        $sale = $request->sale ?? false;
        $top = $request->top ?? false;
        $top_rated = $request->top_rated ?? false;

        $products = (new ShopProduct);

        $data = [];
        if ($flash_sale) {
            $product_flash = (new ShopProductFlashSale)->getAllProductFlashSale(['storeId'=>$storeId]);
            $data['flashSaleProducts'] = ProductFlashSaleCollection::collection($product_flash);
        }

        if ($top) {
            $data['topProducts'] = [];
        }
        return response()->json(new JsonResponse($data), Response::HTTP_OK);
    }
}
