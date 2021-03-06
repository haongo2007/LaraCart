<?php

namespace App\Models\Admin;

use App\Models\Front\ShopNews;
use Cache;
use App\Models\Front\ShopNewsDescription;

class News extends ShopNews
{
    protected static $getListTitleAdmin = null;
    protected static $getListNewsGroupByParentAdmin = null;
    const ITEM_PER_PAGE = 15;
    /**
     * Get news detail in admin
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  [type]       [return description]
     */
    public static function getNewsAdmin($id) {
        return self::where('id', $id)
        ->where('store_id', session('adminStoreId'))
        ->first();
    }

    /**
     * Get list news in admin
     *
     * @param   [array]  $dataSearch  [$dataSearch description]
     *
     * @return  [type]               [return description]
     */
    public static function getNewsListAdmin(array $dataSearch) {
        $arrSort = [
            'id__desc' => trans('news.admin.sort_order.id_desc'),
            'id__asc' => trans('news.admin.sort_order.id_asc'),
            'title__desc' => trans('news.admin.sort_order.title_desc'),
            'title__asc' => trans('news.admin.sort_order.title_asc'),
        ];

        $keyword          = lc_clean($dataSearch['keyword'] ?? '');
        $sort_order       = lc_clean($dataSearch['sort_order'] ?? 'id_desc');
        $limit            = lc_clean($dataSearch['limit'] ?? self::ITEM_PER_PAGE);
        $storeId          = lc_clean($dataSearch['storeId'] ?? session('adminStoreId'));

        if ($storeId && !is_array($storeId)) {
            $storeId = [$storeId];
        }

        $tableDescription = (new ShopNewsDescription)->getTable();
        $tableNews     = (new ShopNews)->getTable();

        $newsList = (new ShopNews)
            ->leftJoin($tableDescription, $tableDescription . '.news_id', $tableNews . '.id')
            ->where($tableDescription . '.lang', lc_get_locale());

        $newsList = $newsList->whereIn($tableNews . '.store_id',  $storeId);

        if ($keyword) {
            $newsList = $newsList->where(function ($sql) use($tableDescription, $keyword){
                $sql->where($tableDescription . '.title', 'like', '%' . $keyword . '%');
            });
        }

        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $newsList = $newsList->orderBy($field, $sort_field);
        } else {
            $newsList = $newsList->orderBy('id', 'desc');
        }
        $newsList = $newsList->paginate($limit);

        return $newsList;
    }


    /**
     * Get array title news
     * user for admin 
     *
     * @return  [type]  [return description]
     */
    public static function getListTitleAdmin()
    {
        $tableDescription = (new ShopNewsDescription)->getTable();
        $table = (new AdminNews)->getTable();
        if (bc_config_global('cache_status') && bc_config_global('cache_news')) {
            if (!Cache::has(session('adminStoreId').'_cache_news_'.bc_get_locale())) {
                if (self::$getListTitleAdmin === null) {
                    self::$getListTitleAdmin = self::join($tableDescription, $tableDescription.'.news_id', $table.'.id')
                    ->where('lang', bc_get_locale())
                    ->where('store_id', session('adminStoreId'))
                    ->pluck('title', 'id')
                    ->toArray();
                }
                bc_set_cache(session('adminStoreId').'_cache_news_'.bc_get_locale(), self::$getListTitleAdmin);
            }
            return Cache::get(session('adminStoreId').'_cache_news_'.bc_get_locale());
        } else {
            if (self::$getListTitleAdmin === null) {
                self::$getListTitleAdmin = self::join($tableDescription, $tableDescription.'.news_id', $table.'.id')
                ->where('lang', bc_get_locale())
                ->where('store_id', session('adminStoreId'))
                ->pluck('title', 'id')
                ->toArray();
            }
            return self::$getListTitleAdmin;
        }
    }


    /**
     * Create a new news
     *
     * @param   array  $dataInsert  [$dataInsert description]
     *
     * @return  [type]              [return description]
     */
    public static function createNewsAdmin(array $dataInsert) {

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

        return ShopNewsDescription::create($dataInsert);
    }

     /**
     * Get total news of system
     *
     * @return  [type]  [return description]
     */
    public static function getTotalNews() {
        return self::count();
    }
}
