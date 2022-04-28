<?php

namespace App\Models\Admin;

use App\Models\Front\ShopCustomer;
use App\Models\Front\ShopCustomerAddress;
use Illuminate\Support\Arr;

class Customer extends ShopCustomer
{
    const ITEM_PER_PAGE = 15;
    protected static $getListTitleAdmin = null;
    protected static $getListCustomerGroupByParentAdmin = null;
    private static $getList = null;
    /**
     * Get customer detail in admin
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  [type]       [return description]
     */
    public static function getCustomerAdmin($id) {
        return self::with('addresses')
        ->where('id', $id)
        ->where('store_id', session('adminStoreId'))
        ->first();
    }

    /**
     * Get customer detail in admin json
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  [type]       [return description]
     */
    public static function getCustomerAdminJson($id) {
        return self::getCustomerAdmin($id)
        ->toJson();
    }

    /**
     * Get list customer in admin
     *
     * @param   [array]  $dataSearch  [$dataSearch description]
     *
     * @return  [type]               [return description]
     */
    public static function getCustomerListAdmin(array $dataSearch) {
        $arrSort = [
            'id__desc' => trans('customer.admin.sort_order.id_desc'),
            'id__asc' => trans('customer.admin.sort_order.id_asc'),
            'first_name__desc' => trans('customer.admin.sort_order.first_name_desc'),
            'first_name__asc' => trans('customer.admin.sort_order.first_name_asc'),
            'last_name__desc' => trans('customer.admin.sort_order.last_name_desc'),
            'last_name__asc' => trans('customer.admin.sort_order.last_name_asc'),
        ];

        $keyword    = Arr::get($dataSearch,'keyword','');
        $sort_order = Arr::get($dataSearch,'sort_order','');
        $arrSort          = $arrSort;
        $limit            = lc_clean($dataSearch['limit'] ?? self::ITEM_PER_PAGE);

        $customerList = (new ShopCustomer)
            ->whereIn('store_id', session('adminStoreId'));

        if ($keyword) {
            $customerList->where('email', 'like', '%' . $keyword . '%')
            ->orWhere('last_name', 'like', '%' . $keyword . '%')
            ->orWhere('first_name', 'like', '%' . $keyword . '%');
        }

        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $customerList = $customerList->orderBy($field, $sort_field);
        } else {
            $customerList = $customerList->orderBy('id', 'desc');
        }
        $customerList = $customerList->paginate($limit);

        return $customerList;
    }

    /**
     * Find address id
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  [type]       [return description]
     */
    public static function getAddress($id) {
        return ShopCustomerAddress::find($id);
    }

    /**
     * Delete address id
     *
     * @return  [type]  [return description]
     */
    public static function deleteAddress($id) {
        return ShopCustomerAddress::where('id', $id)->delete();
    }

    /**
     * Get total customer of system
     *
     * @return  [type]  [return description]
     */
    public static function getTotalCustomer() {
        return self::count();
    }


    /**
     * Get total customer of system
     *
     * @return  [type]  [return description]
     */
    public static function getTopCustomer() {
        return self::orderBy('id', 'desc')
            ->limit(10)
            ->get();
    }


    /**
     * [getListAll description]
     * Performance can be affected if the data is too large
     * @return  [type]  [return description]
     */
    public static function getListAll()
    {
        if (self::$getList === null) {
            self::$getList = self::where('store_id', session('adminStoreId'))
                ->get()->keyBy('id');
        }
        return self::$getList;
    }

    /**
     * Get Sum customer total In Week
     *
     * @return  [type]  [return description]
     */
    public static function getSumCustomerTotalIn($type = '1 WEEK') {
        return self::selectRaw('DATE_FORMAT(created_at, "%m-%d") AS d, count(id) AS total_customer')
            ->whereRaw('created_at >=  DATE_FORMAT(DATE_SUB(CURRENT_DATE(), INTERVAL '.$type.'), "%Y-%m-%d")')
            ->groupBy('d')->get();
    }
    /**
     * Get Sum customer total In Custom
     *
     * @return  [type]  [return description]
     */
    public static function getSumCustomerTotalCustomTime($from = '',$to = '',$storeId = null) {
        return self::selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d") AS d,store_id,
            count(id) AS total_customer')
            ->whereBetween('created_at',[$from,$to])
            ->whereIn('store_id',$storeId)
            ->groupBy('store_id','d')->get();
    }
}
