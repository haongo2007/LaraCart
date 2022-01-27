<?php
#black-cart/Core/Front/Models/ShopProductAttribute.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class ShopProductAttribute extends Model
{
    public $timestamps = false;
    public $table = 'shop_product_attribute';
    protected $guarded = [];
    protected $connection = LC_CONNECTION;
    public function attGroup()
    {
        return $this->belongsTo(ShopAttributeGroup::class, 'attribute_group_id', 'id');
    }
}
