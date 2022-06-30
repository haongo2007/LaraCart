<?php
#App\Models\Front\ShopShippingStatus.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ShopShippingStatus extends Model
{
    public $timestamps  = false;
    public $table = 'shop_shipping_status';
    protected $guarded           = [];
    protected static $listStatus = null;
    const ITEM_PER_PAGE = 15;

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

    public function getShippingStatusListAdmin($searchParams)
    {
        $keyword = $searchParams['keyword'] ?? '';
        $limit = $searchParams['limit'] ?? self::ITEM_PER_PAGE;
        $storeId = Arr::get($searchParams, 'store_id', '');
        
        $ShStatusList = new self;
        if ($keyword) {
            $ShStatusList = $ShStatusList->where('name', 'like', '%' . $keyword . '%');
        }

        if (count(session('adminStoreId')) == 1 || $storeId) {
            if (!$storeId) {
                $storeId = session('adminStoreId');
            }
            $ShStatusList = $ShStatusList->where('store_id',$storeId);
        }
        $ShStatusList = $ShStatusList->orderBy('id', 'desc');
        
        return $ShStatusList->paginate($limit);
    }
}
