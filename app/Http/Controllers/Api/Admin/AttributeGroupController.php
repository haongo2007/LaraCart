<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopAttributeGroup;
use App\Http\Resources\AttributeGroupCollection;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;
use Validator;

class AttributeGroupController extends Controller
{
    /**
    * Index interface.
    *
    * @return Content
    */
    public function index()
    {
        $data = ShopAttributeGroup::whereIn('store_id', session('adminStoreId'))->paginate(20);
        return AttributeGroupCollection::collection($data)->additional(['message' => 'Successfully']);
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
            'type' => 'required',
        ], [
            'name.required' => trans('validation.required'),
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }
        $dataInsert = [
            'name'      => $data['name'],
            'type'      => $data['type'],
            'status'    => !empty($data['status']) ? 1 : 0,
            'store_id'  => $data['store_id'],
        ];
        $attGroup = ShopAttributeGroup::create($dataInsert);

        return response()->json(new JsonResponse(['id' => $attGroup->id]), Response::HTTP_OK);

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
        $attrGroup = ShopAttributeGroup::find($id);
        if (!$attrGroup) {
            return response()->json(new JsonResponse([], trans('admin.data_not_found')), Response::HTTP_NOT_FOUND);
        }
        $dataUpdate = [
            'name'      => $data['name'],
            'type'      => $data['type'],
            'status'    => !empty($data['status']) ? 1 : 0,
            'store_id'  => $data['store_id'],
        ];
        $attrGroup->update($dataUpdate);
        return response()->json(new JsonResponse(), Response::HTTP_OK);

    }

    /*
    Delete list item
    Need mothod destroy to boot deleting in model
    */
    public function destroy($id)
    {
        $arrID = explode(',', $id);
        ShopAttributeGroup::destroy($arrID);
        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }

}
