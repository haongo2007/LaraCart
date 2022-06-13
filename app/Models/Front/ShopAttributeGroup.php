<?php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class ShopAttributeGroup extends Model
{
    public $timestamps        = false;
    public $table = 'shop_attribute_group';
    protected $guarded        = [];
    protected static $getListType = null;
    protected static $getListAll = null;
    
    /*
    Get store
    */
    public function stores()
    {
        return $this->belongsTo(ShopStore::class, 'store_id', 'id')->with('descriptionsCurrentLang');
    }
    
    public static function getListAll($storeId)
    {
        if (!self::$getListAll) {
            self::$getListAll = self::pluck('name', 'id')->where('store_id',$storeId)->all();
        }
        return self::$getListAll;
    }

    public static function getListType()
    {
        if (!self::$getListType) {
            self::$getListType = self::all()->keyBy('id');
        }
        return self::$getListType;
    }

    public function attributeDetails()
    {
        return $this->hasMany(ShopProductAttribute::class, 'attribute_group_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($group) {
            $group->attributeDetails()->delete();
        });
    }

}
