<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
// use App\Plugins\Other\ProductSale\AppConfig;
use App\Models\Admin\ProductFlashSale;
use App\Models\Front\ShopProductPromotion;
use App\Http\Resources\ProductFlashSaleCollection;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;
use Validator;

class ProductFlashSaleController extends Controller
{
    public function __construct()
    {
        // parent::__construct();
        // $this->plugin = new AppConfig;
    }
    public function index()
    {
        // (new AppConfig)->install();
        $dataSearch = request()->all();
        $data = (new ProductFlashSale)->getAllProductFlashSale($dataSearch);
        return ProductFlashSaleCollection::collection($data)->additional(['message' => 'Successfully']);
    }



    /**
     * Post create new item in admin
     * @return [type] [description]
     */
    public function store()
    {
        $data = request()->all();

        $validator = Validator::make($data, [
            'product_id'      => 'required|unique:"'.ProductFlashSale::class.'",product_id',
            'stock'           => 'required|numeric|min:1',
            'sort'            => 'required|numeric|min:0',
            'price_promotion' => 'required|numeric|min:1',
            'date_start'      => 'required',
            'date_end'        => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }
        $dataInsert = [
            'product_id' => $data['product_id'],
            'stock' => (int)$data['stock'],
            'sort' => (int)$data['sort'],
            'store_id' => (int)$data['store_id'],
        ];

        $pflashsale = ProductFlashSale::create($dataInsert);

        (new ShopProductPromotion)->updateOrCreate(
            ['product_id' => $data['product_id']],
            [
                'price_promotion' => $data['price_promotion'],
                'date_start' => $data['date_start'],
                'date_end' => $data['date_end'],
                'status_promotion' => (!empty($data['status_promotion']) ? 1 : 0),
            ]
        );
        return response()->json(new JsonResponse(['id'=>$pflashsale->id]), Response::HTTP_OK);

    }

    /**
     * update status
     */
    public function update($id)
    {
        $pflashsale = (new ProductFlashSale)->find($id);
        $data = request()->all();

        if (!$pflashsale) {
            return response()->json(new JsonResponse([], trans('admin.data_not_found')), Response::HTTP_NOT_FOUND);
        }
        
        $validator = Validator::make($data, [
            'product_id'      => 'required|unique:"'.ProductFlashSale::class.'",product_id,' . $id . '',
            'stock'           => 'required|numeric|min:1',
            'sort'            => 'required|numeric|min:0',
            'price_promotion' => 'required|numeric|min:1',
            'date_start'      => 'required',
            'date_end'        => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }
        //Edit

        $dataUpdate = [
            'product_id' => $data['product_id'],
            'stock'      => (int)$data['stock'],
            'sort'       => (int)$data['sort'],
        ];

        $pflashsale->update($dataUpdate);

        (new ShopProductPromotion)->where('product_id', $data['product_id'])
        ->update(
            [
                'price_promotion' => $data['price_promotion'],
                'date_start' => $data['date_start'],
                'date_end' => $data['date_end'],
                'status_promotion' => (!empty($data['status_promotion']) ? 1 : 0),
            ]
        );

        return response()->json(new JsonResponse(), Response::HTTP_OK);

    }
    
    /*
    Delete list item
    Need mothod destroy to boot deleting in model
    */
    public function destroy($id)
    {
        $arrID = explode(',', $id);
        ProductFlashSale::destroy($arrID);
        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }

}
