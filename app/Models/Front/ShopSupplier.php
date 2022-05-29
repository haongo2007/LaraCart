<?php
#black-cart/Core/Front/Models/ShopSupplier.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;
use App\Models\Front\ModelTrait;

class ShopSupplier extends Model
{
    use ModelTrait;

    public $timestamps = false;
    public $table = 'shop_supplier';
    protected $guarded = [];
    private static $getList = null;
    protected $connection = LC_CONNECTION;
    const ITEM_PER_PAGE = 15;


    public function getSupplierListAdmin($searchParams='')
    {
        $keyword = $searchParams['keyword'] ?? '';
        $limit = $searchParams['limit'] ?? self::ITEM_PER_PAGE;
        
        $supplierList = new self;
        if ($keyword) {
            $supplierList = $supplierList->where('name', 'like', '%' . $keyword . '%');
        }
        
        $supplierList = $supplierList->whereIn('store_id', session('adminStoreId'));
        $supplierList = $supplierList->orderBy('id', 'desc');
        
        return $supplierList->paginate($limit);
    }
    /*
    Get store
    */
    public function stores()
    {
        return $this->belongsTo(ShopStore::class, 'store_id', 'id')->with('descriptionsCurrentLang');
    }
    
    public static function getListAll()
    {
        if (self::$getList === null) {
            self::$getList = self::get()->keyBy('id');
        }
        return self::$getList;
    }

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(function ($supplier) {
        });
    }

    /**
     * [getUrl description]
     * @return [type] [description]
     */
    public function getUrl()
    {
        return bc_route('supplier.detail', ['alias' => $this->alias]);
    }

    /*
    *Get thumb
    */
    public function getThumb()
    {
        return bc_image_get_path_thumb($this->image);
    }

    /*
    *Get image
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
     * Get page detail
     *
     * @param   [string]  $key     [$key description]
     * @param   [string]  $type  [id, alias]
     *
     */
    public function getDetail($key, $type = null)
    {
        if(empty($key)) {
            return null;
        }
        if ($type === null) {
            $data = $this->where('id', (int) $key);
        } else {
            $data = $this->where($type, $key);
        }

        $data = $data->where('status', 1)
            ->where('store_id', config('app.storeId'));

        return $data->first();
    }


    /**
     * Start new process get data
     *
     * @return  new model
     */
    public function start() {
        return new ShopSupplier;
    }


    /**
     * build Query
     */
    public function buildQuery() {
        $query = $this->where('status', 1)
        ->where('store_id', config('app.storeId'));

        if (count($this->bc_moreWhere)) {
            foreach ($this->bc_moreWhere as $key => $where) {
                if(count($where)) {
                    $query = $query->where($where[0], $where[1], $where[2]);
                }
            }
        }

        if ($this->bc_random) {
            $query = $query->inRandomOrder();
        } else {
            if (is_array($this->bc_sort) && count($this->bc_sort)) {
                foreach ($this->bc_sort as  $rowSort) {
                    if(is_array($rowSort) && count($rowSort) == 2) {
                        $query = $query->sort($rowSort[0], $rowSort[1]);
                    }
                }
            }
        }

        return $query;
    }
}
