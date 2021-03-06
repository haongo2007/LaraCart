<?php

namespace App\Http\Controllers\Api\Front\Auth;
use App\Models\Front\ShopCustomer;
use App\Models\Front\ShopCustomField;
use App\Models\Front\ShopCountry;

/**
 * Trait Auth controller.
 */
trait AuthTrait
{
    /**
     * Map validate when edit customer
     *
     * @param   [array]  $data  [$data description]
     *
     * @return  [array]         [return description]
     */
    public function mappingValidatorEdit($data) {
        $dataUpdate = [
            'first_name' => $data['first_name'],
        ];
        if (isset($data['status'])) {
            $dataUpdate['status']  = $data['status'];
        }
        $validate = [
            'first_name' => config('validation.customer.first_name', 'required|string|max:100'),
            'password' => config('validation.customer.password_null', 'nullable|string|min:6'),
        ];

        if (!empty($data['password'])) {
            $dataUpdate['password'] = bcrypt($data['password']);
        }
        if (!empty($data['email'])) {
            $dataUpdate['email'] = $data['email'];
            $validate['email'] = config('validation.customer.email', 'required|string|email|max:255').'|unique:"'.ShopCustomer::class.'",email, '.$data['id'].',id';
        }
        //Dont update id
        unset($data['id']);

        if (lc_config('customer_lastname',$data['store_id'])) {
            if (lc_config('customer_lastname_required',$data['store_id'])) {
                $validate['last_name'] = config('validation.customer.last_name_required', 'required|string|max:100');
            } else {
                $validate['last_name'] = config('validation.customer.last_name_null', 'nullable|string|max:100');
            }
            if (!empty($data['last_name'])) {
                $dataUpdate['last_name'] = $data['last_name'];
            }
        }
        if (lc_config('customer_address1',$data['store_id'])) {
            if (lc_config('customer_address1_required',$data['store_id'])) {
                $validate['address1'] = config('validation.customer.address1_required', 'required|string|max:100');
            } else {
                $validate['address1'] = config('validation.customer.address1_null', 'nullable|string|max:100');
            }
            if (!empty($data['address1'])) {
                $dataUpdate['address1'] = $data['address1'];
            }
        }

        if (lc_config('customer_address2',$data['store_id'])) {
            if (lc_config('customer_address2_required',$data['store_id'])) {
                $validate['address2'] = config('validation.customer.address2_required', 'required|string|max:100');
            } else {
                $validate['address2'] = config('validation.customer.address2_null', 'nullable|string|max:100');
            }
            if (!empty($data['address2'])) {
                $dataUpdate['address2'] = $data['address2'];
            }
        }

        if (lc_config('customer_address3',$data['store_id'])) {
            if (lc_config('customer_address3_required',$data['store_id'])) {
                $validate['address3'] = config('validation.customer.address3_required', 'required|string|max:100');
            } else {
                $validate['address3'] = config('validation.customer.address3_null', 'nullable|string|max:100');
            }
            if (!empty($data['address3'])) {
                $dataUpdate['address3'] = $data['address3'];
            }
        }


        if (lc_config('customer_phone',$data['store_id'])) {
            if (lc_config('customer_phone_required',$data['store_id'])) {
                $validate['phone'] = config('validation.customer.phone_required', 'required|regex:/^[0-9\-]{8,14}$/');
            } else {
                $validate['phone'] = config('validation.customer.phone_null', 'nullable|regex:/^[0-9\-]{8,14}$/');
            }
            if (!empty($data['phone'])) {
                $dataUpdate['phone'] = $data['phone'];
            }
        }

        if (lc_config('customer_country',$data['store_id'])) {
            $arraycountry = (new ShopCountry)->pluck('code')->toArray();
            if (lc_config('customer_country_required',$data['store_id'])) {
                $validate['country'] = config('validation.customer.country_required', 'required|string|min:2').'|in:'. implode(',', $arraycountry);
            } else {
                $validate['country'] = config('validation.customer.country_null', 'nullable|string|min:2').'|in:'. implode(',', $arraycountry);
            }
            if (!empty($data['country'])) {
                $dataUpdate['country'] = $data['country'];
            }
        }

        if (lc_config('customer_postcode',$data['store_id'])) {
            if (lc_config('customer_postcode_required',$data['store_id'])) {
                $validate['postcode'] = config('validation.customer.postcode_required', 'required|min:5');
            } else {
                $validate['postcode'] = config('validation.customer.postcode_null', 'nullable|min:5');
            }
            if (!empty($data['postcode'])) {
                $dataUpdate['postcode'] = $data['postcode'];
            }
        }

        if (lc_config('customer_company',$data['store_id'])) {
            if (lc_config('customer_company_required',$data['store_id'])) {
                $validate['company'] = config('validation.customer.company_required', 'required|string|max:100');
            } else {
                $validate['company'] = config('validation.customer.company_null', 'nullable|string|max:100');
            }
            if (!empty($data['company'])) {
                $dataUpdate['company'] = $data['company'];
            }
        }   

        if (lc_config('customer_sex',$data['store_id'])) {
            if (lc_config('customer_sex_required',$data['store_id'])) {
                $validate['sex'] = config('validation.customer.sex_required', 'required|integer|max:10');
            } else {
                $validate['sex'] = config('validation.customer.sex_null', 'nullable|integer|max:10');
            }
            if (!empty($data['sex'])) {
                $dataUpdate['sex'] = $data['sex'];
            }
        }   

        if (lc_config('customer_birthday',$data['store_id'])) {
            if (lc_config('customer_birthday_required',$data['store_id'])) {
                $validate['birthday'] = config('validation.customer.birthday_required', 'required|date|date_format:Y-m-d');
            } else {
                $validate['birthday'] = config('validation.customer.birthday_null', 'nullable|date|date_format:Y-m-d');
            }
            if (!empty($data['birthday'])) {
                $dataUpdate['birthday'] = $data['birthday'];
            }
        } 

        if (lc_config('customer_group',$data['store_id'])) {
            if (lc_config('customer_group_required',$data['store_id'])) {
                $validate['group'] = config('validation.customer.group_required', 'required|integer|max:10');
            } else {
                $validate['group'] = config('validation.customer.group_null', 'nullable|integer|max:10');
            }
            if (!empty($data['group'])) {
                $dataUpdate['group'] = $data['group'];
            }
        }

        if (lc_config('customer_name_kana',$data['store_id'])) {
            if (lc_config('customer_name_kana_required',$data['store_id'])) {
                $validate['first_name_kana'] = config('validation.customer.name_kana_required', 'required|string|max:100');
                $validate['last_name_kana'] = config('validation.customer.name_kana_required', 'required|string|max:100');
            } else {
                $validate['first_name_kana'] = config('validation.customer.name_kana_null', 'nullable|string|max:100');
                $validate['last_name_kana'] = config('validation.customer.name_kana_null', 'nullable|string|max:100');
            }
            $dataUpdate['first_name_kana'] = $data['first_name_kana']?? '';
            $dataUpdate['last_name_kana'] = $data['last_name_kana']?? '';
        }

        $messages = [
            'last_name.required'   => trans('validation.required', ['attribute'=> trans('customer.last_name')]),
            'first_name.required'  => trans('validation.required', ['attribute'=> trans('customer.first_name')]),
            'email.required'       => trans('validation.required', ['attribute'=> trans('customer.email')]),
            'password.required'    => trans('validation.required', ['attribute'=> trans('customer.password')]),
            'address1.required'    => trans('validation.required', ['attribute'=> trans('customer.address1')]),
            'address2.required'    => trans('validation.required', ['attribute'=> trans('customer.address2')]),
            'address3.required'    => trans('validation.required', ['attribute'=> trans('customer.address3')]),
            'phone.required'       => trans('validation.required', ['attribute'=> trans('customer.phone')]),
            'country.required'     => trans('validation.required', ['attribute'=> trans('customer.country')]),
            'postcode.required'    => trans('validation.required', ['attribute'=> trans('customer.postcode')]),
            'company.required'     => trans('validation.required', ['attribute'=> trans('customer.company')]),
            'sex.required'         => trans('validation.required', ['attribute'=> trans('customer.sex')]),
            'birthday.required'    => trans('validation.required', ['attribute'=> trans('customer.birthday')]),
            'email.email'          => trans('validation.email', ['attribute'=> trans('customer.email')]),
            'phone.regex'          => trans('customer.phone_regex'),
            'password.confirmed'   => trans('validation.confirmed', ['attribute'=> trans('customer.password')]),
            'postcode.min'         => trans('validation.min', ['attribute'=> trans('customer.postcode')]),
            'password.min'         => trans('validation.min', ['attribute'=> trans('customer.password')]),
            'country.min'          => trans('validation.min', ['attribute'=> trans('customer.country')]),
            'first_name.max'       => trans('validation.max', ['attribute'=> trans('customer.first_name')]),
            'email.max'            => trans('validation.max', ['attribute'=> trans('customer.email')]),
            'address1.max'         => trans('validation.max', ['attribute'=> trans('customer.address1')]),
            'address2.max'         => trans('validation.max', ['attribute'=> trans('customer.address2')]),
            'address3.max'         => trans('validation.max', ['attribute'=> trans('customer.address3')]),
            'last_name.max'        => trans('validation.max', ['attribute'=> trans('customer.last_name')]),
            'birthday.date'        => trans('validation.date', ['attribute'=> trans('customer.birthday')]),
            'birthday.date_format' => trans('validation.date_format', ['attribute'=> trans('customer.birthday')]),
        ];
        $dataMap = [
            'validate' => $validate,
            'messages' => $messages,
            'dataUpdate' => $dataUpdate
        ];
        return $dataMap;
    }

    /**
     * Mapp validate when register new customer
     *
     * @param [array] $data  [$data description]
     *
     * @return [array]         [return description]
     */
    public function mappingValidator($data) {
        $dataInsert = $this->mappDataInsert($data);
        $validate = [
            'first_name' => config('validation.customer.first_name', 'required|string|max:100'),
            'email' => config('validation.customer.email', 'required|string|email|max:255').'|unique:"'.ShopCustomer::class.'",email',
            'password' => config('validation.customer.password', 'nullable|string|min:6'),
        ];

        //Custom fields
        $customFields = (new ShopCustomField)->getCustomField($type = 'customer');
        if ($customFields) {
            foreach ($customFields as $field) {
                if ($field->required) {
                    $validate['fields.'.$field->code] = 'required';
                }
            }
        }

        if (lc_config('customer_lastname',$data['store_id'])) {
            if (lc_config('customer_lastname_required',$data['store_id'])) {
                $validate['last_name'] = config('validation.customer.last_name_required', 'required|string|max:100');
            } else {
                $validate['last_name'] = config('validation.customer.last_name_null', 'nullable|string|max:100');
            }
        }
        if (lc_config('customer_address1',$data['store_id'])) {
            if (lc_config('customer_address1_required',$data['store_id'])) {
                $validate['address1'] = config('validation.customer.address1_required', 'required|string|max:100');
            } else {
                $validate['address1'] = config('validation.customer.address1_null', 'nullable|string|max:100');
            }
        }

        if (lc_config('customer_address2',$data['store_id'])) {
            if (lc_config('customer_address2_required',$data['store_id'])) {
                $validate['address2'] = config('validation.customer.address2_required', 'required|string|max:100');
            } else {
                $validate['address2'] = config('validation.customer.address2_null', 'nullable|string|max:100');
            }
        }

        if (lc_config('customer_address3',$data['store_id'])) {
            if (lc_config('customer_address3_required',$data['store_id'])) {
                $validate['address3'] = config('validation.customer.address3_required', 'required|string|max:100');
            } else {
                $validate['address3'] = config('validation.customer.address3_null', 'nullable|string|max:100');
            }
        }


        if (lc_config('customer_phone',$data['store_id'])) {
            if (lc_config('customer_phone_required',$data['store_id'])) {
                $validate['phone'] = config('validation.customer.phone_required', 'required|regex:/^[0-9\-]{8,14}$/');
            } else {
                $validate['phone'] = config('validation.customer.phone_null', 'nullable|regex:/^[0-9\-]{8,14}$/');
            }
        }
        if (lc_config('customer_country',$data['store_id'])) {
            $arraycountry = (new ShopCountry)->pluck('code')->toArray();
            if (lc_config('customer_country_required',$data['store_id'])) {
                $validate['country'] = config('validation.customer.country_required', 'required|string|min:2').'|in:'. implode(',', $arraycountry);
            } else {
                $validate['country'] = config('validation.customer.country_null', 'nullable|string|min:2').'|in:'. implode(',', $arraycountry);
            }
        }

        if (lc_config('customer_postcode',$data['store_id'])) {
            if (lc_config('customer_postcode_required',$data['store_id'])) {
                $validate['postcode'] = config('validation.customer.postcode_required', 'required|min:5');
            } else {
                $validate['postcode'] = config('validation.customer.postcode_null', 'nullable|min:5');
            }
        }
        if (lc_config('customer_company',$data['store_id'])) {
            if (lc_config('customer_company_required',$data['store_id'])) {
                $validate['company'] = config('validation.customer.company_required', 'required|string|max:100');
            } else {
                $validate['company'] = config('validation.customer.company_null', 'nullable|string|max:100');
            }
        }   
        if (lc_config('customer_sex',$data['store_id'])) {
            if (lc_config('customer_sex_required',$data['store_id'])) {
                $validate['sex'] = config('validation.customer.sex_required', 'required|integer|max:10');
            } else {
                $validate['sex'] = config('validation.customer.sex_null', 'nullable|integer|max:10');
            }
        }   
        if (lc_config('customer_birthday',$data['store_id'])) {
            if (lc_config('customer_birthday_required',$data['store_id'])) {
                $validate['birthday'] = config('validation.customer.birthday_required', 'required|date|date_format:Y-m-d');
            } else {
                $validate['birthday'] = config('validation.customer.birthday_null', 'nullable|date|date_format:Y-m-d');
            }
        } 
        if (lc_config('customer_group',$data['store_id'])) {
            if (lc_config('customer_group_required',$data['store_id'])) {
                $validate['group'] = config('validation.customer.group_required', 'required|integer|max:10');
            } else {
                $validate['group'] = config('validation.customer.group_null', 'nullable|integer|max:10');
            }
        }

        if (lc_config('customer_name_kana',$data['store_id'])) {
            if (lc_config('customer_name_kana_required',$data['store_id'])) {
                $validate['first_name_kana'] = config('validation.customer.name_kana_required', 'required|string|max:100');
                $validate['last_name_kana'] = config('validation.customer.name_kana_required', 'required|string|max:100');
            } else {
                $validate['first_name_kana'] = config('validation.customer.name_kana_null', 'nullable|string|max:100');
                $validate['last_name_kana'] = config('validation.customer.name_kana_null', 'nullable|string|max:100');
            }
        }

        $messages = [
            'last_name.required'   => trans('validation.required', ['attribute'=> trans('customer.last_name')]),
            'first_name.required'  => trans('validation.required', ['attribute'=> trans('customer.first_name')]),
            'email.required'       => trans('validation.required', ['attribute'=> trans('customer.email')]),
            'password.required'    => trans('validation.required', ['attribute'=> trans('customer.password')]),
            'address1.required'    => trans('validation.required', ['attribute'=> trans('customer.address1')]),
            'address2.required'    => trans('validation.required', ['attribute'=> trans('customer.address2')]),
            'address3.required'    => trans('validation.required', ['attribute'=> trans('customer.address3')]),
            'phone.required'       => trans('validation.required', ['attribute'=> trans('customer.phone')]),
            'country.required'     => trans('validation.required', ['attribute'=> trans('customer.country')]),
            'postcode.required'    => trans('validation.required', ['attribute'=> trans('customer.postcode')]),
            'company.required'     => trans('validation.required', ['attribute'=> trans('customer.company')]),
            'sex.required'         => trans('validation.required', ['attribute'=> trans('customer.sex')]),
            'birthday.required'    => trans('validation.required', ['attribute'=> trans('customer.birthday')]),
            'email.email'          => trans('validation.email', ['attribute'=> trans('customer.email')]),
            'phone.regex'          => trans('customer.phone_regex'),
            'password.confirmed'   => trans('validation.confirmed', ['attribute'=> trans('customer.password')]),
            'postcode.min'         => trans('validation.min', ['attribute'=> trans('customer.postcode')]),
            'password.min'         => trans('validation.min', ['attribute'=> trans('customer.password')]),
            'country.min'          => trans('validation.min', ['attribute'=> trans('customer.country')]),
            'first_name.max'       => trans('validation.max', ['attribute'=> trans('customer.first_name')]),
            'email.max'            => trans('validation.max', ['attribute'=> trans('customer.email')]),
            'address1.max'         => trans('validation.max', ['attribute'=> trans('customer.address1')]),
            'address2.max'         => trans('validation.max', ['attribute'=> trans('customer.address2')]),
            'address3.max'         => trans('validation.max', ['attribute'=> trans('customer.address3')]),
            'last_name.max'        => trans('validation.max', ['attribute'=> trans('customer.last_name')]),
            'birthday.date'        => trans('validation.date', ['attribute'=> trans('customer.birthday')]),
            'birthday.date_format' => trans('validation.date_format', ['attribute'=> trans('customer.birthday')]),
        ];
        $dataMap = [
            'validate' => $validate,
            'messages' => $messages,
            'dataInsert' => $dataInsert
        ];
        return $dataMap;
    }

    /**
     * Mapping data before inser
     *
     * @param [type]  $data  [$data description]
     *
     * @return [type]         [return description]
     */
    public function mappDataInsert($data) {

        $dataInsert = [
            'first_name' => $data['first_name'] ?? '',
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ];
        if (isset($data['status'])) {
            $dataInsert['status']  = $data['status'];
        }
        if (lc_config('customer_lastname',$data['store_id'])) {
            if (!empty($data['last_name'])) {
                $dataInsert['last_name'] = $data['last_name'];
            }
        }
        if (lc_config('customer_firstname_kana',$data['store_id'])) {
            if (!empty($data['first_name_kana'])) {
                $dataInsert['first_name_kana'] = $data['first_name_kana'];
            }
        }
        if (lc_config('customer_lastname_kana',$data['store_id'])) {
            if (!empty($data['last_name_kana'])) {
                $dataInsert['last_name_kana'] = $data['last_name_kana'];
            }
        }
        if (lc_config('customer_address1',$data['store_id'])) {
            if (!empty($data['address1'])) {
                $dataInsert['address1'] = $data['address1'];
            }
        }
        if (lc_config('customer_address2',$data['store_id'])) {
            if (!empty($data['address2'])) {
                $dataInsert['address2'] = $data['address2'];
            }
        }

        if (lc_config('customer_address3',$data['store_id'])) {
            if (!empty($data['address3'])) {
                $dataInsert['address3'] = $data['address3'];
            }
        }

        if (lc_config('customer_phone',$data['store_id'])) {
            if (!empty($data['phone'])) {
                $dataInsert['phone'] = $data['phone'];
            }
        }
        if (lc_config('customer_country',$data['store_id'])) {
            if (!empty($data['country'])) {
                $dataInsert['country'] = $data['country'];
            }
        }
        if (lc_config('customer_postcode',$data['store_id'])) {
            if (!empty($data['postcode'])) {
                $dataInsert['postcode'] = $data['postcode'];
            }
        }
        if (lc_config('customer_company',$data['store_id'])) {
            if (!empty($data['company'])) {
                $dataInsert['company'] = $data['company'];
            }
        }   
        if (lc_config('customer_sex',$data['store_id'])) {
            if (!empty($data['sex'])) {
                $dataInsert['sex'] = $data['sex'];
            }
        }   
        if (lc_config('customer_birthday',$data['store_id'])) {
            if (!empty($data['birthday'])) {
                $dataInsert['birthday'] = $data['birthday'];
            }
        } 
        if (lc_config('customer_group',$data['store_id'])) {
            if (!empty($data['group'])) {
                $dataInsert['group'] = $data['group'];
            }
        }
        return $dataInsert;
    }
}
