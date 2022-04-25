<?php
#black-cart/Core/Front/Models/ShopPageDescription.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class ShopPageDescription extends Model
{
    protected $primaryKey = ['lang', 'page_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = 'shop_page_description';
    protected $connection = LC_CONNECTION;
}
