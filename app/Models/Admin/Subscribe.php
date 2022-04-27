<?php

namespace App\Models\Admin;

use App\Models\Front\ShopSubscribe;

class Subscribe extends ShopSubscribe
{
    const ITEM_PER_PAGE = 15;
    /**
     * Get subcribe detail in admin
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  [type]       [return description]
     */
    public static function getSubscribeAdmin($id) {
        return self::where('id', $id)
        ->where('store_id', session('adminStoreId'))
        ->first();
    }

    /**
     * Get list subcribe in admin
     *
     * @param   [array]  $dataSearch  [$dataSearch description]
     *
     * @return  [type]               [return description]
     */
    public static function getSubscribeListAdmin(array $dataSearch) {

        $sort_order = lc_clean($dataSearch['sort_order'] ?? 'id_desc');
        $keyword    = lc_clean($dataSearch['keyword'] ?? '');
        $limit      = lc_clean($dataSearch['limit'] ?? self::ITEM_PER_PAGE);
        $arrSort = [
            'id__desc' => trans('subscribe.admin.sort_order.id_desc'),
            'id__asc' => trans('subscribe.admin.sort_order.id_asc'),
            'email__desc' => trans('subscribe.admin.sort_order.email_desc'),
            'email__asc' => trans('subscribe.admin.sort_order.email_asc'),
        ];

        $subcribeList = (new Subscribe)
            ->whereIn('store_id', session('adminStoreId'));

        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $subcribeList = $subcribeList->orderBy($field, $sort_field);
        } else {
            $subcribeList = $subcribeList->orderBy('id', 'desc');
        }
        $subcribeList = $subcribeList->paginate($limit);

        return $subcribeList;
    }

    /**
     * Create a new subcribe
     *
     * @param   array  $dataInsert  [$dataInsert description]
     *
     * @return  [type]              [return description]
     */
    public static function createSubscribeAdmin(array $dataInsert) {
        $dataInsert = bc_clean($dataInsert);
        return self::create($dataInsert);
    }

}
