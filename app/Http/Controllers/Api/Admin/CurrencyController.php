<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopCurrency;
use Validator;
use App\Helper\JsonResponse;
use Illuminate\Http\Response;
use App\Http\Resources\CurrencyCollection;

class CurrencyController extends Controller
{
    public function __construct()
    {
    }
    public function index()
    {
        $searchParams = request()->all();
        $data = (new ShopCurrency)->getCurrencyListAdmin($searchParams);
        if ($data->total() == 0) {
            $data = lc_currency_default();
        }
        return CurrencyCollection::collection($data)->additional(['message' => 'Successfully']);
    }

    /**
    * Post create new item in admin
    * @return [type] [description]
    */
    public function store()
    {
        $data = request()->all();
        $validator = Validator::make($data, [
            'symbol' => 'required',
            'exchange_rate' => 'required|numeric|gt:0',
            'precision' => 'required',
            'symbol_first' => 'required',
            'thousands' => 'required',
            'sort' => 'numeric|min:0',
            'name' => 'required|string|max:100',
            'code' => 'required|unique:"'.ShopCurrency::class.'",code',
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }

        $dataInsert = [
            'name' => $data['name'],
            'code' => $data['code'],
            'symbol' => $data['symbol'],
            'exchange_rate' => $data['exchange_rate'],
            'precision' => $data['precision'],
            'symbol_first' => $data['symbol_first'],
            'thousands' => $data['thousands'],
            'status' => empty($data['status']) ? 0 : 1,
            'sort' => (int) $data['sort'],
        ];
        $currency = ShopCurrency::create($dataInsert);

        return response()->json(new JsonResponse(['id'=>$currency->id]), Response::HTTP_OK);
    }

    /**
    * update status
    */
    public function update($id)
    {
        $currency = ShopCurrency::find($id);
        if (!$currency) {
            return response()->json(new JsonResponse([], trans('admin.data_not_found')), Response::HTTP_NOT_FOUND);
        }
        $data = request()->all();
        $validator = Validator::make($data, [
            'symbol' => 'required',
            'exchange_rate' => 'required|numeric|gt:0',
            'precision' => 'required',
            'symbol_first' => 'required',
            'thousands' => 'required',
            'sort' => 'numeric|min:0',
            'name' => 'required|string|max:100',
            'code' => 'required|unique:"'.ShopCurrency::class.'",code,' . $currency->id . ',id',
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }

        $dataUpdate = [
            'name' => $data['name'],
            'code' => $data['code'],
            'symbol' => $data['symbol'],
            'exchange_rate' => $data['exchange_rate'],
            'precision' => $data['precision'],
            'symbol_first' => $data['symbol_first'],
            'thousands' => $data['thousands'],
            'sort' => (int) $data['sort'],
            'status' => empty($data['status']) ? 0 : 1,

        ];

        $currency->update($dataUpdate);

        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }
    /*
    Delete list item
    Need mothod destroy to boot deleting in model
    */
    public function destroy($id)
    {
        $arrID = explode(',', $id);
        $arrID = array_diff($arrID, LC_GUARD_CURRENCY);
        ShopCurrency::destroy($arrID);
        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }

}
