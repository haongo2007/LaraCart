<?php
namespace App\Models\Front;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class ShopDiscount extends Model
{
    public $timestamps    = false;
    public $table = 'shop_discount';
    protected $connection = LC_CONNECTION;
    protected $guarded    = [];
    protected $dates      = ['expires_at'];

    /*
    Get store
    */
    public function store()
    {
        return $this->belongsTo(ShopStore::class, 'store_id', 'id')->with('descriptionsCurrentLang');
    }
    
    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(function ($model) {
                $model->users()->detach();
            }
        );
    }
    /**
     * Get the users who is related promocode.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(ShopCustomer::class, 'shop_discount_customer', 'discount_id','customer_id')
            ->withPivot('used_at', 'log');
    }

    /**
     * Query builder to get expired promotion codes.
     *
     * @param $query
     * @return mixed
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')->whereDate('expires_at', '<=', Carbon::now());
    }

    /**
     * Check if code is expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->expires_at ? Carbon::now()->gte($this->expires_at) : false;
    }

    /**
     * [getPromotionByCode description]
     *
     * @param   [type]  $code  [$code description]
     *
     * @return  [type]         [return description]
     */
    public function getPromotionByCode($code,$store_id = LC_ID_ROOT) {
        $promocode = $this
        ->where('store_id', $store_id)
        ->where('code', $code)
        ->first();

        return $promocode;
    }

    /**
     * [check description]
     * @param  [type]  $code       [description]
     * @param  [type]  $uID        [description]
     * @return [type]  $store_id   [description]
     */
    public function check($code, $uID = null,$store_id)
    {
        $uID = (int) $uID;
        $promocode = (new self)->getPromotionByCode($code,$store_id);
        if ($promocode === null) {
            return false;
        }
        //Check user  login
        if ($promocode->login && !$uID) {
            return false;
        }

        if ($promocode->limit == 0 || $promocode->limit <= $promocode->used) {
            return false;
        }

        if ($promocode->status == 0 || $promocode->isExpired()) {
            return false;
        }
        if ($promocode->login) {
            //check if this user has already used this code already
            $arrUsers = [];
            foreach ($promocode->users as $value) {
                $arrUsers[] = $value->pivot->customer_id;
            }
            if (in_array($uID, $arrUsers)) {
                return false;
            }
        }

        return $promocode;
    }
    /**
     * [apply description]
     * @param  [type] $code  [description]
     * @param  [type] $msg   [description]
     * @return [type] $store [description]
     */
    public function apply($code, $uID = null, $store,$msg = '')
    {
        //check code valid
        $checkCode = $this->check($code, $uID,$store);

        if ($checkCode) {
            $promocode = (new ShopDiscount)->getPromotionByCode($code,$store);
            if($promocode) {
                try {
                    // increment used
                    $promocode->used += 1;
                    $promocode->save();
    
                    $promocode->users()->attach($uID, [
                        'used_at' => Carbon::now(),
                        'log' => $msg,
                    ]);
                    return true;
                } catch (\Throwable $e) {
                    return json_encode(['error' => 1, 'msg' => $e->getMessage()]);
                }
            } else {
                return false;
            }

        } else {
            return $checkCode;
        }

    }

    public function getValue($total,$check)
    {
        if ($check['type'] == 'percent') {
            $value = $total * $check['reward'] / 100;
        } else {
            $value = lc_currency_value($check['reward']);
        }
        $value = ($value > $total) ? -$total : -$value;
        return $value;
    }

    public function createDiscount($orderID,$code,$customer_id = 0,$storeId) {
        $msg = 'Order #'.$orderID;
        return $this->apply($code, $customer_id,$storeId,$msg);
    }
}
