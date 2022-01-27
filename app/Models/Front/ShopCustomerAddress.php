<?php
#black-cart/Core/Front/Models/ShopCustomerAddress.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class ShopCustomerAddress extends Model
{
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = 'shop_customer_address';
    protected $connection = LC_CONNECTION;
}
