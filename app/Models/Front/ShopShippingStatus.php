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
    public static function getIdAll()
    {
        if (!self::$listStatus) {
            self::$listStatus = self::all();
        }
        return self::$listStatus;
    }
}
