<?php
#black-cart/Core/Front/Models/ShopTax.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class ShopTax extends Model
{
    public $timestamps = false;
    public $table = 'shop_tax';
    protected $guarded = [];
    protected $connection = LC_CONNECTION;

    private static $getList = null;
    private static $status = null;
    private static $arrayId = null;
    private static $arrayValue = null;


    /*
    Get store
    */
    public function stores()
    {
        return $this->belongsTo(ShopStore::class, 'store_id', 'id')->with('descriptionsCurrentLang');
    }
    
    /**
     * Get list item
     *
     * @return  [type]  [return description]
     */
    public static function getListAll()
    {
        if (self::$getList === null) {
            self::$getList = self::get()->keyBy('id');
        }
        return self::$getList;
    }

    /**
     * Get array ID
     *
     * @return  [type]  [return description]
     */
    public static function getArrayId()
    {
        if (self::$arrayId === null) {
            self::$arrayId = self::pluck('id')->all();
        }
        return self::$arrayId;
    }

    /**
     * Get array value
     *
     * @return  [type]  [return description]
     */
    public static function getArrayValue()
    {
        if (self::$arrayValue === null) {
            self::$arrayValue = self::pluck('value', 'id')->all();
        }
        return self::$arrayValue;
    }


    /**
     * Check status tax
     *
     * @return  [type]  [return description]
     */
    public static function checkStatus() {
        $arrTaxId = self::getArrayId();
        if (self::$status === null) {
            if(!lc_config('product_tax')) {
                $status = 0;
            } else {
                if(!in_array(lc_config('product_tax'), $arrTaxId)) {
                    $status = 0; 
                } else {
                    $status = lc_config('product_tax');
                }
            }
            self::$status = $status;
        }
        return self::$status;
    }

}
