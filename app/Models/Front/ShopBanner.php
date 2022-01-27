<?php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;
use App\Models\Front\ModelTrait;
class ShopBanner extends Model
{
    use ModelTrait;

    public $table = BC_DB_PREFIX.'shop_banner';
    protected $guarded = [];
    protected $connection = BC_CONNECTION;

    protected  $bc_type = 'all'; // all or interger
    protected  $bc_store = 0; // 1: only produc promotion,
    /*
    Get thumb
    */
    public function getThumb()
    {
        return bc_image_get_path_thumb($this->image);
    }

    /*
    Get image
    */
    public function getImage()
    {
        return bc_image_get_path($this->image);

    }
    //Scort
    public function scopeSort($query, $sortBy = null, $sortOrder = 'asc')
    {
        $sortBy = $sortBy ?? 'sort';
        return $query->orderBy($sortBy, $sortOrder);
    }

    /**
     * Get info detail
     *
     * @param   [int]  $id     
     * @param   [int]  $status 
     *
     */
    public function getDetail($id) {

        return $this->where('id', (int)$id)->where('status', 1)
        ->where('store_id', config('app.storeId'))
        ->first();
    }

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(function ($banner) {
        });
    }


    /**
     * Start new process get data
     *
     * @return  new model
     */
    public function start() {
        return new ShopBanner;
    }

    /**
     * Set type
     */
    public function setType($type) {
        $this->bc_type = $type;
        return $this;
    }

    /**
     * Get banner
     */
    public function getBanner() {
        $this->setType('banner');
        return $this;
    }

    /**
     * Get banner
     */
    public function getBannerStore() {
        $this->setType('banner-store');
        return $this;
    }

    /**
     * Get background
     */
    public function getBackground() {
        $this->setType('background');
        $this->setLimit(1);
        return $this;
    }

    /**
     * Get background
     */
    public function getBackgroundStore() {
        $this->setType('background-store');
        $this->setLimit(1);
        return $this;
    }

    /**
     * Get banner
     */
    public function getBreadcrumb() {
        $this->setType('breadcrumb');
        $this->setLimit(1);
        return $this;
    }

    /**
     * Get banner
     */
    public function getBreadcrumbStore() {
        $this->setType('breadcrumb-store');
        $this->setLimit(1);
        return $this;
    }

    /**
     * Get banner
     */
    public function getBannerProductDetail() {
        $this->setType('product-detail');
        $this->setLimit(1);
        return $this;
    }
    /**
     * Get banner
     */
    public function getBannerCart() {
        $this->setType('cart');
        $this->setLimit(1);
        return $this;
    }
    /**
     * Set store id
     *
     */
    public function setStore($id) {
        $this->bc_store = (int)$id;
        return $this;
    }

    /**
     * build Query
     */
    public function buildQuery() {
        $query = $this;

        $query = $query->where('status', 1);

        if ($this->bc_type !== 'all') {
            $query = $query->where('type', $this->bc_type);
        }

        if (count($this->bc_moreWhere)) {
            foreach ($this->bc_moreWhere as $key => $where) {
                if(count($where)) {
                    $query = $query->where($where[0], $where[1], $where[2]);
                }
            }
        }

        //Get product active for store
        if (!empty($this->bc_store)) {
            //If sepcify store id
            $query = $query->where($this->getTable().'.store_id', $this->bc_store);
        } else {
            // If stor ID is 1, will get product of all stores
            $query = $query->where($this->getTable().'.store_id', config('app.storeId'));
        }
        //End store

        if ($this->bc_random) {
            $query = $query->inRandomOrder();
        } else {
            $ckeckSort = false;
            if (is_array($this->bc_sort) && count($this->bc_sort)) {
                foreach ($this->bc_sort as  $rowSort) {
                    if (is_array($rowSort) && count($rowSort) == 2) {
                        if ($rowSort[0] == 'sort') {
                            $ckeckSort = true;
                        }
                        $query = $query->sort($rowSort[0], $rowSort[1]);
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

        return $query;
    }

}
