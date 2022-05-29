<?php
#black-cart/Core/Front/Models/ShopLength.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class ShopLength extends Model
{
    public $timestamps     = false;
    public $table = 'shop_length';
    protected $connection = LC_CONNECTION;
    protected $guarded           = [];
    protected static $getList = null;
    const ITEM_PER_PAGE = 15;


    public function getLengthListAdmin($searchParams='')
    {
        $keyword = $searchParams['keyword'] ?? '';
        $limit = $searchParams['limit'] ?? self::ITEM_PER_PAGE;
        
        $lengthList = new self;
        if ($keyword) {
            $lengthList = $lengthList->where('name', 'like', '%' . $keyword . '%');
        }
        
        $lengthList = $lengthList->whereIn('store_id', session('adminStoreId'));
        $lengthList = $lengthList->orderBy('id', 'desc');
        
        return $lengthList->paginate($limit);
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
