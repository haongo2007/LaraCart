<?php
#black-cart/Core/Front/Models/ShopProductBuild.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;
use App\Models\Front\ShopProduct;

class ShopProductBuild extends Model
{
    protected $primaryKey = ['build_id', 'product_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = 'shop_product_build';
    protected $connection = LC_CONNECTION;
    public function product()
    {
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
    }
}
