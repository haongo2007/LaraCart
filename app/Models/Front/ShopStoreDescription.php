<?php
#black-cart/Core/Front/Models/ShopStoreDescription.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class ShopStoreDescription extends Model
{
    protected $primaryKey = ['lang', 'store_id'];
    public $incrementing = false;
    protected $guarded = [];
    public $timestamps = false;
    public $table = 'admin_store_description';
    protected $connection = LC_CONNECTION;
}
