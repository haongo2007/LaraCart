<?php
#black-cart/Core/Front/Models/ShopProductPromotion.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;
use App\Models\Front\ShopProduct;
class ShopProductPromotion extends Model
{
    public $table = 'shop_product_promotion';
    protected $guarded    = [];
    protected $primaryKey = ['product_id'];
    public $incrementing  = false;
    protected $connection = LC_CONNECTION;

    public function product()
    {
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
    }

}
