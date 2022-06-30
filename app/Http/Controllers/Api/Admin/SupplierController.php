<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopSupplier;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;
use App\Http\Resources\SupplierCollection;
use Validator;

class SupplierController extends Controller
{    
    public function index()
    {
        $data = (new ShopSupplier)->getSupplierListAdmin(request()->all());
        return SupplierCollection::collection($data)->additional(['message' => 'Successfully']);
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
            'image' => 'required',
            'sort' => 'numeric|min:0',
            'name' => 'required|string|max:100',
            'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|unique:"'.ShopSupplier::class.'",alias|string|max:100',
            'url' => 'url|nullable',
            'email' => 'email|nullable',
        ],[
            'name.required' => trans('validation.required', ['attribute' => trans('supplier.name')]),
            'alias.regex' => trans('supplier.alias_validate'),
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }

        $dataInsert = [
            'image'    => is_array($data['image']) ? implode(',',$data['image']) : $data['image'],
            'name' => $data['name'],
            'alias' => $data['alias'],
            'url' => $data['url']??null,
            'email' => $data['email'],
            'address' => $data['address'],
            'phone' => $data['phone'],
            'sort' => (int) $data['sort'],
            'store_id' => (int) $data['store_id'],
            'status' => (int) $data['status'] ?? 0,
        ];
        $supplier = ShopSupplier::create($dataInsert);

        return response()->json(new JsonResponse(['id' => $supplier->id]), Response::HTTP_OK);

    }


    /**
    * update status
    */
    public function update($id)
    {
        $supplier = ShopSupplier::find($id);
        if (!$supplier) {
            return response()->json(new JsonResponse([], trans('admin.data_not_found')), Response::HTTP_NOT_FOUND);
        }
        $data = request()->all();

        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['name'];
        $data['alias'] = lc_word_format_url($data['alias']);
        $data['alias'] = lc_word_limit($data['alias'], 100);

        $validator = Validator::make($data, [
            'store_id' => 'required',
            'image' => 'required',
            'sort' => 'numeric|min:0',
            'name' => 'required|string|max:100',
            'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|unique:"'.ShopSupplier::class.'",alias,' . $supplier->id . ',id|string|max:100',
            'url' => 'url|nullable',
            'email' => 'email|nullable',
        ],[
            'name.required' => trans('validation.required', ['attribute' => trans('supplier.name')]),
            'alias.regex' => trans('supplier.alias_validate'),
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }

        $dataUpdate = [
            'image'    => is_array($data['image']) ? implode(',',$data['image']) : $data['image'],
            'name' => $data['name'],
            'alias' => $data['alias'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'url' => $data['url']??null,
            'address' => $data['address'],
            'sort' => (int) $data['sort'],
            'store_id' => (int) $data['store_id'],
            'status' => (int) $data['status'] ?? 0,

        ];
        
        $supplier->update($dataUpdate);

        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }

    /*
    Delete list item
    Need mothod destroy to boot deleting in model
    */
    public function destroy($id)
    {
        $arrID = explode(',', $id);
        ShopSupplier::destroy($arrID);
    }

}
