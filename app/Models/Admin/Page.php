<?php

namespace App\Models\Admin;

use App\Models\Front\ShopPage;
use Cache;
use App\Models\Front\ShopPageDescription;

class Page extends ShopPage
{
    const ITEM_PER_PAGE = 15;
    protected static $getListTitleAdmin = null;
    protected static $getListPageGroupByParentAdmin = null;
    /**
     * Get page detail in admin
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  [type]       [return description]
     */
    public static function getPageAdmin($id) {
        return self::where('id', $id)
        ->whereIn('store_id', session('adminStoreId'))
        ->first();
    }

    /**
     * Get list page in admin
     *
     * @param   [array]  $dataSearch  [$dataSearch description]
     *
     * @return  [type]               [return description]
     */
    public static function getPageListAdmin(array $dataSearch) {

        $arrSort = [
            'id__desc'    => trans('page.admin.sort_order.id_desc'),
            'id__asc'     => trans('page.admin.sort_order.id_asc'),
            'title__desc' => trans('page.admin.sort_order.title_desc'),
            'title__asc'  => trans('page.admin.sort_order.title_asc'),
        ];

        $keyword          = lc_clean($dataSearch['keyword'] ?? '');
        $sort_order       = lc_clean($dataSearch['sort_order'] ?? 'id_desc');
        $limit            = lc_clean($dataSearch['limit'] ?? self::ITEM_PER_PAGE);

        $tableDescription = (new ShopPageDescription)->getTable();
        $tablePage     = (new Page)->getTable();

        $pageList = (new ShopPage)
            ->leftJoin($tableDescription, $tableDescription . '.page_id', $tablePage . '.id')
            ->whereIn('store_id', session('adminStoreId'))
            ->where($tableDescription . '.lang', lc_get_locale());

        if ($keyword) {
            $pageList = $pageList->where(function ($sql) use($tableDescription, $keyword){
                $sql->where($tableDescription . '.title', 'like', '%' . $keyword . '%');
            });
        }

        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $pageList = $pageList->orderBy($field, $sort_field);
        } else {
            $pageList = $pageList->orderBy('id', 'desc');
        }
        $pageList = $pageList->paginate($limit);

        return $pageList;
    }


    /**
     * Get array title page
     * user for admin 
     *
     * @return  [type]  [return description]
     */
    public static function getListTitleAdmin()
    {
        $tableDescription = (new ShopPageDescription)->getTable();
        $table = (new AdminPage)->getTable();
        if (bc_config_global('cache_status') && bc_config_global('cache_page')) {
            if (!Cache::has(session('adminStoreId').'_cache_page_'.bc_get_locale())) {
                if (self::$getListTitleAdmin === null) {
                    self::$getListTitleAdmin = self::join($tableDescription, $tableDescription.'.page_id', $table.'.id')
                    ->where('lang', bc_get_locale())
                    ->where('store_id', session('adminStoreId'))
                    ->pluck('title', 'id')
                    ->toArray();
                }
                bc_set_cache(session('adminStoreId').'_cache_page_'.bc_get_locale(), self::$getListTitleAdmin);
            }
            return Cache::get(session('adminStoreId').'_cache_page_'.bc_get_locale());
        } else {
            if (self::$getListTitleAdmin === null) {
                self::$getListTitleAdmin = self::join($tableDescription, $tableDescription.'.page_id', $table.'.id')
                ->where('lang', bc_get_locale())
                ->where('store_id', session('adminStoreId'))
                ->pluck('title', 'id')
                ->toArray();
            }
            return self::$getListTitleAdmin;
        }
    }


    /**
     * Create a new page
     *
     * @param   array  $dataInsert  [$dataInsert description]
     *
     * @return  [type]              [return description]
     */
    public static function createPageAdmin(array $dataInsert) {

        return self::create($dataInsert);
    }


    /**
     * Insert data description
     *
     * @param   array  $dataInsert  [$dataInsert description]
     *
     * @return  [type]              [return description]
     */
    public static function insertDescriptionAdmin(array $dataInsert) {

        return ShopPageDescription::create($dataInsert);
    }
}
