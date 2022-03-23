<?php
namespace App\Models\Admin;

use App\Models\Front\ShopStore;
use App\Models\Front\ShopStoreDescription;
use Illuminate\Support\Arr;

class Store extends ShopStore
{
    const ITEM_PER_PAGE = 15;
    const ORDER = 'id__desc';
    const ACTIVE = ['1'];
    
    /**
     * Get all template used
     *
     * @return  [type]  [return description]
     */
    public static function getAllTemplateUsed() {
        return self::pluck('template')->all();
    }

    public static function insertDescription(array $data) {
        return ShopStoreDescription::insert($data);
    }

    /**
     * Update description
     *
     * @param   array  $data  [$data description]
     *
     * @return  [type]        [return description]
     */
    public static function updateDescription(array $data) {
        $checkDes = ShopStoreDescription::where('store_id', $data['storeId'])
        ->where('lang', $data['lang'])
        ->first();
        if($checkDes) {
            return ShopStoreDescription::where('store_id', $data['storeId'])
            ->where('lang', $data['lang'])
            ->update([$data['name'] => $data['value']]);
        } else {
            return ShopStoreDescription::insert(
                [
                    'store_id' => $data['storeId'],
                    'lang' => $data['lang'],
                    $data['name'] => $data['value'],
                ]
            );
        }

    }
    /**
     * Get list store in admin
     *
     * @param   [array]  $dataSearch  [$dataSearch description]
     *
     * @return  [type]               [return description]
     */
    public static function getStoreListAdmin(array $dataSearch) {

        $limit = Arr::get($dataSearch, 'limit', self::ITEM_PER_PAGE);
        $sort  = Arr::get($dataSearch, 'sort', self::ORDER);
        $status= Arr::get($dataSearch, 'status', self::ACTIVE);
        $title = Arr::get($dataSearch, 'name', '');
        $arrSort = [
            'id__desc' => trans('category.admin.sort_order.id_desc'),
            'id__asc' => trans('category.admin.sort_order.id_asc'),
            'title__desc' => trans('category.admin.sort_order.title_desc'),
            'title__asc' => trans('category.admin.sort_order.title_asc'),
        ];

        $tableDescription = (new ShopStoreDescription)->getTable();

        $storeList     = (new ShopStore);
        
        $storeList = $storeList->leftJoin($tableDescription, $tableDescription . '.store_id', $storeList->getTable() . '.id')
            ->where($tableDescription . '.lang', lc_get_locale());
        if ($title) {
            $storeList = $storeList->where(function ($sql) use($tableDescription, $title){
                $sql->where($tableDescription . '.title', 'like', '%' . $title . '%');
            });
        }

        if (!is_null($status) && is_array($status)) {
            $storeList = $storeList->whereIn('status',$status);
        }

        if ($sort && array_key_exists($sort, $arrSort)) {
            $field = explode('__', $sort)[0];
            $sort_field = explode('__', $sort)[1];
            // $storeList = $storeList->sort($field, $sort_field);
        } else {
            $storeList = $storeList->sort('id', 'desc');
        }

        $storeList = $storeList->paginate($limit);

        return $storeList;
    }
}
