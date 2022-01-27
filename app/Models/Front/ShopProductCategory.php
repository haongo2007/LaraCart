<?php
#black-cart/Core/Front/Models/ShopProductCategory.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class ShopProductCategory extends Model
{
    protected $primaryKey = ['category_id', 'product_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = 'shop_product_category';
    protected $connection = LC_CONNECTION;
}
