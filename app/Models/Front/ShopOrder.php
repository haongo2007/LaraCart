<?php
namespace App\Models\Front;

use App\Models\Front\ShopOrderDetail;
use App\Models\Front\ShopOrderHistory;
use App\Models\Front\ShopOrderTotal;
use App\Models\Front\ShopProduct;
use DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\Front\ModelTrait;
use App\Models\Admin\Admin;

class ShopOrder extends Model
{
    use ModelTrait;
    
    public $table = 'shop_order';
    protected $guarded = [];

    protected  $lc_order_profile = 0; // 0: all, 1: only user's order
    public $lc_status = 1;
    
    public function details()
    {
        return $this->hasMany(ShopOrderDetail::class, 'order_id', 'id');
    }
    public function orderTotal()
    {
        return $this->hasMany(ShopOrderTotal::class, 'order_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Front\ShopCustomer', 'customer_id', 'id');
    }
    public function orderStatus()
    {
        return $this->hasOne(ShopOrderStatus::class, 'id', 'status');
    }
    public function paymentStatus()
    {
        return $this->hasOne(ShopPaymentStatus::class, 'id', 'payment_status');
    }
    public function history()
    {
        return $this->hasMany(ShopOrderHistory::class, 'order_id', 'id');
    }
    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(function ($order) {
            foreach ($order->details as $key => $orderDetail) {
                $item = ShopProduct::find($orderDetail->product_id);
                //Update stock, sold
                ShopProduct::updateStock($orderDetail->product_id, -$orderDetail->qty);

            }
            $order->details()->delete(); //delete order details
            $order->orderTotal()->delete(); //delete order total
            $order->history()->delete(); //delete history

        });
    }
    /**
     * A order has and belongs to many stores.
     *
     * @return BelongsToMany
     */
    public function stores()
    {
        return $this->belongsTo(ShopStore::class, 'store_id', 'id')->with('descriptionsCurrentLang');
    }

/**
 * Update status order
 * @param  [type]  $orderId
 * @param  integer $status
 * @param  string  $msg
 */
    public function updateStatus($orderId, $status = 0, $msg = '')
    {
        $customer = auth()->user();
        $uID = $customer->id ?? 0;
        $order = $this->find($orderId);
        if ($order) {
            //Update status
            $order->update(['status' => (int) $status]);

            //Add history
            $dataHistory = [
                'order_id' => $orderId,
                'content' => $msg,
                'customer_id' => $uID,
                'order_status_id' => $status,
            ];
            $this->addOrderHistory($dataHistory);
        }
    }

//Scort
    public function scopeSort($query, $sortBy = null, $sortOrder = 'asc')
    {
        $sortBy = $sortBy ?? 'sort';
        return $query->orderBy($sortBy, $sortOrder);
    }

    /**
     * Create new order
     * @param  [array] $dataOrder
     * @param  [array] $dataTotal
     * @param  [array] $arrCartDetail
     * @return [array]
     */
    public function createOrder($dataOrder, $dataTotal, $arrCartDetail)
    {
        //Process escape
        $dataOrder     = lc_clean($dataOrder);
        $dataTotal     = lc_clean($dataTotal);
        $arrCartDetail = lc_clean($arrCartDetail);

        try {
            DB::connection(config('const.LC_CONNECTION'))->beginTransaction();
            $uID = $dataOrder['customer_id'];
            $currency = $dataOrder['currency'];
            $exchange_rate = $dataOrder['exchange_rate'];

            //Insert order
            $order = ShopOrder::create($dataOrder);
            $orderID = $order->id;
            //End insert order

            //Insert order total
            foreach ($dataTotal as $key => $row) {
                array_walk($row, function (&$v, $k) {
                    return $v = lc_clean($v);
                    }
                );
                $row['order_id'] = $orderID;
                $row['created_at'] = date('Y-m-d H:i:s');
                $dataTotal[$key] = $row;
            }
            ShopOrderTotal::insert($dataTotal);
            //End order total

            //Order detail
            foreach ($arrCartDetail as $cartDetail) {
                $pID = $cartDetail['product_id'];
                $product = ShopProduct::find($pID);
                
                //Check product flash sale over stock
                if (function_exists('lc_product_flash_check_over') && !lc_product_flash_check_over($pID, $cartDetail['qty'])) {
                    return $return = ['error' => 1, 'msg' => trans('cart.over', ['item' => $product->sku])];
                }

                //If product out of stock
                if (!lc_config('product_buy_out_of_stock') && $product->stock < $cartDetail['qty']) {
                    return $return = ['error' => 1, 'msg' => trans('cart.over', ['item' => $product->sku])];
                }
                //

                $cartDetail['order_id'] = $orderID;
                $cartDetail['currency'] = $currency;
                $cartDetail['exchange_rate'] = $exchange_rate;
                $cartDetail['sku'] = $product->sku;
                $cartDetail['tax'] = $dataOrder['tax'];
                $cartDetail['store_id'] = $cartDetail['store_id'];
                $this->addOrderDetail($cartDetail);

                //Update stock flash sale
                if (function_exists('lc_product_flash_update_stock')) {
                    lc_product_flash_update_stock($pID, $cartDetail['qty']);
                }

                //Update stock and sold
                ShopProduct::updateStock($pID, $cartDetail['qty']);
            }
            //End order detail

            //Add order store - MultiVendorPro
            if (function_exists('lc_vendor_create_order')) {
                lc_vendor_create_order($orderID);
            }

            //Add history
            $dataHistory = [
                'admin_id' => 0,
                'order_id' => $orderID,
                'content' => 'New order',
                'customer_id' => $uID,
                'order_status_id' => $order->status,
            ];
            $this->addOrderHistory($dataHistory);

            //Process Discount
            $codeDiscount = session('Discount') ?? '';
            if ($codeDiscount) {
                if (!empty(lc_config('Discount'))) {
                    $moduleClass = lc_get_class_plugin_controller($code = 'Total', $key = 'Discount');
                    $returnModuleDiscount = (new $moduleClass)->apply($codeDiscount, $uID, $msg = 'Order #' . $orderID);
                    $arrReturnModuleDiscount = json_decode($returnModuleDiscount, true);
                    if ($arrReturnModuleDiscount['error'] == 1) {
                        if ($arrReturnModuleDiscount['msg'] == 'error_code_not_exist') {
                            $msg = trans('promotion.process.invalid');
                        } elseif ($arrReturnModuleDiscount['msg'] == 'error_code_cant_use') {
                            $msg = trans('promotion.process.over');
                        } elseif ($arrReturnModuleDiscount['msg'] == 'error_code_expired_disabled') {
                            $msg = trans('promotion.process.expire');
                        } elseif ($arrReturnModuleDiscount['msg'] == 'error_user_used') {
                            $msg = trans('promotion.process.used');
                        } elseif ($arrReturnModuleDiscount['msg'] == 'error_uID_input') {
                            $msg = trans('promotion.process.customer_id_invalid');
                        } elseif ($arrReturnModuleDiscount['msg'] == 'error_login') {
                            $msg = trans('promotion.process.must_login');
                        } else {
                            $msg = trans('promotion.process.undefined');
                        }
                        return redirect(lc_route('cart'))->with(['error_discount' => $msg]);
                    }
                }
            }
            // End process Discount

            DB::connection(config('const.LC_CONNECTION'))->commit();
            $return = ['error' => 0, 'orderID' => $orderID, 'msg' => ""];
        } catch (\Throwable $e) {
            DB::connection(config('const.LC_CONNECTION'))->rollBack();
            $return = ['error' => 1, 'msg' => $e->getMessage()];
        }
        return $return;
    }

/**
 * Add order history
 * @param [array] $dataHistory
 */
    public function addOrderHistory($dataHistory)
    {
        return ShopOrderHistory::create($dataHistory);
    }

/**
 * Add order detail
 * @param [type] $dataDetail [description]
 */
    public function addOrderDetail($dataDetail)
    {
        return ShopOrderDetail::create($dataDetail);
    }


    /**
     * Start new process get data
     *
     * @return  new model
     */
    public function start() {
        if($this->lc_order_profile) {
            $obj = (new ShopOrder);
            $obj->lc_order_profile = 1;
            return $obj;
        } else {
            return new ShopOrder;
        }
    }

    /**
     * Get order detail
     *
     * @param   [int]  $orderID 
     *
     */
    public function getDetail($orderID)
    {
        if(empty($orderID)) {
            return null;
        }
        $customer = auth()->user();
        if ($customer) {
            return $this->where('id', $orderID)
                ->where('customer_id', $customer->id)
                ->first();
        } else {
            return null;
        }

    }

    /**
     * Disable only user's order mode
     */
    public function setOrderProfile() {
        $this->lc_order_profile = 1;
        $this->lc_status = 'all' ;
        return $this;
    }

    public function profile() {
        $this->setOrderProfile();
        return $this;
    }

    /**
     * Get list order new
     */
    public function getOrderNew() {
        $this->lc_status = 1;
        return $this;
    }

    /**
     * Get list order processing
     */
    public function getOrderProcessing() {
        $this->lc_status = 2;
        return $this;
    }

    /**
     * Get list order hold
     */
    public function getOrderHold() {
        $this->lc_status = 3;
        return $this;
    }

    /**
     * Get list order canceld
     */
    public function getOrderCanceled() {
        $this->lc_status = 4;
        return $this;
    }

    /**
     * Get list order done
     */
    public function getOrderDone() {
        $this->lc_status = 5;
        return $this;
    }

    /**
     * Get list order failed
     */
    public function getOrderFailed() {
        $this->lc_status = 6;
        return $this;
    }

    /**
     * build Query
     */
    public function buildQuery() {
        $customer = auth()->user();
        if ($this->lc_order_profile == 1) {
            if(!$customer) {
                return null;
            }
            $uID = $customer->id;
            $query = $this->with('orderTotal')->where('customer_id', $uID);
        } else {
            $query = $this->with('orderTotal')->with('details');
        }

        if ($this->lc_status !== 'all') {
            $query = $query->where('status', $this->lc_status);
        }

        if (count($this->lc_moreWhere)) {
            foreach ($this->lc_moreWhere as $key => $where) {
                if(count($where)) {
                    $query = $query->where($where[0], $where[1], $where[2]);
                }
            }
        }

        if ($this->random) {
            $query = $query->inRandomOrder();
        } else {
            if (is_array($this->lc_sort) && count($this->lc_sort)) {
                foreach ($this->lc_sort as  $rowSort) {
                    if(is_array($rowSort) && count($rowSort) == 2) {
                        $query = $query->sort($rowSort[0], $rowSort[1]);
                    }
                }
            }
        }

        return $query;
    }

    /**
     * Update value balance, received when order capture full money with payment method
     *
     * @return  [type]  [return description]
     */
    public function processPaymentPaid() {
        $total = $this->total;
        $this->balance = 0;
        $this->received = -$total;
        $this->save();
        (new ShopOrderTotal)
            ->where('order_id', $this->id)
            ->where('code', 'received')
            ->update(['value' =>  -$total]);
    }
}
