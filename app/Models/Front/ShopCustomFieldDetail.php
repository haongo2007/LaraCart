<?php
#black-cart/Core/Front/Models/ShopCustomFieldDetail.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;
use Cache;
class ShopCustomFieldDetail extends Model
{
    public $timestamps     = false;
    public $table          = 'shop_custom_field_detail';
    protected $connection  = LC_CONNECTION;
    protected $guarded     = [];

    //Function get text description 
    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(function ($obj) {
            //
        }
        );
    }

}
