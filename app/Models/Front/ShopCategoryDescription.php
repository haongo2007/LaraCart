<?php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class ShopCategoryDescription extends Model
{
    protected $primaryKey = ['category_id', 'lang'];
    public $incrementing  = false;
    public $timestamps    = false;
    public $table = 'shop_category_description';
    protected $guarded    = [];
}
