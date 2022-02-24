<?php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class ShopAttributePalette extends Model
{
    public $timestamps = false;
    public $table = 'shop_attribute_palette';
    protected $guarded = [];
    protected $connection = LC_CONNECTION;
}
