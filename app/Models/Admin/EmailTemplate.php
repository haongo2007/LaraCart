<?php

namespace App\Models\Admin;

use App\Models\Front\ShopEmailTemplate;

class EmailTemplate extends ShopEmailTemplate
{
    const ACTIVE = ['1'];
    const ORDER = 'id__desc';
    const ITEM_PER_PAGE = 15;
    protected static $getListTitleAdmin = null;
    protected static $getListEmailTemplateGroupByParentAdmin = null;
    /**
     * Get news detail in admin
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  [type]       [return description]
     */
    public static function getEmailTemplateAdmin($id) {
        return self::where('id', $id)
        ->whereIn('store_id', session('adminStoreId'))
        ->first();
    }

    /**
     * Get list news in admin
     *
     * @param   [array]  $dataSearch  [$dataSearch description]
     *
     * @return  [type]               [return description]
     */
    public static function getEmailTemplateListAdmin(array $dataSearch) {
        $keyword          = lc_clean($dataSearch['keyword'] ?? '');
        $status           = lc_clean($dataSearch['status'] ?? self::ACTIVE);
        $sort             = lc_clean($dataSearch['sort'] ?? self::ORDER);
        $limit            = lc_clean($dataSearch['limit'] ?? self::ITEM_PER_PAGE);

        $arrSort = [
            'id__desc',
            'id__asc',
            'name__desc',
            'name__asc',
        ];

        $newsList = (new ShopEmailTemplate)
            ->whereIn('store_id', session('adminStoreId'));

        if ($keyword) {
            $newsList = $newsList->where(function ($sql) use ($keyword){
                $sql->where('name', 'like', '%' . $keyword . '%');
            });
        }

        if (!is_null($status) && is_array($status)) {
            $newsList = $newsList->whereIn('status',$status);
        }
        
        if ($sort && in_array($sort, $arrSort)) {
            $field = explode('__', $sort)[0];
            $sort_field = explode('__', $sort)[1];
            $newsList = $newsList->orderBy($field, $sort_field);
        } else {
            $newsList = $newsList->orderBy('id', 'desc');
        }

        $newsList = $newsList->paginate($limit);

        return $newsList;
    }

    /**
     * Create a new news
     *
     * @param   array  $dataInsert  [$dataInsert description]
     *
     * @return  [type]              [return description]
     */
    public static function createEmailTemplateAdmin(array $dataInsert) {

        return self::create($dataInsert);
    }
}
