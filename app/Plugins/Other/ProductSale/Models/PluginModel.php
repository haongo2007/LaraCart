<?php
#App\Plugins\Other\ProductSale\Models\PluginModel.php
namespace App\Plugins\Other\ProductSale\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use BlackCart\Core\Front\Models\ShopProduct;
use BlackCart\Core\Front\Models\ShopProductPromotion;

class PluginModel extends Model
{
    public $timestamps    = false;
    public $table = BC_DB_PREFIX.'shop_product_flash';
    protected $connection = BC_CONNECTION;
    protected $guarded    = [];

    public function product()
    {
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
    }

    public function uninstallExtension()
    {
        if (Schema::hasTable($this->table)) {
            Schema::drop($this->table);
        }
        return ['error' => 0, 'msg' => 'uninstall success'];
    }

    public function installExtension()
    {
        $this->uninstallExtension();

        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unique();
            $table->integer('stock');
            $table->integer('sold');
            $table->integer('sort');
        });

        return ['error' => 0, 'msg' => 'install success'];
    }

    /**
     * Get produc flash sale
     *
     * @return void
     */
    public function getProduct($pid) {
        $select = $this->table.'.*, pr.price_promotion, pr.date_start, pr.date_end, pr.status_promotion';
        return 
        $this->leftjoin(BC_DB_PREFIX.'shop_product_promotion as pr', 'pr.product_id', $this->table.'.product_id')
        ->selectRaw($select)
        ->where($this->table.'.id', $pid)
        ->first();
    }

    /**
     * Get all product flash sale
     *
     * @return void
     */
    public function getAllProductFlashSale() {
        $select = $this->table.'.*, pr.price_promotion, pr.date_start, pr.date_end, pr.status_promotion';
        return 
        $this->leftjoin(BC_DB_PREFIX.'shop_product_promotion as pr', 'pr.product_id', $this->table.'.product_id')
        ->selectRaw($select)
        ->paginate(20);
    }

    /**
     * Get name product not group
     *
     * @return void
     */
    public function getAllProductNotGroup() {
        return (new ShopProduct)
            ->leftJoin(BC_DB_PREFIX . 'shop_product_description', BC_DB_PREFIX . 'shop_product_description.product_id', BC_DB_PREFIX.'shop_product.id')
            ->where(BC_DB_PREFIX . 'shop_product_description.lang', bc_get_locale())
            ->whereIn('kind', [BC_PRODUCT_SINGLE, BC_PRODUCT_BUILD])
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
        ->select(BC_DB_PREFIX.'shop_product.*', 'pf.sold as pf_sold', 'pf.stock as pf_stock')
        ->join(BC_DB_PREFIX.'shop_product_flash as pf', 'pf.product_id', BC_DB_PREFIX.'shop_product.id')
        ->join(BC_DB_PREFIX.'shop_product_promotion as pr', 'pr.product_id', 'pf.product_id')
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
