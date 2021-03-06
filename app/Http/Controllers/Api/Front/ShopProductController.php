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
use App\Models\Front\ShopRating;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Helper\JsonResponse;
use App\Http\Resources\Front\ProductCollection;
use App\Http\Resources\Front\ProductRelatedCollection;
use App\Http\Resources\Front\ProductFlashSaleCollection;
use Cache;

class ShopProductController extends Controller
{
    /**
     * All products
     * @return [view]
     */
    public function index(Request $request)
    {
        $storeId = $request->header('x-store');
        $params = array_filter($request->all());
        $params['storeId'] = $storeId;
        $products = (new ShopProduct)->getProductList($params);
        $data = ProductCollection::collection($products);
        return $data;
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
        $instance = (new ShopProduct);
        $product = $instance->getDetail($alias, 'alias', $storeId);
        //Update last view
        $product->view += 1;
        $product->date_lastview = date('Y-m-d H:i:s');
        $product->save();
        //End last viewed
        
        //Product relation by categories
        $productRelation = $instance->setStore($storeId);

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
    public function rating(Request $request) 
    {
        $storeId = $request->header('x-store');
        if (!$request->user()) {
            return response()->json(new JsonResponse([],'Login is require'), Response::HTTP_FORBIDDEN);
        }
        $data = request()->all();
        $validate = [
            'product_id' => 'required',
            'comment' => 'required|string|max:300|min:10',
            'point' => 'required|numeric|min:1|max:5',
        ];
        if(lc_captcha_method() && in_array('checkout', lc_captcha_page())) {
            $data['captcha_field'] = $data[lc_captcha_method()->getField()] ?? '';
            $validate['captcha_field'] = ['required', 'string', new \App\Plugins\Other\GoogleCaptcha\Rules\CaptchaRule];
        }
        $validator = Validator::make($data, $validate);
        
        if ($validator->fails()) {
            return response()->json(new JsonResponse([],$validator->errors()), Response::HTTP_FORBIDDEN);
        }
        $product = ShopProduct::find($data['product_id']);
        if (!$product) {
            return response()->json(new JsonResponse([],'Product not found'), Response::HTTP_NOT_FOUND);
        }
        $rate_point = ShopRating::getPointData($product->id);
        $product->rate_point = (int) ($rate_point ? $rate_point['total'] + $data['point'] : $data['point']);
        $product->rate_count = (int) ($rate_point ? $rate_point['ct'] + 1 : 1);
        $product->save();

        $dataInsert = [
            'id' => lc_uuid(),
            'product_id' => $data['product_id'],
            'customer_id' => $request->user()->id,
            'name' => $request->user()->first_name.' '.$request->user()->last_name,
            'comment' => strip_tags(str_replace("\n", "<br>", $data['comment']), '<br>'),
            'point' => min((int)$data['point'], 5),
            'status' => lc_config_global('ProductReview',$storeId),
        ];

        $dataInsert = lc_clean($dataInsert);
        ShopRating::create($dataInsert);
        return response()->json(new JsonResponse([]), Response::HTTP_OK);
    }
}
