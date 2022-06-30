<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopBrand;
use App\Http\Resources\BrandCollection;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;
use Validator;

class BrandController extends Controller
{
    public function index()
    {
        $data = (new ShopBrand)->getBrandListAdmin(request()->all());
        return BrandCollection::collection($data)->additional(['message' => 'Successfully']);
    }


    /**
    * Post create new item in admin
    * @return [type] [description]
    */
    public function store()
    {
        $data = request()->all();

        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['name'];
        $data['alias'] = lc_word_format_url($data['alias']);
        $data['alias'] = lc_word_limit($data['alias'], 100);

        $validator = Validator::make($data, [
            'store_id' => 'required',
            'name' => 'required|string|max:100',
            'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|unique:"'.ShopBrand::class.'",alias|string|max:100',
            'image' => 'required',
            'sort' => 'numeric|min:0',
            'url' => 'url|nullable',
        ],[
            'name.required' => trans('validation.required', ['attribute' => trans('brand.name')]),
            'alias.regex' => trans('brand.alias_validate'),
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }
        $dataInsert = [
            'image'    => is_array($data['image']) ? implode(',',$data['image']) : $data['image'],
            'name' => $data['name'],
            'alias' => $data['alias'],
            'url' => $data['url'],
            'sort' => (int) $data['sort'],
            'store_id' => (int) $data['store_id'],
            'status' => (!empty($data['status']) ? 1 : 0),
        ];
        $brand = ShopBrand::create($dataInsert);

        return response()->json(new JsonResponse(['id' => $brand->id]), Response::HTTP_OK);
    }

    /**
    * update status
    */
    public function update($id)
    {
        $brand = ShopBrand::find($id);
        if (!$brand) {
            return response()->json(new JsonResponse([], trans('admin.data_not_found')), Response::HTTP_NOT_FOUND);
        }
        $data = request()->all();
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['name'];
        $data['alias'] = lc_word_format_url($data['alias']);
        $data['alias'] = lc_word_limit($data['alias'], 100);

        $validator = Validator::make($data, [
            'store_id' => 'required',
            'name' => 'required|string|max:100',
            'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|unique:"'.ShopBrand::class.'",alias,' . $brand->id . ',id|string|max:100',
            'image' => 'required',
            'sort' => 'numeric|min:0',
        ], [
            'name.required' => trans('validation.required', ['attribute' => trans('brand.name')]),
            'alias.regex' => trans('brand.alias_validate'),
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }

        $dataUpdate = [
            'image'    => is_array($data['image']) ? implode(',',$data['image']) : $data['image'],
            'name' => $data['name'],
            'alias' => $data['alias'],
            'store_id' => (int) $data['store_id'],
            'url' => $data['url'] ?? null,
            'sort' => (int) $data['sort'],
            'status' => (!empty($data['status']) ? 1 : 0),

        ];

        $brand->update($dataUpdate);
        return response()->json(new JsonResponse(), Response::HTTP_OK);

    }

    /*
    Delete list item
    Need mothod destroy to boot deleting in model
    */
    public function destroy($id)
    {
        $arrID = explode(',', $id);
        ShopBrand::destroy($arrID);
        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }

}
