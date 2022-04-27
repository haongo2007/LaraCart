<?php

namespace App\Models\Admin;

use App\Models\Front\ShopOrder;
use App\Models\Front\ShopOrderTotal;
use App\Models\Front\ShopStore;
use Cache;
use Carbon\Carbon;

class Order extends ShopOrder
{
    const ITEM_PER_PAGE = 20;

    public static $mapStyleStatus = [
        '1' => 'info', //new
        '2' => 'primary', //processing
        '3' => 'warning', //Hold
        '4' => 'danger', //Cancel
        '5' => 'success', //Success
        '6' => 'default', //Failed
    ];

    /**
     * Get order detail in admin
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  [type]       [return description]
     */
    public static function getOrderAdmin($id) {
        return self::with(['stores','details', 'orderTotal','history' => function ($q)
        {
            $q->with('Staff:id,fullname')->orderBy('add_date','DESC');
        }])
        ->where('id', $id)
        ->first();
    }

    /**
     * Get list order in admin
     *
     * @param   [array]  $dataSearch  [$dataSearch description]
     *
     * @return  [type]               [return description]
     */
    public static function getOrderListAdmin(array $dataSearch) {
        $order_id       = $dataSearch['order_id'] ?? '';
        $customer_name  = $dataSearch['customer_name'] ?? '';
        $customer_email = $dataSearch['customer_email'] ?? '';
        $customer_phone = $dataSearch['customer_phone'] ?? '';
        $from         = $dataSearch['from'] ?? '';
        $to           = $dataSearch['to'] ?? '';
        $sort_order   = $dataSearch['sort_order'] ?? 'id__desc';
        $status       = $dataSearch['status'] ?? [];
        $limit        = $dataSearch['limit'] ?? self::ITEM_PER_PAGE;
        $arrSort = [
            'id__desc'         => trans('order.admin.sort_order.id_desc'),
            'id__asc'          => trans('order.admin.sort_order.id_asc'),
            'subtotal__desc'      => trans('order.admin.sort_order.price_desc'),
            'subtotal__asc'       => trans('order.admin.sort_order.price_asc'),
            'created_at__desc' => trans('order.admin.sort_order.date_desc'),
            'created_at__asc'  => trans('order.admin.sort_order.date_asc'),
        ];
        $orderList = (new ShopOrder);
        
        $orderList = $orderList->whereIn('store_id', session('adminStoreId'));

        if($status && is_array($status)) {
            $orderList = $orderList->whereIn('status', $status);
        }
        if ($order_id) {
            $orderList = $orderList->where(function ($sql) use($order_id){
                $sql->Where('id', $order_id );
            });
        }

        if ($customer_email) {
            $orderList = $orderList->where(function ($sql) use($customer_email){
                $sql->Where('email', 'like' , '%'.$customer_email.'%' );
            });
        }

        if ($customer_phone) {
            $orderList = $orderList->where(function ($sql) use($customer_phone){
                $sql->Where('phone', 'like' , '%'.$customer_phone.'%' );
            });
        }

        if ($customer_name) {
            $orderList = $orderList->where(function ($sql) use($customer_name){
                $sql->Where('first_name', 'like' , '%'.$customer_name.'%' )
                ->orwhere('last_name','like','%'.$customer_name.'$');
            });
        }
        

        if ($from && $to) {
            $orderList = $orderList->where(function ($sql) use($from,$to){
                $sql->Where([['created_at', '>=' , Carbon::parse($from)->format('Y-m-d H:i:s')],['created_at', '<=' , Carbon::parse($to)->format('Y-m-d H:i:s')]]);
            });
        }

        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $orderList = $orderList->sort($field, $sort_field);
        } else {
            $orderList = $orderList->sort('id', 'desc');
        }
        $orderList = $orderList->paginate($limit);

        return $orderList;
    }

    /**
     * Insert order total
     *
     * @param   [type]  $dataInsert  [$dataInsert description]
     *
     * @return  [type]               [return description]
     */
    public static function insertOrderTotal($dataInsert) {
        $dataInsert = lc_clean($dataInsert);
        return ShopOrderTotal::insert($dataInsert);
    }

    /**
     * Get item order total, then re-sort
     * @param  [int] $order_id [description]
     * @return [array]           [description]
     */
    public static function getOrderTotal($orderId)
    {
        $objects = ShopOrderTotal::where('order_id', $orderId)->get()->toArray();
        usort($objects, function ($a, $b) {
            if ($a['sort'] > $b['sort']) {
                return 1;
            } else {
                return -1;
            }
        });
        return $objects;
    }

    /**
     * Get row order total
     *
     * @param   [type]  $rowId  [$rowId description]
     *
     * @return  [type]          [return description]
     */
    public static function getRowOrderTotal($rowId) {
        return ShopOrderTotal::find($rowId);
    }

    /**
     * Update data when row of total change
     * @param  [array] $row [description]
     * @return [void]
     */
    public static function updateRowOrderTotal($dataRowTotal)
    {
        //Udate dataRowTotal
        $upField = ShopOrderTotal::find($dataRowTotal['id']);
        $upField->value = $dataRowTotal['value'];
        $upField->text = $dataRowTotal['text'];
        $upField->updated_at = date('Y-m-d H:i:s');
        $upField->save();
        $order_id = $upField->order_id;

        //Sum value item order total
        $totalData = ShopOrderTotal::where('order_id', $order_id)->get();
        $total = $discount = $shipping = $received = 0;
        foreach ($totalData as $key => $value) {
            if ($value['code'] === 'subtotal') {
                $total += $value['value'];
            }
            if ($value['code'] === 'tax') {
                $total += $value['value'];
            }
            if ($value['code'] === 'discount') {
                $discount += $value['value'];
                $total += $value['value'];
            }
            if ($value['code'] === 'shipping') {
                $shipping += $value['value'];
                $total += $value['value'];
            }
            if ($value['code'] === 'received') {
                $received += $value['value'];
            }
        }

        //Update total
        $updateTotal = ShopOrderTotal::where('order_id', $order_id)
            ->where('code', 'total')
            ->first();
        $updateTotal->value = $total;
        $updateTotal->save();

        //Update Order
        $order = ShopOrder::find($order_id);
        $order->discount = $discount;
        $order->shipping = $shipping;
        $order->received = $received;
        $order->balance = $total + $received;
        $order->total = $total;
        $order->save();
    }


    /**
     * Update new sub total
     * @param  [int] $orderId [description]
     * @return [type]           [description]
     */
    public static function updateSubTotal($orderId)
    {
        try {
            $order = self::getOrderAdmin($orderId);
            $details = $order->details;
            $tax = $subTotal = 0;
            if($details->count()) {
                foreach ($details as $detail) {
                    $tax +=$detail->tax;
                    $subTotal +=$detail->total_price;
                }
            }
            $order->subtotal = $subTotal;
            $order->tax = $tax;
            $total = $subTotal + $tax + $order->discount + $order->shipping;
            $balance = $total + $order->received;
            $payment_status = 0;
            if ($balance == $total) {
                $payment_status = ShopOrderTotal::NOT_YET_PAY; //Not pay
            } elseif ($balance < 0) {
                $payment_status = ShopOrderTotal::NEED_REFUND; //Need refund
            } elseif ($balance == 0) {
                $payment_status = ShopOrderTotal::PAID; //Paid
            } else {
                $payment_status = ShopOrderTotal::PART_PAY; //Part pay
            }
            $order->payment_status = $payment_status;
            $order->total = $total;
            $order->balance = $balance;
            $order->save();

            //Update total
            $updateTotal = ShopOrderTotal::where('order_id', $orderId)
                ->where('code', 'total')
                ->first();
            $updateTotal->value = $total;
            $updateTotal->save();
            
            //Update Subtotal
            $updateSubTotal = ShopOrderTotal::where('order_id', $orderId)
                ->where('code', 'subtotal')
                ->first();
            $updateSubTotal->value = $subTotal;
            $updateSubTotal->save();

            //Update tax
            $updateSubTotal = ShopOrderTotal::where('order_id', $orderId)
            ->where('code', 'tax')
            ->first();
            $updateSubTotal->value = $tax;
            $updateSubTotal->save();

            return 1;
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    /**
     * Get country order in year
     *
     * @return  [type]  [return description]
    */
    public static function getCountryInYear() {
        return self::selectRaw('country, count(id) as count, sum(total) as amount,currency',)
        ->whereRaw('DATE(created_at) >=  DATE_SUB(DATE(NOW()), INTERVAL 12 MONTH)')
        ->groupBy('country')
        ->orderBy('count', 'desc')
        ->get();
    }

    /**
     * Get Sum order total In Week
     *
     * @return  [type]  [return description]
     */
    public static function getSumOrderTotalIn($type = '1 WEEK') {
        return self::selectRaw('DATE_FORMAT(created_at, "%m-%d") AS d,
        SUM(total/exchange_rate) AS total_amount, count(id) AS total_order')
            ->whereRaw('created_at >=  DATE_FORMAT(DATE_SUB(CURRENT_DATE(), INTERVAL '.$type.'), "%Y-%m-%d")')
            ->groupBy('d')->get();
    }

    /**
     * Get Sum order total Custom Time
     *
     * @return  [type]  [return description]
     */
    public static function getSumOrderTotalCustomTime($from = '',$to = '',$storeId = null) {
        $store_table = (new ShopStore)->getTable();
        $order_table = (new Order)->getTable();
        return self::selectRaw('DATE_FORMAT(created_at, "%m-%d") AS d,
        SUM(total/exchange_rate) AS total_amount, 
        count(id) AS total_order,'.$store_table.'.*')
        ->leftJoin($store_table, $order_table.'.store_id', '=', $store_table.'.id')
        ->whereIn('store_id',$storeId)
        ->whereBetween('created_at',[$from,$to])
        ->groupBy('d','store_id')->get();
    }

    /**
     * Get total order of system
     *
     * @return  [type]  [return description]
     */
    public static function getTotalOrder() {
        return self::count();
    }

    /**
     * Get count order new
     *
     * @return  [type]  [return description]
     */
    public static function getCountOrderNew() {
        return self::where('status', 1)
        ->count();
    }

    /**
     * Get list order new
     *
     * @return  [type]  [return description]
     */
    public static function getListOrderNew($numRow) {
        return self::where('status', 1)
        ->take($numRow)->orderBy('status', 'asc')->orderBy('id', 'desc')->get();
    }

    /**
     * Get total order of system
     *
     * @return  [type]  [return description]
     */
    public static function getTopOrder() {
        return self::with('orderStatus')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();
    }
    
}
