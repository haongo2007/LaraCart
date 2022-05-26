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
    public function palette()
    {
        return $this->hasMany(ShopAttributePalette::class, 'attribute_id','id');
    }

    public function activePalette()
    {
        return $this->hasOne(ShopAttributePalette::class, 'attribute_id','id')->where('active',1);
    }

    public function Children()
    {
        return $this->hasMany(self::class, 'parent','id')->with('palette');
    }
}
