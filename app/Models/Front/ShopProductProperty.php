<?php
#black-cart/Core/Front/Models/ShopProductProperty.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class ShopProductProperty extends Model
{
    public $timestamps  = false;
    public $table = 'shop_product_property';
    protected $guarded   = [];
    protected $connection = LC_CONNECTION;
}
