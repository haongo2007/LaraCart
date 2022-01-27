<?php
#black-cart/Core/Front/Models/ShopCustomField.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;
use App\Models\Front\ShopCustomFieldDetail;
class ShopCustomField extends Model
{
    public $timestamps     = false;
    public $table          = 'shop_custom_field';
    protected $connection  = LC_CONNECTION;
    protected $guarded     = [];

    public function details()
    {
        $data  = (new ShopCustomFieldDetail)->where('custom_field_id', $this->id)
            ->get();
        return $data;
    }

    /**
     * Get custom fields
     */
    public function getCustomField($type) {
        return $this->where('type', $type)
            ->where('status', 1)
            ->get();
    }

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
