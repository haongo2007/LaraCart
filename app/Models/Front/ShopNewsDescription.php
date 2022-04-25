<?php
#black-cart/Core/Front/Models/ShopNewsDescription.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class ShopNewsDescription extends Model
{
    protected $primaryKey = ['lang', 'news_id'];
    public $incrementing = false;
    protected $guarded = [];
    public $timestamps = false;
    public $table = 'shop_news_description';
    protected $connection = LC_CONNECTION;
}
