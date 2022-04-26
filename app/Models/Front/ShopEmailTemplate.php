<?php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class ShopEmailTemplate extends Model
{
    public $timestamps = false;
    public $table = 'shop_email_template';
    protected $guarded = [];
    protected $connection = LC_CONNECTION;

    /*
    Get store
    */
    public function store()
    {
        return $this->belongsTo(ShopStore::class, 'store_id', 'id')->with('descriptionsCurrentLang');
    }
}
