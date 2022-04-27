<?php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class ShopSubscribe extends Model
{
    public $table = 'shop_subscribe';
    protected $guarded      = [];
    protected $connection = LC_CONNECTION;
    /*
    Get store
    */
    public function store()
    {
        return $this->belongsTo(ShopStore::class, 'store_id', 'id')->with('descriptionsCurrentLang');
    }
}
