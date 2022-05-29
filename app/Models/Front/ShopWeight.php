<?php
#black-cart/Core/Front/Models/ShopWeight.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class ShopWeight extends Model
{
    public $timestamps     = false;
    public $table = 'shop_weight';
    protected $connection = LC_CONNECTION;
    protected $guarded           = [];
    protected static $getList = null;
    const ITEM_PER_PAGE = 15;


    public function getWeightListAdmin($searchParams='')
    {
        $keyword = $searchParams['keyword'] ?? '';
        $limit = $searchParams['limit'] ?? self::ITEM_PER_PAGE;
        
        $weightList = new self;
        if ($keyword) {
            $weightList = $weightList->where('name', 'like', '%' . $keyword . '%');
        }
        
        $weightList = $weightList->whereIn('store_id', session('adminStoreId'));
        $weightList = $weightList->orderBy('id', 'desc');
        
        return $weightList->paginate($limit);
    }
    /*
    Get store
    */
    public function stores()
    {
        return $this->belongsTo(ShopStore::class, 'store_id', 'id')->with('descriptionsCurrentLang');
    }
    
    public static function getListAll()
    {
        if (!self::$getList) {
            self::$getList = self::pluck('description', 'name')->all();
        }
        return self::$getList;
    }
}
