<?php
namespace App\Models\Front;

use Illuminate\Database\Eloquent\Model;

class ShopOrderHistory extends Model
{
    public $table = 'shop_order_history';
    protected $connection = LC_CONNECTION;
	const CREATED_AT = 'add_date';
	const UPDATED_AT = null;
    protected $guarded = [];

    public function Staff()
    {
        return $this->hasOne(\App\Models\Admin\User::class, 'id','admin_id');
    }
}
