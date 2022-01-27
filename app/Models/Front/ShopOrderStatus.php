<?php
#black-cart/Core/Front/Models/ShopOrderStatus.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class ShopOrderStatus extends Model
{
    public $timestamps     = false;
    public $table = 'shop_order_status';
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
