<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopTax;
use App\Http\Resources\TaxCollection;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;
use Validator;

class TaxController extends Controller
{
    public function index()
    {
        $data = (new ShopTax)->getTaxListAdmin(request()->all());
        return TaxCollection::collection($data)->additional(['message' => 'Successfully']);
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
            'value' => 'numeric|min:0',
        ],[
            'name.required' => trans('validation.required', ['attribute' => trans('tax.name')]),
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }

        $dataInsert = [
            'value' => (int)$data['value'],
            'name' => $data['name'],
            'store_id' => $data['store_id'],
        ];
        $tax = ShopTax::create($dataInsert);

        return response()->json(new JsonResponse(['id'=>$tax->id]), Response::HTTP_OK);

    }


/**
 * update status
 */
    public function update($id)
    {
        $tax = ShopTax::find($id);
        $data = request()->all();
        $validator = Validator::make($data, [
            'name' => 'required',
            'value' => 'numeric|min:0',
        ],[
            'name.required' => trans('validation.required', ['attribute' => trans('tax.name')]),
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }

        $dataUpdate = [
            'value' => (int)$data['value'],
            'name' => $data['name'],
        ];
        
        $tax->update($dataUpdate);
        return response()->json(new JsonResponse(), Response::HTTP_OK);

    }

/*
Delete list item
Need mothod destroy to boot deleting in model
 */
    public function destroy($id)
    {
        $arrID = explode(',', $id);
        ShopTax::destroy($arrID);
        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }

}
