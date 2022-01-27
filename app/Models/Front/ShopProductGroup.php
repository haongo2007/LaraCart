<?php
#black-cart/Core/Front/Models/ShopProductGroup.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;
use App\Models\Front\ShopProduct;

class ShopProductGroup extends Model
{
    protected $primaryKey = ['group_id', 'product_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = 'shop_product_group';
    protected $connection = LC_CONNECTION;

    public function product()
    {
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
    }
}
