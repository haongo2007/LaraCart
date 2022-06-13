<?php
namespace App\Models\Front;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Front\ShopProduct;
use App\Models\Front\ShopProductPromotion;

class ShopProductFlashSale extends Model
{
    public $timestamps    = false;
    public $table = 'shop_product_flash';
    protected $connection = LC_CONNECTION;
    protected $guarded    = [];
    const ITEM_PER_PAGE = 15;

    public function product()
    {
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
    }
    public function promotion()
    {
        return $this->belongsTo(ShopProductPromotion::class, 'product_id','product_id');
    }

    /**
     * Get produc flash sale
     *
     * @return void
     */
    public function getProduct($pid) {
        $select = $this->table.'.*, pr.price_promotion, pr.date_start, pr.date_end, pr.status_promotion';
        return 
        $this->leftjoin('shop_product_promotion as pr', 'pr.product_id', $this->table.'.product_id')
        ->selectRaw($select)
        ->where($this->table.'.id', $pid)
        ->first();
    }

    /**
     * Get all product flash sale
     *
     * @return void
     */
    public function getAllProductFlashSale(array $dataSearch,$paginate = false) {
        $limit   = lc_clean($dataSearch['limit'] ?? self::ITEM_PER_PAGE);
        $storeId = $dataSearch['storeId'];
        $productFlash = self::whereHas('promotion' , function ($query)
        {
            $query->where('date_start', '<=', date('Y-m-d'))->where('date_end', '>=', date('Y-m-d'))->where('status_promotion',1);
        })->where('store_id',$storeId);
        if ($paginate) {
            $productFlash = $productFlash->paginate($limit);
        } else {
            $productFlash = $productFlash->limit($limit)->get();
        }
        return $productFlash;
    }

    /**
     * Get name product not group
     *
     * @return void
     */
    public function getAllProductNotGroup() {
        return (new ShopProduct)
            ->leftJoin('shop_product_description', 'shop_product_description.product_id', 'shop_product.id')
            ->where('shop_product_description.lang', bc_get_locale())
            ->whereIn('kind', [LC_PRODUCT_SINGLE, LC_PRODUCT_BUILD])
            ->get()
            ->pluck('name', 'id');
    }
    

    /**
     * Get product flash
     *
     * @return void
     */
    public function getProductFlash($limit = 8, $paginate = false) {
        $productFlash = (new ShopProduct)
        ->select('shop_product.*', 'pf.sold as pf_sold', 'pf.stock as pf_stock', 'pr.price_promotion as price_promotion')
        ->join('shop_product_flash as pf', 'pf.product_id', 'shop_product.id')
        ->join('shop_product_promotion as pr', 'pr.product_id', 'pf.product_id')
        ->where('pr.status_promotion', 1)
        ->where('pr.date_start', '<=', date('Y-m-d'))
        ->where('pr.date_end', '>=', date('Y-m-d'))
        ->whereColumn('pf.sold', '<', 'pf.stock')
        ->orderBy('pf.sort', 'asc');
        if ($paginate) {
            $productFlash = $productFlash->paginate($limit);
        } else {
            $productFlash = $productFlash->limit($limit)->get();
        }
        return $productFlash;
    }

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(function ($item) {
            //Delete promotion
            (new ShopProductPromotion)->where('product_id', $item->product_id)->delete();

            }
        );
    }
}
