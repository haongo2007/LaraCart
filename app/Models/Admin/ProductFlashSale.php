<?php
namespace App\Models\Admin;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Front\ShopProductFlashSale;

class ProductFlashSale extends ShopProductFlashSale
{
    protected $connection = LC_CONNECTION;
    public $timestamps    = false;
    protected $guarded    = [];
}
