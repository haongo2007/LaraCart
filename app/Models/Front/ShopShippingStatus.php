<?php
#App\Models\Front\ShopShippingStatus.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class ShopShippingStatus extends Model
{
    public $timestamps  = false;
    public $table = 'shop_shipping_status';
    protected $guarded           = [];
    protected static $listStatus = null;
    public static function getIdAll($storeId)
    {
        if (!self::$listStatus) {
            self::$listStatus = self::where('store_id',$storeId)->get();
        }
        return self::$listStatus;
    }
    public function stores()
    {
        return $this->belongsTo(ShopStore::class, 'store_id', 'id')->with('descriptionsCurrentLang');
    }
    public function getFirstIdDefault($storeId = 0)
    {
        $default = self::where('store_id',$storeId)->first();
        if(!$default){
            $default = self::where('store_id',0)->first();
        }
        return $default->id;
    }
}
