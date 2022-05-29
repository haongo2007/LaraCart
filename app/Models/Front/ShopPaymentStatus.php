<?php
#black-cart/Core/Front/Models/ShopPaymentStatus.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ShopPaymentStatus extends Model
{
    public $timestamps  = false;
    public $table = 'shop_payment_status';
    protected $guarded   = [];
    protected static $listStatus = null;
    const ITEM_PER_PAGE = 15;


    public function getPaymentStatusListAdmin($searchParams='')
    {
        $keyword = $searchParams['keyword'] ?? '';
        $limit = $searchParams['limit'] ?? self::ITEM_PER_PAGE;
        $storeId = Arr::get($searchParams, 'store_id', '');
        
        $PmStatusList = new self;
        if ($keyword) {
            $PmStatusList = $PmStatusList->where('name', 'like', '%' . $keyword . '%');
        }

        if (count(session('adminStoreId')) == 1 || $storeId) {
            if (!$storeId) {
                $storeId = session('adminStoreId');
            }
            $PmStatusList = $PmStatusList->where('store_id',$storeId);
        }
        $PmStatusList = $PmStatusList->orderBy('id', 'desc');
        
        return $PmStatusList->paginate($limit);
    }
    public static function getIdAll($storeId)
    {
        if (!self::$listStatus) {
            self::$listStatus = self::where('store_id',$storeId)->get();
        }
        return self::$listStatus;
    }
    public function stores()
    {
        return $this->belongsTo(ShopStore::class, 'store_id', 'id')->with('descriptionsCurrentLang');
    }
    
    public function getFirstIdDefault($storeId = 0)
    {
        $default = self::where('store_id',$storeId)->first();
        if(!$default){
            $default = self::where('store_id',0)->first();
        }
        return $default->id;
    }
}
