<?php
namespace App\Http\Controllers\Api\Admin;

use App\Models\Admin\Discount;
use App\Http\Controllers\Controller;
use App\Models\Front\ShopLanguage;
use App\Models\Front\ShopDiscount;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;
use App\Http\Resources\DiscountCollection;
use Validator;

class DiscountController extends Controller
{
    public $plugin;

    public function __construct()
    {
        // $this->languages = ShopLanguage::getListActive();
        // $this->plugin = new AppConfig;
    }

    public function index()
    {
        // (new ShopDiscount)->install();
        $dataSearch = request()->all();
        $data = (new Discount)->getDiscountListAdmin($dataSearch);
        return DiscountCollection::collection($data)->additional(['message' => 'Successfully']);
    }

/**
 * Store new 
 * @return [type] [description]
 */
    public function store()
    {
        $data = request()->all();
        $validator = Validator::make($data, [
            'code'   => 'required|regex:/(^([0-9A-Za-z\-\._]+)$)/|unique:'.Discount::class.',code|string|max:50',
            'limit'  => 'required|numeric|min:1',
            'reward' => 'required|numeric|min:0',
            'type'   => 'required',
        ], [
            'code.regex' => trans('discount.admin.code_validate'),
            'code.discount_unique' => trans('discount.discount_unique'),
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }
        $dataInsert = [
            'code'       => $data['code'],
            'reward'     => (int)$data['reward'],
            'limit'      => $data['limit'],
            'type'       => $data['type'],
            'data'       => $data['data'],
            'login'      => empty($data['login']) ? 0 : 1,
            'status'     => empty($data['status']) ? 0 : 1,
            'store_id'   => $data['store_id'],
        ];
        if(!empty($data['expires_at'])) {
            $dataInsert['expires_at'] = $data['expires_at'];
        }
        $discounted = Discount::createDiscountAdmin($dataInsert);

        return response()->json(new JsonResponse(['id'=>$discounted->id]), Response::HTTP_OK);
    }

    /**
     * update
     */
    public function update($id)
    {
        $discount = Discount::getDiscountAdmin($id);
        if (!$discount) {
            return response()->json(new JsonResponse([], trans('admin.data_not_found')), Response::HTTP_NOT_FOUND);
        }
        $data = request()->all();
        $validator = Validator::make($data, [
            'code' => 'required|regex:/(^([0-9A-Za-z\-\._]+)$)/|unique:'.Discount::class.',code,'.$id.'|string|max:50',
            'limit' => 'required|numeric|min:1',
            'reward' => 'required|numeric|min:0',
            'type' => 'required',
        ], [
            'code.regex' => trans('discount.admin.code_validate'),
            'code.discount_unique' => trans('discount.discount_unique'),
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }
        //Edit
        $dataUpdate = [
            'code'       => $data['code'],
            'reward'     => (int)$data['reward'],
            'limit'      => $data['limit'],
            'type'       => $data['type'],
            'data'       => $data['data'],
            'login'      => empty($data['login']) ? 0 : 1,
            'status'     => empty($data['status']) ? 0 : 1,
        ];
        if(!empty($data['expires_at'])) {
            $dataUpdate['expires_at'] = $data['expires_at'];
        }
        $discount->update($dataUpdate);

        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }

    /*
    Delete list item
    Need mothod destroy to boot deleting in model
    */
    public function destroy($id)
    {
        $arrID = explode(',', $id);
        Discount::destroy($arrID);
        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }

}
