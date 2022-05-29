<?php

namespace App\Models\Admin;

use App\Models\Front\ShopBannerType;

class BannerType extends ShopBannerType
{

    /**
     * Get list banner in admin
     *
     * @param   [array]  $dataSearch  [$dataSearch description]
     *
     * @return  [type]               [return description]
     */
    public static function getBannerTypeListAdmin(array $dataSearch) {
        $keyword    = lc_clean($dataSearch['keyword'] ?? '');
        $storeId    = lc_clean($dataSearch['store_id'] ?? session('adminStoreId'));
        $bannerTypeList = (new self);
        if (!is_array($storeId)) {
            $bannerTypeList = $bannerTypeList->where('store_id', $storeId);
        }else{
            $bannerTypeList = $bannerTypeList->whereIn('store_id', $storeId);
        }
        if ($keyword) {
            $bannerTypeList = $bannerTypeList->where('name', 'like', '%'.$keyword.'%');
        }

        $bannerTypeList = $bannerTypeList->get();

        return $bannerTypeList;
    }

}
