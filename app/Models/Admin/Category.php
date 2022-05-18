<?php

namespace App\Models\Admin;

use App\Models\Front\ShopCategory;
use Cache;
use App\Models\Front\ShopCategoryDescription;
use Illuminate\Support\Arr;
use DB;

class Category extends ShopCategory
{
    const ITEM_PER_PAGE = 15;
    const ORDER = 'id__desc';
    const ACTIVE = ['1'];

    protected static $getListTitleAdmin = null;
    protected static $getListCategoryGroupByParentAdmin = null;
    /**
     * Get category detail in admin
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  [type]       [return description]
     */
    public static function getCategoryAdmin($id) {
        return self::where('id', $id)
        ->first();
    }
    /**
     * Get list category in admin
     *
     * @param   [array]  $dataSearch  [$dataSearch description]
     *
     * @return  [type]               [return description]
     */
    public static function getCategoryListAdmin(array $dataSearch) {

        $limit = Arr::get($dataSearch, 'limit', self::ITEM_PER_PAGE);
        $sort  = Arr::get($dataSearch, 'sort', self::ORDER);
        $status= Arr::get($dataSearch, 'status', self::ACTIVE);
        $title = Arr::get($dataSearch, 'name', '');
        $parent = Arr::get($dataSearch, 'parent', '');
        $storeId = Arr::get($dataSearch, 'store_id', '');
        $parent_list = Arr::get($dataSearch, 'parent_list', '');
        $except_id = Arr::get($dataSearch, 'except_id', '');

        $arrSort = [
            'id__desc' => trans('category.admin.sort_order.id_desc'),
            'id__asc' => trans('category.admin.sort_order.id_asc'),
            'title__desc' => trans('category.admin.sort_order.title_desc'),
            'title__asc' => trans('category.admin.sort_order.title_asc'),
        ];
        $tableDescription = (new ShopCategoryDescription)->getTable();
        $tableCategory    = (new ShopCategory)->getTable();
        $categoryList     = (new ShopCategory);

        $categoryList = $categoryList->leftJoin($tableDescription, $tableDescription . '.category_id', $tableCategory . '.id')
            ->where($tableDescription . '.lang', lc_get_locale());

        if ($title) {
            $categoryList = $categoryList->where(function ($sql) use($tableDescription, $title){
                $sql->where($tableDescription . '.title', 'like', '%' . $title . '%');
            });
        }
        if ($parent != '') {
            $categoryList = $categoryList->where('parent',$parent);
        }

        if ($parent_list != '') {
            $categoryList = $categoryList->whereRaw('FIND_IN_SET(id, "'.$parent_list.'")');
        }

        if (!is_null($status) && is_array($status)) {
            $categoryList = $categoryList->whereIn('status',$status);
        }

        if (count(session('adminStoreId')) == 1 || $storeId) {
            if (!$storeId) {
                $storeId = session('adminStoreId');
            }
            $categoryList = $categoryList->where('store_id',$storeId);
        }
        if ($except_id) {
            $categoryList = $categoryList->where('id','!=',$except_id);
        }
        if ($sort && array_key_exists($sort, $arrSort)) {
            $field = explode('__', $sort)[0];
            $sort_field = explode('__', $sort)[1];
            $categoryList = $categoryList->sort($field, $sort_field);
        } else {
            $categoryList = $categoryList->sort('id', 'desc');
        }

        $categoryList = $categoryList->paginate($limit);

        return $categoryList;
    }

    /**
     * Get tree categories
     *
     * @param   [type]  $parent      [$parent description]
     * @param   [type]  &$tree       [&$tree description]
     * @param   [type]  $categories  [$categories description]
     * @param   [type]  &$st         [&$st description]
     *
     * @return  [type]               [return description]
     */
    public function getTreeCategoriesAdmin($parent = 0, &$tree = [], $categories = null, &$st = '')
    {
        $categories = $categories ?? $this->getListCategoryGroupByParentAdmin();
        $categoriesTitle =  $this->getListTitleAdmin();
        $tree = $tree ?? [];
        $lisCategory = $categories[$parent] ?? [];
        if ($lisCategory) {
            foreach ($lisCategory as $category) {
                $tree[$category['id']] = $st . ($categoriesTitle[$category['id']]??'');
                if (!empty($categories[$category['id']])) {
                    $st .= '--';
                    $this->getTreeCategoriesAdmin($category['id'], $tree, $categories, $st);
                    $st = '';
                }
            }
        }
        return $tree;
    }


    /**
     * Get array title category
     * user for admin 
     *
     * @return  [type]  [return description]
     */
    public static function getListTitleAdmin()
    {
        $tableDescription = (new ShopCategoryDescription)->getTable();
        $table = (new Category)->getTable();
        if (lc_config_global('cache_status') && lc_config_global('cache_category')) {
            if (!Cache::has(session('adminStoreId').'_cache_category_'.lc_get_locale())) {
                if (self::$getListTitleAdmin === null) {
                    self::$getListTitleAdmin = self::join($tableDescription, $tableDescription.'.category_id', $table.'.id')
                    ->where('lang', lc_get_locale())
                    ->pluck('title', 'id')
                    ->toArray();
                }
                lc_set_cache(session('adminStoreId').'_cache_category_'.lc_get_locale(), self::$getListTitleAdmin);
            }
            return Cache::get(session('adminStoreId').'_cache_category_'.lc_get_locale());
        } else {
            if (self::$getListTitleAdmin === null) {
                self::$getListTitleAdmin = self::join($tableDescription, $tableDescription.'.category_id', $table.'.id')
                ->where('lang', lc_get_locale())
                ->pluck('title', 'id')
                ->toArray();
            }
            return self::$getListTitleAdmin;
        }
    }


    /**
     * Get array title category
     * user for admin 
     *
     * @return  [type]  [return description]
     */
    public static function getListCategoryGroupByParentAdmin()
    {
        if (self::$getListCategoryGroupByParentAdmin === null) {
            self::$getListCategoryGroupByParentAdmin = self::select('id', 'parent')
            ->get()
            ->groupBy('parent')
            ->toArray();
        }
        return self::$getListCategoryGroupByParentAdmin;
    }


    /**
     * Create a new category
     *
     * @param   array  $dataInsert  [$dataInsert description]
     *
     * @return  [type]              [return description]
     */
    public static function createCategoryAdmin(array $dataInsert) {
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
        return ShopCategoryDescription::create($dataInsert);
    }

}
