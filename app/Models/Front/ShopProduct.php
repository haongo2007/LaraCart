<?php
namespace App\Models\Front;

use App\Models\Front\ShopAttributeGroup;
use App\Models\Front\ShopCategory;
use App\Models\Front\ShopProductCategory;
use App\Models\Front\ShopProductDescription;
use App\Models\Front\ShopProductGroup;
use App\Models\Front\ShopProductPromotion;
use App\Models\Front\ShopTax;
use App\Models\Front\ShopStore;
use App\Models\Front\ShopCustomFieldDetail;
use Illuminate\Database\Eloquent\Model;
use App\Models\Front\ModelTrait;
class ShopProduct extends Model
{
    use ModelTrait;
    public $table = 'shop_product';
    protected  $guarded = [];
    protected  $lc_kind = []; // 0:single, 1:bundle, 2:group
    protected  $lc_property = 'all'; // 0:physical, 1:download, 2:only view, 3: Service
    protected  $lc_promotion = 0; // 1: only produc promotion,
    protected  $lc_store_id = 0; 
    protected  $lc_category_store = 'all'; 
    protected  $lc_array_ID = []; // array ID product
    protected  $lc_category = []; // array category id
    protected  $lc_brand = []; // array brand id
    protected  $lc_supplier = []; // array supplier id
    protected static $storeCode = null;

    
    public function brand()
    {
        return $this->belongsTo(ShopBrand::class, 'brand_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(ShopSupplier::class, 'supplier_id', 'id');
    }

    public function categories()
    {
        return $this->belongsToMany(ShopCategory::class, ShopProductCategory::class, 'product_id', 'category_id');
    }
    public function store()
    {
        return $this->belongsTo(ShopStore::class, 'store_id', 'id')->with('descriptionsCurrentLang');
    }
    public function groups()
    {
        return $this->hasMany(ShopProductGroup::class, 'group_id', 'id');
    }
    public function builds()
    {
        return $this->hasMany(ShopProductBuild::class, 'build_id', 'id');
    }
    public function images()
    {
        return $this->hasMany(ShopProductImage::class, 'product_id', 'id');
    }
    public function img()
    {
        return $this->hasOne(ShopProductImage::class, 'product_id', 'id');
    }
    public function descriptions()
    {
        return $this->hasMany(ShopProductDescription::class, 'product_id', 'id');
    }

    public function promotionPrice()
    {
        return $this->hasOne(ShopProductPromotion::class, 'product_id', 'id');
    }
    public function attributes()
    {
        return $this->hasMany(ShopProductAttribute::class, 'product_id', 'id');
    }
    public function downloadPath()
    {
        return $this->hasOne(ShopProductDownload::class, 'product_id', 'id');
    }
    // Function get category description with default locate

    public function categoriesWithLangDefault() {
        return $this->categories()->with('descriptionsWithLangDefault');
    }
    // end get category description
    //Function get text description 
    public function getText() {
        return $this->descriptions()->where('lang', lc_get_locale())->first();
    }
    public function getName() {
        return $this->getText()->name;
    }
    public function getDescription() {
        return $this->getText()->description;
    }
    public function getKeyword() {
        return $this->getText()->keyword;
    }
    public function getContent() {
        return $this->getText()->content;
    }
    public function getFirstAttributeImage() {
        return $this->attributes()->where('images','<>','null')->first();
    }
    //End  get text description

    /*
    *Get final price
    */
    public function getFinalPrice()
    {
        $promotion = $this->processPromotionPrice();
        if ($promotion != -1) {
            return $promotion;
        } else {
            return $this->price;
        }
    }

    /*
    *Get final price with tax
    */
    public function getFinalPriceTax()
    {
        return lc_tax_price($this->getFinalPrice(), $this->getTaxValue());
    }


    /**
     * [showPrice description]
     * @return [type]           [description]
     */
    public function showPrice($detail = false)
    {
        if (!lc_config('product_price', $this->store_id)) {
            return false;
        }
        $price = $this->price;
        $priceFinal = $this->getFinalPrice();
        // Process with tax
        return  view('templates.'.lc_store('template').'.common.show_price', 
            [
                'price' => $price,
                'priceFinal' => $priceFinal,
                'kind' => $this->kind,
                'detail' => $detail
            ]
        )->render();
    }

    /**
     * Get product detail
     * @param  [string] $key [description]
     * @param  [string] $type id, sku, alias
     * @return [type]     [description]
     */
    public function getDetail($key = null, $type = null, $storeId = null)
    {
        if (empty($key)) {
            return null;
        }
        $tableDescription = (new ShopProductDescription)->getTable();
        // $dataSelect = $this->getTable().'.*, '.$tableDescription.'.*'; 
        $storeId = empty($storeId) ? config('app.storeId') : $storeId;

        if (config('app.storeId') != LC_ID_ROOT) {
            //If the store is not the primary store
            //Cannot view the product in another store
            $storeId = config('app.storeId');
        }

        $product = $this->selectRaw('*')
            ->leftJoin($tableDescription, $tableDescription . '.product_id', $this->getTable() . '.id')
            ->where($this->getTable() . '.store_id', $storeId)
            ->where($tableDescription . '.lang', lc_get_locale());

        if (empty($type)) {            
            $product = $product->where($this->getTable().'.id', (int)$key);  
        } elseif ($type == 'alias') {
            $product = $product->where($this->getTable().'.alias', $key);
        } elseif ($type == 'sku') {
            $product = $product->where($this->getTable().'.sku', $key);
        } else {
            return null;
        }

        $product = $product->where($this->getTable().'.status', 1);
        
        $product = $product
            ->with('images')
            ->with('store')
            ->with('promotionPrice');        
        $product = $product->first();
        return $product;
    }

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(function ($product) {
            $product->images()->delete();
            $product->descriptions()->delete();
            $product->promotionPrice()->delete();
            $product->groups()->delete();
            $product->attributes()->delete();
            $product->downloadPath()->delete();
            $product->builds()->delete();
            $product->categories()->detach();

            //Delete custom field
            (new ShopCustomFieldDetail)
                ->join('shop_custom_field', 'shop_custom_field.id', 'shop_custom_field_detail.custom_field_id')
                ->select('code', 'name', 'text')
                ->where('shop_custom_field_detail.rel_id', $product->id)
                ->where('shop_custom_field.type', 'product')
                ->delete();


            }
        );
    }

    /*
    *Get thumb
    */
    public function getThumb()
    {
        return lc_image_get_path_thumb($this->image);
    }

    /*
    *Get image
    */
    public function getImage()
    {
        return lc_image_get_path($this->image);

    }

    /**
     * [getUrl description]
     * @return [type] [description]
     */
    public function getUrl()
    {
        return lc_route('product.detail', ['alias' => $this->alias, 'storeId' => $this->store_id]);
    }

    /**
     * [getPercentDiscount description]
     * @return [type] [description]
     */
    public function getPercentDiscount()
    {
        return round((($this->price - $this->getFinalPrice()) / $this->price) * 100);
    }

    public function renderAttributeDetails()
    {
        return  view('templates.'.lc_store('template').'.common.render_attribute', 
            [
                'details' => $this->attributes()->get()->groupBy('attribute_group_id'),
                'groups' => ShopAttributeGroup::getListType()->toArray(),
            ]
        );
    }


    //Scort
    public function scopeSort($query, $sortBy = null, $sortOrder = 'asc')
    {
        $sortBy = $sortBy ?? 'id';
        return $query->orderBy($sortBy, $sortOrder);
    }

    /**
    *Condition:
    * -Active
    * -In of stock or allow order out of stock
    * -Date availabe
    * -Not LC_PRODUCT_GROUP
    */
    public function allowSale()
    {
        if (!lc_config('product_price', $this->store_id)) {
            return false;
        }
        if ($this->status &&
            (lc_config('product_preorder', $this->store_id) == 1 || $this->date_available === null || date('Y-m-d H:i:s') >= $this->date_available) 
            && (lc_config('product_buy_out_of_stock', $this->store_id) || $this->stock || empty(lc_config('product_stock', $this->store_id))) 
            && $this->kind != LC_PRODUCT_GROUP
        ) {
            return true;
        } else {
            return false;
        }
    }

    /*
    Check promotion price
    */
    private function processPromotionPrice()
    {
        $promotion = $this->promotionPrice;
        if ($promotion) {
            if (($promotion['date_end'] >= date("Y-m-d") || $promotion['date_end'] === null)
                && ($promotion['date_start'] <= date("Y-m-d") || $promotion['date_start'] === null)
                && $promotion['status_promotion'] = 1) {
                return $promotion['price_promotion'];
            }
        }

        return -1;
    }

    /*
    Upate stock, sold
     */
    public static function updateStock($product_id, $qty_change)
    {
        $item = self::find($product_id);
        if ($item) {
            $item->stock = $item->stock - $qty_change;
            $item->sold = $item->sold + $qty_change;
            $item->save();

            //Process build
            if ($item->kind == LC_PRODUCT_BUILD) {
                foreach ($item->builds as $key => $build) {
                    $productBuild = $build->product;
                    if ($productBuild) {
                        $productBuild->stock -= $qty_change;
                        $productBuild->sold += $qty_change;
                        $productBuild->save();
                    }
                }
            }

        }

    }

    /**
     * Start new process get data
     *
     * @return  new model
     */
    public function start() {
        return new ShopProduct;
    }
    
    /**
     * Set product kind
     */
    private function setKind($kind) {
        if (is_array($kind)) {
            $this->lc_kind = $kind;
        } else {
            $this->lc_kind = array((int)$kind);
        }
        return $this;
    }

    /**
     * Set property product
     */
    private function setVirtual($property) {
        if ($property === 'all') {
            $this->lc_property = $property;
        } else {
            $this->lc_property = (int)$property;
        }
        return $this;
    }

    /**
     * Set array category 
     *
     * @param   [array|int]  $category 
     *
     */
    private function setCategory($category) {
        if (is_array($category)) {
            $this->lc_category = $category;
        } else {
            $this->lc_category = array((int)$category);
        }
        return $this;
    }

    /**
     * Set sub category 
     *
     * @param   [int]  $category 
     *
     */
    private function setCategoryStore($category) {
        $this->lc_category_store = (int)$category;
        return $this;
    }
    /**
     * Set array brand 
     *
     * @param   [array|int]  $brand 
     *
     */
    private function setBrand($brand) {
        if (is_array($brand)) {
            $this->lc_brand = $brand;
        } else {
            $this->lc_brand = array((int)$brand);
        }
        return $this;
    }
    /**
     * Set product promotion 
     *
     */
    private function setPromotion() {
        $this->lc_promotion = 1;
        return $this;
    }

    /**
     * Set store id
     *
     */
    public function setStore($id) {
        $this->lc_store_id = (int)$id;
        return $this;
    }

    /**
     * Set array ID product 
     *
     * @param   [array|int]  $arrID 
     *
     */
    private function setArrayID($arrID) {
        if (is_array($arrID)) {
            $this->lc_array_ID = $arrID;
        } else {
            $this->lc_array_ID = array((int)$arrID);
        }
        return $this;
    }

    
    /**
     * Set array supplier 
     *
     * @param   [array|int]  $supplier 
     *
     */
    private function setSupplier($supplier) {
        if (is_array($supplier)) {
            $this->lc_supplier = $supplier;
        } else {
            $this->lc_supplier = array((int)$supplier);
        }
        return $this;
    }

    /**
     * Product hot
     */
    public function getProductHot() {
        return $this->getProductPromotion();
    }

    /**
     * Product build
     */
    public function getProductBuild() {
        $this->setKind(LC_PRODUCT_BUILD);
        return $this;
    }

    /**
     * Product group
     */
    public function getProductGroup() {
        $this->setKind(LC_PRODUCT_GROUP);
        return $this;
    }

    /**
     * Product single
     */
    public function getProductSingle() {
        $this->setKind(LC_PRODUCT_SINGLE);
        return $this;
    }

    /**
     * Get product to array Catgory
     * @param   [array|int]  $arrCategory 
     */
    public function getProductToCategory($arrCategory) {
        $this->setCategory($arrCategory);
        return $this;
    }

    /**
     * Get product to  Catgory store
     * @param   [int]  $category 
     */
    public function getProductToCategoryStore($category) {
        $this->setCategoryStore($category);
        return $this;
    }

    /**
     * Get product to array Brand
     * @param   [array|int]  $arrBrand 
     */
    public function getProductToBrand($arrBrand) {
        $this->setBrand($arrBrand);
        return $this;
    }
    
    /**
     * Get product to array Supplier
     * @param   [array|int]  $arrSupplier 
     */
    private function getProductToSupplier($arrSupplier) {
        $this->setSupplier($arrSupplier);
        return $this;
    }


    /**
     * Get product latest
     */
    public function getProductLatest() {
        $this->setLimit(10);
        $this->setSort(['id', 'desc']);
        return $this;
    }

    /**
     * Get product last view
     */
    public function getProductLastView() {
        $this->setLimit(10);
        $this->setSort(['date_available', 'desc']);
        return $this;
    }

    /**
     * Get product best sell
     */
    public function getProductBestSell() {
        $this->setLimit(10);
        $this->setSort(['sold', 'desc']);
        return $this;
    }

    /**
     * Get product promotion
     */
    public function getProductPromotion() {
        $this->setLimit(10);
        $this->setPromotion();
        return $this;
    }

    /**
     * Get product from list ID product
     *
     * @param   [array]  $arrID  array id product
     *
     * @return  [type]          [return description]
     */
    public function getProductFromListID($arrID) {
        if (is_array($arrID)) {
            $this->setArrayID($arrID);
        }
        return $this;
    }

    /**
     * build Query
     */
    public function buildQuery() {

        $tableDescription = (new ShopProductDescription)->getTable();
        $tableStore = (new ShopStore)->getTable();

        //description
        $query = $this
            ->leftJoin($tableDescription, $tableDescription . '.product_id', $this->getTable() . '.id')
            ->where($tableDescription . '.lang', lc_get_locale());
        //search keyword
        if ($this->lc_keyword !='') {
            $query = $query->where(function ($sql) use ($tableDescription) {
                $sql->where($tableDescription . '.name', 'like', '%' . $this->lc_keyword . '%')
                    ->orWhere($tableDescription . '.keyword', 'like', '%' . $this->lc_keyword . '%')
                    ->orWhere($tableDescription . '.description', 'like', '%' . $this->lc_keyword . '%')
                    ->orWhere($this->getTable() . '.sku', 'like', '%' . $this->lc_keyword . '%');
            });
        }

        //search price beetween
        if ($this->lc_min_price !='' && $this->lc_max_price != '') {
            $query = $query->where(function ($sql) use ($tableDescription) {
                $sql->where([[self::getTable() . '.price', '>=', $this->lc_min_price],[self::getTable() . '.price', '<=', $this->lc_max_price]]);
            });
        }
        // search by attribute
        if ($this->lc_attribute !='') {
            $tableAttribute = (new ShopProductAttribute)->getTable();
            $query = $query->join($tableAttribute, $this->getTable() . '.id', '=', $tableAttribute . '.product_id')
            ->select($this->getTable().'.*',$tableDescription.'.*')
            ->where($tableAttribute . '.code', 'like','%'.$this->lc_attribute.'%');
        }
        //Promotion
        if ($this->lc_promotion == 1) {
            $tablePromotion = (new ShopProductPromotion)->getTable();
            $query = $query->join($tablePromotion, $this->getTable() . '.id', '=', $tablePromotion . '.product_id')
                ->where($tablePromotion . '.status_promotion', 1)
                ->where(function ($query) use ($tablePromotion) {
                    $query->where($tablePromotion . '.date_end', '>=', date("Y-m-d"))
                        ->orWhereNull($tablePromotion . '.date_end');
                })
                ->where(function ($query) use ($tablePromotion) {
                    $query->where($tablePromotion . '.date_start', '<=', date("Y-m-d"))
                        ->orWhereNull($tablePromotion . '.date_start');
                });
        }

        $query = $query->with('promotionPrice');
        $query = $query->with('store');
            

        if (count($this->lc_category)) {
            $tablePTC = (new ShopProductCategory)->getTable();
            $query = $query->leftJoin($tablePTC, $tablePTC . '.product_id', $this->getTable() . '.id');
            $query = $query->whereIn($tablePTC . '.category_id', $this->lc_category);
        }
        $storeId = $this->lc_store_id ? $this->lc_store_id : config('app.storeId');

        //Process store
        if (!empty($this->lc_store_id) || config('app.storeId') != 1) {
            //If the store is specified or the default is not the primary store
            //Only get products from eligible stores
            $query = $query->where($this->getTable().'.store_id', $storeId);
        }
        //End store

        if (count($this->lc_array_ID)) {
            $query = $query->whereIn($this->getTable().'.id', $this->lc_array_ID);
        }

        $query = $query->where($this->getTable().'.status', 1);

        if ($this->lc_kind !== []) {
            $query = $query->whereIn($this->getTable().'.kind', $this->lc_kind);
        }

        
        if ($this->lc_property !== 'all') {
            $query = $query->where($this->getTable().'.property', $this->lc_property);
        }

        if (count($this->lc_brand)) {
            $query = $query->whereIn($this->getTable().'.brand_id', $this->lc_brand);
        }

        if ($this->lc_category_store !== 'all') {
            $query = $query->where($this->getTable().'.category_store_id', $this->lc_category_store);
        }

        if (count($this->lc_supplier)) {
            $query = $query->whereIn($this->getTable().'.supplier_id', $this->lc_supplier);
        }

        if (count($this->lc_moreWhere)) {
            foreach ($this->lc_moreWhere as $key => $where) {
                if (count($where)) {
                    $query = $query->where($where[0], $where[1], $where[2]);
                }
            }
        }

        if ($this->lc_random) {
            $query = $query->inRandomOrder();
        } else {
            $ckeckSort = false;
            if (is_array($this->lc_sort) && count($this->lc_sort)) {
                foreach ($this->lc_sort as  $rowSort) {
                    if (is_array($rowSort) && count($rowSort) == 2) {
                        if ($rowSort[0] == 'sort') {
                            $ckeckSort = true;
                        }
                        $query = $query->orderBy($this->getTable().'.'.$rowSort[0], $rowSort[1]);
                    }
                }
            }
            //Use field "sort" if haven't above
            if (!$ckeckSort) {
                $query = $query->orderBy($this->getTable().'.sort', 'asc');
            }
            //Default, will sort id
            $query = $query->orderBy($this->getTable().'.id', 'desc');
        }

        //Hidden product out of stock
        if (empty(lc_config('product_display_out_of_stock', $storeId)) && !empty(lc_config('product_stock', $storeId))) {
            $query = $query->where($this->getTable().'.stock', '>', 0);
        }

        return $query;
    }

    /**
     * Get tax ID
     *
     * @return  [type]  [return description]
     */
    public function getTaxId() {
        if (!ShopTax::checkStatus()) {
            return 0;
        }
        if ($this->tax_id == 'auto') {
            return ShopTax::checkStatus();
        } else {
            $arrTaxList = ShopTax::getListAll();
            if ($this->tax_id == 0 || !$arrTaxList->has($this->tax_id)) {
                return 0;
            }
        }
        return $this->tax_id;
    }

    /**
     * Get value tax (%)
     *
     * @return  [type]  [return description]
     */
    public function getTaxValue() {
        $taxId = $this->getTaxId();
        if ($taxId) {
            $arrValue = ShopTax::getArrayValue();
            return $arrValue[$taxId] ?? 0;
        } else {
            return 0;
        }
    }

    /**
     * Go to shop
     *
     * @return  [type]  [return description]
     */
    public function goToStore() {
        return url('store/'.$this->store->code);
    }

    /**
     * Get all custom fields
     *
     * @return void
     */
    public function getCustomFields() {
        $data =  (new ShopCustomFieldDetail)
            ->join('shop_custom_field', 'shop_custom_field.id', 'shop_custom_field_detail.custom_field_id')
            ->select('code', 'name', 'text')
            ->where('shop_custom_field_detail.rel_id', $this->id)
            ->where('shop_custom_field.type', 'product')
            ->where('shop_custom_field.status', '1')
            ->get()
            ->keyBy('code');
        return $data;
    }

    /**
     * Get custom field
     *
     * @return void
     */
    public function getCustomField($code = null) {
        $data =  (new ShopCustomFieldDetail)
            ->join('shop_custom_field', 'shop_custom_field.id', 'shop_custom_field_detail.custom_field_id')
            ->select('code', 'name', 'text')
            ->where('shop_custom_field_detail.rel_id', $this->id)
            ->where('shop_custom_field.type', 'product')
            ->where('shop_custom_field.status', '1');
        if ($code) {
            $data = $data->where('shop_custom_field.code', $code);
        }
        $data = $data->first();
        return $data;
    }
}
