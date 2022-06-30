<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;
use App\Models\Front\ShopCountry;
use App\Models\Front\ShopLanguage;
use App\Models\Admin\Customer;
use App\Models\Front\ShopCustomField;
use App\Models\Front\ShopCustomFieldDetail;
use App\Http\Controllers\Api\Front\Auth\AuthTrait;
use App\Http\Resources\CustomerCollection;
use Carbon\Carbon;
use Validator;

class CustomerController extends Controller
{
    use AuthTrait;

    public function index()
    {
        $searchParams = request()->all();
        $data = (new Customer)->getCustomerListAdmin($searchParams);
        return CustomerCollection::collection($data)->additional(['message' => 'Successfully']);
    }

    /**
    * Form show new item in admin
    * @return [type] [description]
    */
    public function show($id)
    {
        $data = [
            'customFields'         => (new ShopCustomField)->getCustomField($type = 'customer'),
            'customer'          => (new Customer)->getCustomerAdmin($id),
        ];

        return response()->json(new JsonResponse($data), Response::HTTP_OK);
    }

    /**
    * Post create new item in admin
    * @return [type] [description]
    */
    public function store()
    {
        $data = request()->all(); 
        $data['status'] = empty($data['status']) ? 0 : 1;
        $dataMapping = $this->mappingValidator($data);
        $validator =  Validator::make($data, $dataMapping['validate'], $dataMapping['messages']);
        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }
        $customer = Customer::createCustomer($dataMapping['dataInsert']);

        //Insert custom fields
        if (!empty($data['fields'])) {
            $dataField = [];
            foreach ($data['fields'] as $key => $value) {
                $field = (new ShopCustomField)->where('code', $key)->where('type', 'customer')->first();
                if ($field) {
                    $dataField[] = [
                        'custom_field_id' => $field->id,
                        'rel_id' => $customer->id,
                        'text' => trim($value),
                    ];
                }
            }
            if ($dataField) {
                (new ShopCustomFieldDetail)->insert($dataField);
            }
        }
        return response()->json(new JsonResponse([]), Response::HTTP_OK);
    }
    /**
     * update status
     */
    public function update($id)
    {
        $data = request()->all();
        $customer = (new Customer)->getCustomerAdmin($id);
        if (!$customer) {
            return response()->json(new JsonResponse([], trans('admin.data_not_found')), Response::HTTP_NOT_FOUND);
        }

        $data['status'] = empty($data['status']) ? 0 : 1;
        $data['id'] = $id;
        $dataMapping = $this->mappingValidatorEdit($data);

        $validator =  Validator::make($data, $dataMapping['validate'], $dataMapping['messages']);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }
        Customer::updateInfo($dataMapping['dataUpdate'], $id);

        //Update custom field
        if (!empty($data['fields'])) {
            (new ShopCustomFieldDetail)
                ->join('shop_custom_field', 'shop_custom_field.id', 'shop_custom_field_detail.custom_field_id')
                ->select('code', 'name', 'text')
                ->where('shop_custom_field_detail.rel_id', $customer->id)
                ->where('shop_custom_field.type', 'customer')
                ->delete();

            $dataField = [];
            foreach ($data['fields'] as $key => $value) {
                $field = (new ShopCustomField)->where('code', $key)->where('type', 'customer')->first();
                if ($field) {
                    $dataField[] = [
                        'custom_field_id' => $field->id,
                        'rel_id' => $customer->id,
                        'text' => trim($value),
                    ];
                }
            }
            if ($dataField) {
                (new ShopCustomFieldDetail)->insert($dataField);
            }
        }
        return response()->json(new JsonResponse([]), Response::HTTP_OK);
    }

    /*
    Delete list Item
    Need mothod destroy to boot deleting in model
    */
    public function destroy($id)
    {
        $arrID = explode(',', $id);
        Customer::destroy($arrID);
        return response()->json(new JsonResponse([]), Response::HTTP_OK);
    }


    /**
     * Render address detail
     * @return [view]
     */
    public function showAddress($id)
    {
        $address =  Customer::getAddress($id);
        return response()->json(new JsonResponse(['data' => $address]), Response::HTTP_OK);
    }

    /**
     * Process update address
     *
     *
     * @return  [redirect] 
     */
    public function updateAddress($id)
    {
        $data = request()->all();
        $address =  Customer::getAddress($id);
        $dataUpdate = [
            'first_name' => $data['first_name'],
            'address1' => $data['address1'],
        ];
        $validate = [
            'first_name' => 'required|string|max:100',
        ];
        
        if (lc_config_admin('customer_lastname')) {
            if (lc_config_admin('customer_lastname_required')) {
                $validate['last_name'] = 'required|string|max:100';
            } else {
                $validate['last_name'] = 'nullable|string|max:100';
            }
            $dataUpdate['last_name'] = $data['last_name']??'';
        }

        if (lc_config_admin('customer_address1')) {
            if (lc_config_admin('customer_address1_required')) {
                $validate['address1'] = 'required|string|max:100';
            } else {
                $validate['address1'] = 'nullable|string|max:100';
            }
            $dataUpdate['address1'] = $data['address1']??'';
        }

        if (lc_config_admin('customer_address2')) {
            if (lc_config_admin('customer_address2_required')) {
                $validate['address2'] = 'required|string|max:100';
            } else {
                $validate['address2'] = 'nullable|string|max:100';
            }
            $dataUpdate['address2'] = $data['address2']??'';
        }

        if (lc_config_admin('customer_address3')) {
            if (lc_config_admin('customer_address3_required')) {
                $validate['address3'] = 'required|string|max:100';
            } else {
                $validate['address3'] = 'nullable|string|max:100';
            }
            $dataUpdate['address3'] = $data['address3']??'';
        }

        if (lc_config_admin('customer_phone')) {
            if (lc_config_admin('customer_phone_required')) {
                $validate['phone'] = 'required|regex:/^0[^0][0-9\-]{7,13}$/';
            } else {
                $validate['phone'] = 'nullable|regex:/^0[^0][0-9\-]{7,13}$/';
            }
            $dataUpdate['phone'] = $data['phone']??'';
        }

        if (lc_config_admin('customer_country')) {
            $arraycountry = (new ShopCountry)->pluck('code')->toArray();
            if (lc_config_admin('customer_country_required')) {
                $validate['country'] = 'required|string|min:2|in:'. implode(',', $arraycountry);
            } else {
                $validate['country'] = 'nullable|string|min:2|in:'. implode(',', $arraycountry);
            }
            
            $dataUpdate['country'] = $data['country']??'';
        }

        if (lc_config_admin('customer_postcode')) {
            if (lc_config_admin('customer_postcode_required')) {
                $validate['postcode'] = 'required|min:5';
            } else {
                $validate['postcode'] = 'nullable|min:5';
            }
            $dataUpdate['postcode'] = $data['postcode']??'';
        }

        if (lc_config_admin('customer_name_kana')) {
            if (lc_config_admin('customer_name_kana_required')) {
                $validate['first_name_kana'] = 'required|string|max:100';
                $validate['last_name_kana'] = 'required|string|max:100';
            } else {
                $validate['first_name_kana'] = 'nullable|string|max:100';
                $validate['last_name_kana'] = 'nullable|string|max:100';
            }
            $dataUpdate['first_name_kana'] = $data['first_name_kana']?? '';
            $dataUpdate['last_name_kana'] = $data['last_name_kana']?? '';
        }

        $messages = [
            'last_name.required'  => trans('validation.required',['attribute'=> trans('account.last_name')]),
            'first_name.required' => trans('validation.required',['attribute'=> trans('account.first_name')]),
            'address1.required'   => trans('validation.required',['attribute'=> trans('account.address1')]),
            'address2.required'   => trans('validation.required',['attribute'=> trans('account.address2')]),
            'address3.required'   => trans('validation.required',['attribute'=> trans('account.address3')]),
            'phone.required'      => trans('validation.required',['attribute'=> trans('account.phone')]),
            'country.required'    => trans('validation.required',['attribute'=> trans('account.country')]),
            'postcode.required'   => trans('validation.required',['attribute'=> trans('account.postcode')]),
            'phone.regex'         => trans('customer.phone_regex'),
            'postcode.min'        => trans('validation.min',['attribute'=> trans('account.postcode')]),
            'country.min'         => trans('validation.min',['attribute'=> trans('account.country')]),
            'first_name.max'      => trans('validation.max',['attribute'=> trans('account.first_name')]),
            'address1.max'        => trans('validation.max',['attribute'=> trans('account.address1')]),
            'address2.max'        => trans('validation.max',['attribute'=> trans('account.address2')]),
            'address3.max'        => trans('validation.max',['attribute'=> trans('account.address3')]),
            'last_name.max'       => trans('validation.max',['attribute'=> trans('account.last_name')]),
        ];

        $validator = Validator::make(
            $dataUpdate, 
            $validate, 
            $messages
        );
        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }

        $address->update(lc_clean($dataUpdate));

        if (!empty($data['default'])) {
            $customer = (new Customer)->getCustomerAdmin($address->customer_id);
            $customer->address_id = $id;
            $customer->save();
        }
        return response()->json(new JsonResponse(['id' => $address->customer_id]), Response::HTTP_OK);
    }

    /**
     * Get address detail 
     *
     * @return  [json] 
     */
    public function deleteAddress() {
        $id = request('id');
        Customer::deleteAddress($id);
        return response()->json(new JsonResponse([]), Response::HTTP_OK);
    }

}
