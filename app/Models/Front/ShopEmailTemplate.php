<?php
#black-cart/Core/Front/Models/ShopEmailTemplate.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class ShopEmailTemplate extends Model
{
    public $timestamps = false;
    public $table = BC_DB_PREFIX.'shop_email_template';
    protected $guarded = [];
    protected $connection = BC_CONNECTION;

}
