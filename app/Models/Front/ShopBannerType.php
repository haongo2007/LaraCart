<?php
#black-cart/Core/Front/Models/ShopBannerType.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class ShopBannerType extends Model
{
    public $timestamps  = false;
    public $table = 'shop_banner_type';
    protected $guarded   = [];
    protected $connection = LC_CONNECTION;
    
    /*
    Get store
    */
    public function store()
    {
        return $this->belongsTo(ShopStore::class, 'store_id', 'id')->with('descriptionsCurrentLang');
    }
}
