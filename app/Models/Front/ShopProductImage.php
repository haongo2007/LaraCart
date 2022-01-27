<?php
#black-cart/Core/Front/Models/ShopProductImage.php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class ShopProductImage extends Model
{
    public $timestamps = false;
    public $table = 'shop_product_image';
    protected $guarded = [];
    protected $connection = LC_CONNECTION;

/*
Get thumb
 */
    public function getThumb()
    {
        return bc_image_get_path_thumb($this->image);
    }

/*
Get image
 */
    public function getImage()
    {
        return bc_image_get_path($this->image);

    }
}
