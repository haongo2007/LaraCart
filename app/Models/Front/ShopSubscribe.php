<?php
#black-cart/Core/Front/Models/ShopSubscribe.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class ShopSubscribe extends Model
{
    public $table = BC_DB_PREFIX.'shop_subscribe';
    protected $guarded      = [];
    protected $connection = BC_CONNECTION;
}
