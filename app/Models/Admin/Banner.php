<?php

namespace App\Models\Admin;

use App\Models\Front\ShopBanner;

class Banner extends ShopBanner
{
    const ITEM_PER_PAGE = 15;
    /**
     * Get banner detail in admin
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  [type]       [return description]
     */
    public static function getBannerAdmin($id) {
        return self::where('id', $id)
        ->whereIn('store_id', session('adminStoreId'))
        ->first();
    }

    /**
     * Get list banner in admin
     *
     * @param   [array]  $dataSearch  [$dataSearch description]
     *
     * @return  [type]               [return description]
     */
    public static function getBannerListAdmin(array $dataSearch) {

        $sort_order = lc_clean($dataSearch['sort_order'] ?? 'id_desc');
        $arrSort = [
            'id__desc' => trans('banner.admin.sort_order.id_desc'),
            'id__asc' => trans('banner.admin.sort_order.id_asc'),
        ];
        $keyword    = lc_clean($dataSearch['keyword'] ?? '');
        $limit      = lc_clean($dataSearch['limit'] ?? self::ITEM_PER_PAGE);

        $bannerList = (new ShopBanner)->whereIn('store_id', session('adminStoreId'));
        if ($keyword) {
            $bannerList = $bannerList->where('title', 'like', '%'.$keyword.'%');
        }
        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $bannerList = $bannerList->sort($field, $sort_field);
        } else {
            $bannerList = $bannerList->sort('id', 'desc');
        }
        $bannerList = $bannerList->paginate($limit);

        return $bannerList;
    }

    /**
     * Create a new banner
     *
     * @param   array  $dataInsert  [$dataInsert description]
     *
     * @return  [type]              [return description]
     */
    public static function createBannerAdmin(array $dataInsert) {

        return self::create($dataInsert);
    }

}
