<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopWeight;
use App\Http\Resources\WeightCollection;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;
use Illuminate\Validation\Rule;
use Validator;

class WeightController extends Controller
{

    /**
    * Index interface.
    *
    * @return Content
    */
    public function index()
    {
        $data = (new ShopWeight)->getWeightListAdmin(request()->all());
        return WeightCollection::collection($data)->additional(['message' => 'Successfully']);
    }

    /**
    * Post create new item in admin
    * @return [type] [description]
    */
    public function store()
    {
        $data = request()->all();
        $validator = Validator::make($data, [
            'name' => [
                'required',
                Rule::unique(ShopWeight::class)->where(function ($query) use ($data) {
                    return $query->where('store_id', $data['store_id'])->where('name',$data['name']);
                })],
            'description' => 'required',
        ], [
            'name.required' => trans('validation.required'),
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }

        $dataInsert = [
            'store_id' => $data['store_id'],
            'name' => $data['name'],
            'description' => $data['description'],
        ];
        $weighUnit = ShopWeight::create($dataInsert);
        return response()->json(new JsonResponse(['id'=>$weighUnit->id]), Response::HTTP_OK);
    }

    /**
    * update status
    */
    public function update($id)
    {
        $data = request()->all();
        $weighUnit = ShopWeight::find($id);
        $validator = Validator::make($data, [
            'name' => 'required|unique:"'.ShopWeight::class.'",name,' . $weighUnit->id . ',id',
            'description' => 'required',
        ], [
            'name.required' => trans('validation.required'),
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }
        if (!$weighUnit) {
            return response()->json(new JsonResponse([], trans('admin.data_not_found')), Response::HTTP_NOT_FOUND);
        }
        $dataUpdate = [
            'store_id' => $data['store_id'],
            'name' => $data['name'],
            'description' => $data['description'],
        ];
        $weighUnit->update($dataUpdate);
        return response()->json(new JsonResponse(), Response::HTTP_OK);

    }

    /*
    Delete list item
    Need mothod destroy to boot deleting in model
    */
    public function destroy($id)
    {   
        $arrID = explode(',', $id);
        ShopWeight::destroy($arrID);
        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }

}
