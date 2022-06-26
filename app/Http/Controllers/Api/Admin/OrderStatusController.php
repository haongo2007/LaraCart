<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopOrderStatus;
use App\Helper\JsonResponse;
use Illuminate\Http\Response;
use App\Http\Resources\OrderStatusCollection;
use Validator;

class OrderStatusController extends Controller
{

    public function __construct()
    {

    }
    
    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        $data = new ShopOrderStatus;
        $data = $data->orderBy('id', 'desc');
        $data = $data->paginate(20);
        return OrderStatusCollection::collection($data)->additional(['message' => 'Successfully']);
    }


    /**
    * Post create new item in admin
    * @return [type] [description]
    */
    public function store()
    {
        $data = request()->all();
        $validator = Validator::make($data, [
            'name' => 'required',
        ], [
            'name.required' => trans('validation.required'),
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }
        $dataInsert = [
            'name' => $data['name'],
            'label' => $data['label'],
            'store_id' => $data['store_id'],
        ];
        $orderStt = ShopOrderStatus::create($dataInsert);
        return response()->json(new JsonResponse(['id'=>$orderStt->id]), Response::HTTP_OK);
    }

    /**
    * update status
    */
    public function update($id)
    {
        $data = request()->all();
        $validator = Validator::make($data, [
            'name' => 'required',
        ], [
            'name.required' => trans('validation.required'),
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }
        $orderStt = ShopOrderStatus::find($id);
        if (!$orderStt) {
            return response()->json(new JsonResponse([], trans('admin.data_not_found')), Response::HTTP_NOT_FOUND);
        }
        $dataUpdate = [
            'name' => $data['name'],
            'label' => $data['label'],
            'store_id' => $data['store_id'],
        ];
        $orderStt->update($dataUpdate);
        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }

    /*
    Delete list item
    Need mothod destroy to boot deleting in model
    */
    public function destroy($id)
    {
        $arrID = explode(',', $id);
        ShopOrderStatus::destroy($arrID);
        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }

}
