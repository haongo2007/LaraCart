<?php
#black-cart/Core/Front/Models/ShopOrderStatus.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ShopOrderStatus extends Model
{
    public $timestamps     = false;
    public $table = 'shop_order_status';
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
    public function getOrderStatusListAdmin($searchParams)
    {
        $keyword = $searchParams['keyword'] ?? '';
        $limit = $searchParams['limit'] ?? self::ITEM_PER_PAGE;
        $storeId = Arr::get($searchParams, 'store_id', '');
        
        $OrStatusList = new self;
        if ($keyword) {
            $OrStatusList = $OrStatusList->where('name', 'like', '%' . $keyword . '%');
        }

        if (count(session('adminStoreId')) == 1 || $storeId) {
            if (!$storeId) {
                $storeId = session('adminStoreId');
            }
            $OrStatusList = $OrStatusList->where('store_id',$storeId);
        }
        $OrStatusList = $OrStatusList->orderBy('id', 'desc');
        
        return $OrStatusList->paginate($limit);
    }
}
