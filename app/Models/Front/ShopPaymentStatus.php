<?php
#black-cart/Core/Front/Models/ShopPaymentStatus.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class ShopPaymentStatus extends Model
{
    public $timestamps  = false;
    public $table = 'shop_payment_status';
    protected $guarded   = [];
    protected static $listStatus = null;
    public static function getIdAll()
    {
        if (!self::$listStatus) {
            self::$listStatus = self::all();
        }
        return self::$listStatus;
    }
}
