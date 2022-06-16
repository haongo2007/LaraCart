<?php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;
use App\Models\Front\UuidTrait;

class ShopRating extends Model
{
    use UuidTrait;

    public $table = 'product_rating';
    protected $connection = LC_CONNECTION;
    protected $guarded    = [];
    public static $pointData = [];

    public function Customer()
    {
        return $this->hasOne(ShopCustomer::class, 'id', 'customer_id');
    }
    /**
     * [getPointProduct description]
     *
     * @param   [type]  $pId  [$pId description]
     *
     * @return  [type]        [return description]
     */
    public function getPointProduct($pId) {
        return $this->where('product_id', $pId)->where('status', 1)->get();
    }
    

    /**
     * Get point data of product
     *
     * @return void
     */
    public static function getPointData($pId) {
        if (!isset(self::$pointData[$pId])) {
            $pointData = self::selectRaw('count(*) as ct, sum(point) as total')
                ->where('status', 1)
                ->where('product_id', $pId)
                ->groupBy('product_id')
                ->get()
                ->first();
            self::$pointData[$pId] = empty($pointData)?'':$pointData;
        }
        return self::$pointData[$pId];
    }

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(function ($model) {
            //Delete model descrition
        });

        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = lc_generate_id($type = 'product_rating');
            }
        });
    }

}