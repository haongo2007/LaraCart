<?php

namespace App\Models\Admin;

use App\Models\Front\ShopProduct;
use App\Models\Front\ShopProductDescription;
use App\Models\Front\ShopAttributeGroup;
use App\Models\Front\ShopProductCategory;
use Carbon\Carbon;

class Product extends ShopProduct
{
    const ACTIVE = ['1'];
    const ITEM_PER_PAGE = 10;

    /**
     * Get product table name in admin
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  [type]       [return description]
     */
    public static function gettableProduct()
    {
        $tableProduct = (new ShopProduct())->getTable();
        return self::where($tableProduct . '.store_id', session('adminStoreId'));
    }
    /**
     * Get product detail in admin
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  [type]       [return description]
     */
    public static function getProductAdmin($id) {
        $tableProduct = (new ShopProduct())->getTable();
        return ShopProduct::with('descriptions','categories','promotionPrice','attributes','attributes.palette')
        ->where($tableProduct . '.store_id', session('adminStoreId'))
        ->where('id', $id)->first();
    }

    /**
     * Get list product in admin
     *
     * @param   [array]  $dataSearch  [$dataSearch description]
     *
     * @return  [type]               [return description]
     */
    public static function getProductListAdmin(array $dataSearch) {
        $arrSort = [
            'id__desc'   => trans('product.admin.sort_order.id_desc'),
            'id__asc'    => trans('product.admin.sort_order.id_asc'),
            'name__desc' => trans('product.admin.sort_order.name_desc'),
            'name__asc'  => trans('product.admin.sort_order.name_asc'),
            'created_at__desc' => trans('product.admin.sort_order.created_at_desc'),
            'created_at__asc'  => trans('product.admin.sort_order.created_at_asc'),
            'created_at__desc' => trans('product.admin.sort_order.created_at_desc'),
            'created_at__asc'  => trans('product.admin.sort_order.created_at_asc'),
            'price__desc' => trans('product.admin.sort_order.price_desc'),
            'price__asc'  => trans('product.admin.sort_order.price_asc'),
            'sku__desc' => trans('product.admin.sort_order.sku_desc'),
            'sku__asc'  => trans('product.admin.sort_order.sku_asc'),
        ];
        
        $keyword          = $dataSearch['keyword'] ?? '';
        $categories       = $dataSearch['category'] ?? [];
        $sort_order       = $dataSearch['sort_order'] ?? 'id__desc';
        $price            = $dataSearch['price'] ?? '';
        $filter_price_by  = $dataSearch['filter_price_by'] ?? 'Cost';
        $from             = $dataSearch['from'] ?? '';
        $to               = $dataSearch['to'] ?? '';
        $status           = $dataSearch['status'] ?? self::ACTIVE;
        $limit        = $dataSearch['limit'] ?? self::ITEM_PER_PAGE;

        $tableDescription = (new ShopProductDescription)->getTable();
        $tablePTC         = (new ShopProductCategory)->getTable();
        $tableProduct     = (new ShopProduct)->getTable();
        if ($categories) {
            $args = [];

            foreach ($categories as $key => $category) {
                $whatever = $category;
                $parsed = eval("return " . $whatever . ";");
                array_push($args, $parsed);
            }
            array_walk_recursive($args, function ($value, $key) use (&$category_id){
                $category_id[] = $value;
            }, $category_id);


            $productList = (new ShopProduct)
                ->leftJoin($tableDescription, $tableDescription . '.product_id', $tableProduct . '.id')
                ->join($tablePTC, $tablePTC . '.product_id', $tableProduct . '.id')
                ->whereIn($tablePTC . '.category_id', $category_id)
                ->where($tableProduct . '.store_id', session('adminStoreId'))
                ->where($tableDescription . '.lang', lc_get_locale());
        } else {
            $productList = (new ShopProduct)
                ->leftJoin($tableDescription, $tableDescription . '.product_id', $tableProduct . '.id')
                ->where($tableProduct . '.store_id', session('adminStoreId'))
                ->where($tableDescription . '.lang', lc_get_locale());
        }

        if ($keyword) {
            $productList = $productList->where(function ($sql) use($tableDescription, $tableProduct, $keyword){
                $sql->where($tableDescription . '.name', 'like', '%' . $keyword . '%')
                    ->orWhere($tableDescription . '.keyword', 'like', '%' . $keyword . '%')
                    ->orWhere($tableDescription . '.description', 'like', '%' . $keyword . '%')
                    ->orWhere($tableProduct . '.sku', 'like', '%' . $keyword . '%');
            });
        }
        if (is_array($price)) {
            $price_min = $price[0];
            $price_max = $price[1];
            $filter_price_by = strtolower($filter_price_by);
            $productList = $productList->where([[$tableProduct.'.'.$filter_price_by,'>=',$price_min],[$tableProduct.'.'.$filter_price_by,'<=',$price_max]]);
        }

        if ($from && $to) {
            $productList = $productList->where(function ($sql) use($from,$to){
                $sql->Where([['created_at', '>=' , Carbon::parse($from)->format('Y-m-d H:i:s')],['created_at', '<=' , Carbon::parse($to)->format('Y-m-d H:i:s')]]);
            });
        }

        if($status && is_array($status)) {
            $productList = $productList->whereIn('status', $status);
        }

        $productList->groupBy($tableProduct.'.id');

        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $productList = $productList->sort($field, $sort_field);
        } else {
            $productList = $productList->sort('id', 'desc');
        }
        $productList = $productList->paginate($limit);

        return $productList;
    }

    /**
     * Get list product select in admin
     *
     * @param   array  $dataFilter  [$dataFilter description]
     *
     * @return  []                  [return description]
     */
    public function getProductSelectAdmin(array $dataFilter = []) {
        $keyword          = $dataFilter['keyword'] ?? '';
        $limit            = $dataFilter['limit'] ?? '';
        $kind             = $dataFilter['kind'] ?? [];
        $tableDescription = (new ShopProductDescription)->getTable();
        $tableProduct     = $this->getTable();
        $colSelect = [
            'id',
            'image',
            'sku',
             $tableDescription . '.name'
        ];
        $productList = (new ShopProduct)->select($colSelect)
            ->leftJoin($tableDescription, $tableDescription . '.product_id', $tableProduct . '.id')
            ->where($tableProduct . '.store_id', session('adminStoreId'))
            ->where($tableDescription . '.lang', lc_get_locale());
        if(is_array($kind) && $kind) {
            $productList = $productList->whereIn('kind', $kind);
        }
        if ($keyword) {
            $productList = $productList->where(function ($sql) use($tableDescription, $tableProduct, $keyword){
                $sql->where($tableDescription . '.name', 'like', '%' . $keyword . '%')
                    ->orWhere($tableProduct . '.sku', 'like', '%' . $keyword . '%');
            });
        }

        if($limit) {
            $productList = $productList->limit($limit);
        }
        $productList->groupBy($tableProduct.'.id');
        return $productList->get()->keyBy('id');
    }


    /**
     * Create a new product
     *
     * @param   array  $dataInsert  [$dataInsert description]
     *
     * @return  [type]              [return description]
     */
    public static function createProductAdmin(array $dataInsert) {
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
        return ShopProductDescription::create($dataInsert);
    }

    /**
     * [checkProductValidationAdmin description]
     *
     * @param   [type]$type     [$type description]
     * @param   null  $fieldValue    [$field description]
     * @param   null  $pId      [$pId description]
     * @param   null  $storeId  [$storeId description]
     * @param   null            [ description]
     *
     * @return  [type]          [return description]
     */
    public function checkProductValidationAdmin($type = null, $fieldValue = null, $pId = null, $storeId = null) {
        $storeId = $storeId ? lc_clean($storeId) : session('adminStoreId');
        $type = $type ? lc_clean($type) : 'sku';
        $fieldValue = lc_clean($fieldValue);
        $pId = lc_clean($pId);
        $check =  $this
        ->where($type, $fieldValue)
        ->where($this->getTable() . '.store_id', $storeId);
        if($pId) {
            $check = $check->where('id', '<>', $pId);
        }
        $check = $check->first();

        if($check) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get total product of system
     *
     * @return  [type]  [return description]
     */
    public static function getTotalProduct() {
        return self::count();
    }
    

    /**
     * Render html option price in admin
     *
     * @param   [type]$currency  [$currency description]
     * @param   nul   $rate      [$rate description]
     * @param   null             [ description]
     *
     * @return  [type]           [return description]
     */
    public function renderAttributeDetailsAdmin($currency = nul, $rate = null)
    {
        $html = '';
        $details = $this->attributes()->get()->groupBy('attribute_group_id');
        $groups = ShopAttributeGroup::getListAll();
        foreach ($details as $groupId => $detailsGroup) {
            $html .= '<br><b><label>' . $groups[$groupId] . '</label></b>: ';
            foreach ($detailsGroup as $k => $detail) {
                $valueOption = $detail->name.'__'.$detail->add_price;
                $html .= '<label class="radio-inline"><input ' . (($k == 0) ? "checked" : "") . ' type="radio" name="add_att[' . $this->id . '][' . $groupId . ']" value="' . $valueOption . '">' . lc_render_option_price($valueOption, $currency, $rate) . '</label> ';
            }
        }
        return $html;
    }    
    /**
     * Get Sum order total In Week
     *
     * @return  [type]  [return description]
     */
    public static function getSumProductTotalIn($type = '1 WEEK') {
        return self::selectRaw('DATE_FORMAT(created_at, "%m-%d") AS d, count(id) AS total_product')
            ->whereRaw('created_at >=  DATE_FORMAT(DATE_SUB(CURRENT_DATE(), INTERVAL '.$type.'), "%Y-%m-%d")')
            ->groupBy('d')->get();
    }
    /**
     * Get Sum order total In custom time
     *
     * @return  [type]  [return description]
     */
    public static function getSumProductTotalCustomTime($from = '',$to = '') {
        return self::selectRaw('DATE_FORMAT(created_at, "%m-%d") AS d, count(id) AS total_product')
            ->whereBetween('created_at',[$from,$to])
            ->groupBy('d')->get();
    }
}
