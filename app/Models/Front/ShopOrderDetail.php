<?php
#black-cart/Core/Front/Models/ShopOrderDetail.php
namespace App\Models\Front;

use App\Models\Front\ShopProduct;
use Illuminate\Database\Eloquent\Model;

class ShopOrderDetail extends Model
{
    protected $table = 'shop_order_detail';
    protected $connection = LC_CONNECTION;
    protected $guarded = [];
    public function order()
    {
        return $this->belongsTo(ShopOrder::class, 'order_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
    }

    public function updateDetail($id, $data)
    {
        return $this->where('id', $id)->update($data);
    }
    public function addNewDetail($data)
    {
        if ($data) {
            //Update stock, sold
            ShopProduct::updateStock($data['product_id'], $data['qty']);
            return $this->create($data);
        }
    }
}
