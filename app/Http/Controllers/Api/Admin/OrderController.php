<?php
namespace App\Http\Controllers\Api\Admin;

use App\Models\Admin\Admin;
use App\Http\Controllers\Controller;
use App\Models\Front\ShopAttributeGroup;
use App\Models\Front\ShopCountry;
use App\Models\Front\ShopCurrency;
use App\Models\Front\ShopCustomer;
use App\Models\Front\ShopOrderDetail;
use App\Models\Front\ShopOrderStatus;
use App\Models\Front\ShopPaymentStatus;
use App\Models\Front\ShopShippingStatus;
use App\Models\Admin\Customer;
use App\Models\Admin\Order;
use App\Models\Admin\Product;
use App\Http\Resources\OrderCollection;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;
use Illuminate\Http\Request;
use App\Library\ProcessData\Export;
use Validator;

class OrderController extends Controller
{
    public $country;

    public function __construct()
    {
        $this->country        = ShopCountry::getCodeAll();
    }

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        $searchParams = request()->all();
        $data = (new Order)->getOrderListAdmin($searchParams);
        return OrderCollection::collection($data)->additional(['message' => 'Successfully']);
    }

    /**
     * Custom interface.
     *
     * @return Content
     */
    public function getRelation($storeId)
    {
        $paymentMethodTmp = lc_get_plugin_installed('payment', $onlyActive = false);
        foreach ($paymentMethodTmp as $key => $value) {
            $paymentMethod[$key] = lc_language_render($value->detail);
        }
        $shippingMethodTmp = lc_get_plugin_installed('shipping', $onlyActive = false);
        foreach ($shippingMethodTmp as $key => $value) {
            $shippingMethod[$key] = lc_language_render($value->detail);
        }
        $orderStatus            = ShopOrderStatus::getIdAll($storeId);
        $countries              = $this->country;
        $currenciesRate         = ShopCurrency::getListRate();


        $data['currencies']     = ShopCurrency::getListActive($storeId);
        $data['countries']      = $countries;
        $data['orderStatus']    = $orderStatus;
        $data['currenciesRate'] = $currenciesRate;
        $data['paymentMethod']  = $paymentMethod;
        $data['shippingMethod'] = $shippingMethod;
        
        return response()->json(new JsonResponse($data), Response::HTTP_OK);
    }
    /**
     * Post create new item in admin
     * @return [type] [description]
     */
    public function store()
    {
        $data = request()->all();
        $validate = [
            'first_name'      => 'required|max:100',
            'address1'        => 'required|max:100',
            'exchange_rate'   => 'required',
            'currency'        => 'required',
            'status'          => 'required',
            'payment_method'  => 'required',
            'shipping_method' => 'required',
            'email' => 'required',
        ];
        if(lc_config('customer_lastname',$data['storeId'])) {
            $validate['last_name'] = 'required|max:100';
        }
        if(lc_config('customer_address2',$data['storeId'])) {
            $validate['address2'] = 'required|max:100';
        }
        if(lc_config('customer_address3',$data['storeId'])) {
            $validate['address3'] = 'required|max:100';
        }
        if(lc_config('customer_phone',$data['storeId'])) {
            $validate['phone'] = 'required|regex:/^0[^0][0-9\-]{7,13}$/';
        }
        if(lc_config('customer_country',$data['storeId'])) {
            $validate['country'] = 'required|min:2';
        }
        if(lc_config('customer_postcode',$data['storeId'])) {
            $validate['postcode'] = 'required|min:5';
        }
        if(lc_config('customer_company',$data['storeId'])) {
            $validate['company'] = 'required|min:3';
        }
        $messages = [
            'last_name.required'       => trans('validation.required',['attribute'=> trans('cart.last_name')]),
            'first_name.required'      => trans('validation.required',['attribute'=> trans('cart.first_name')]),
            'email.required'           => trans('validation.required',['attribute'=> trans('cart.email')]),
            'address1.required'        => trans('validation.required',['attribute'=> trans('cart.address1')]),
            'address2.required'        => trans('validation.required',['attribute'=> trans('cart.address2')]),
            'address3.required'        => trans('validation.required',['attribute'=> trans('cart.address3')]),
            'phone.required'           => trans('validation.required',['attribute'=> trans('cart.phone')]),
            'country.required'         => trans('validation.required',['attribute'=> trans('cart.country')]),
            'postcode.required'        => trans('validation.required',['attribute'=> trans('cart.postcode')]),
            'company.required'         => trans('validation.required',['attribute'=> trans('cart.company')]),
            'sex.required'             => trans('validation.required',['attribute'=> trans('cart.sex')]),
            'birthday.required'        => trans('validation.required',['attribute'=> trans('cart.birthday')]),
            'email.email'              => trans('validation.email',['attribute'=> trans('cart.email')]),
            'phone.regex'              => trans('customer.phone_regex'),
            'postcode.min'             => trans('validation.min',['attribute'=> trans('cart.postcode')]),
            'country.min'              => trans('validation.min',['attribute'=> trans('cart.country')]),
            'first_name.max'           => trans('validation.max',['attribute'=> trans('cart.first_name')]),
            'email.max'                => trans('validation.max',['attribute'=> trans('cart.email')]),
            'address1.max'             => trans('validation.max',['attribute'=> trans('cart.address1')]),
            'address2.max'             => trans('validation.max',['attribute'=> trans('cart.address2')]),
            'address3.max'             => trans('validation.max',['attribute'=> trans('cart.address3')]),
            'last_name.max'            => trans('validation.max',['attribute'=> trans('cart.last_name')]),
            'birthday.date'            => trans('validation.date',['attribute'=> trans('cart.birthday')]),
            'birthday.date_format'     => trans('validation.date_format',['attribute'=> trans('cart.birthday')]),
            'shipping_method.required' => trans('cart.validation.shippingMethod_required'),
            'payment_method.required'  => trans('cart.validation.paymentMethod_required'),
        ];


        $validator = Validator::make($data, $validate, $messages);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }
        if(!array_key_exists('customer_id', $data)){
            $new_customer = [
                                'first_name'      => $data['first_name'],
                                'last_name'       => $data['last_name'] ?? '',
                                'address1'        => $data['address1'],
                                'address2'        => $data['address2'] ?? '',
                                'address3'        => $data['address3'] ?? '',
                                'country'         => $data['country'] ?? '',
                                'phone'           => $data['phone'] ?? '',
                                'email'           => $data['phone'] ?? '',
                            ];
            $customer = ShopCustomer::createCustomer($new_customer);
            $data['customer_id'] = $customer->id;
        }
        //Create new order
        $dataInsert = [
            'customer_id'     => $data['customer_id'],
            'first_name'      => $data['first_name'],
            'last_name'       => $data['last_name'] ?? '',
            'status'          => $data['status'],
            'currency'        => $data['currency'],
            'address1'        => $data['address1'],
            'address2'        => $data['address2'] ?? '',
            'address3'        => $data['address3'] ?? '',
            'country'         => $data['country'] ?? '',
            'company'         => $data['company'] ?? '',
            'postcode'        => $data['postcode'] ?? '',
            'phone'           => $data['phone'] ?? '',
            'payment_method'  => $data['payment_method'],
            'shipping_method' => $data['shipping_method'],
            'exchange_rate'   => $data['exchange_rate'],
            'email'           => $data['email'],
            'comment'         => $data['comment'],
            'store_id'        => $data['storeId'],
            'shipping_status' => (new ShopShippingStatus)->getFirstIdDefault($data['storeId']),
            'payment_status'  => (new ShopPaymentStatus)->getFirstIdDefault($data['storeId']),
        ];
        $order = Order::create($dataInsert);
        Order::insertOrderTotal([
            ['code' => 'subtotal', 'value' => 0, 'title' => 'Subtotal', 'sort' => 1, 'order_id' => $order->id],
            ['code' => 'tax', 'value' => 0, 'title' => 'Tax', 'sort' => 2, 'order_id' => $order->id],
            ['code' => 'shipping', 'value' => 0, 'title' => 'Shipping', 'sort' => 10, 'order_id' => $order->id],
            ['code' => 'discount', 'value' => 0, 'title' => 'Discount', 'sort' => 20, 'order_id' => $order->id],
            ['code' => 'total', 'value' => 0, 'title' => 'Total', 'sort' => 100, 'order_id' => $order->id],
            ['code' => 'received', 'value' => 0, 'title' => 'Received', 'sort' => 200, 'order_id' => $order->id],
        ]);
        //
        return response()->json(new JsonResponse(), Response::HTTP_OK);

    }

    /**
     * Order detail
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function show($id)
    {
        $order = Order::getOrderAdmin($id);

        if (!$order) {
            return response()->json(new JsonResponse([],'Resources not found!'), Response::HTTP_FORBIDDEN);
        }
        $paymentMethodTmp = lc_get_plugin_installed('payment', $onlyActive = false);
        foreach ($paymentMethodTmp as $key => $value) {
            $paymentMethod[$key] = lc_language_render($value->detail);
        }
        $shippingMethodTmp = lc_get_plugin_installed('shipping', $onlyActive = false);
        foreach ($shippingMethodTmp as $key => $value) {
            $shippingMethod[$key] = lc_language_render($value->detail);
        }

        $data = [
                "order" => $order,
                "statusOrder" => ShopOrderStatus::getIdAll($order->store_id),
                "statusPayment" => ShopPaymentStatus::getIdAll($order->store_id),
                "statusShipping" => ShopShippingStatus::getIdAll($order->store_id),
                'attributesGroup' => ShopAttributeGroup::pluck('name', 'id')->all(),
                'paymentMethod' => $paymentMethod,
                'shippingMethod' => $shippingMethod,
                'countries' => $this->country,
            ];
        return response()->json(new JsonResponse($data), Response::HTTP_OK); 
    }

    /**
     * process update order
     * @return [json]           [description]
     */
    public function update($id,Request $request)
    {
        $id = $id;
        $code = $request->name;
        $value = $request->value;
        $newvalue = $value;
        $dataInvoice = [];
        if ($code == 'shipping' || $code == 'discount' || $code == 'received') {
            $orderTotalOrigin = Order::getRowOrderTotal($id);
            $orderId = $orderTotalOrigin->order_id;
            $oldValue = $orderTotalOrigin->value;
            $order = Order::find($orderId);
            if (!$order) {
                return response()->json(new JsonResponse([],trans('admin.data_not_found_detail', ['msg' => 'order#'.$orderId])), Response::HTTP_FORBIDDEN); 
            }
            $dataRowTotal = [
                'id' => $id,
                'code' => $code,
                'value' => $value,
                'text' => lc_currency_render_symbol($value, $order->currency),
            ];
            Order::updateRowOrderTotal($dataRowTotal);

            $orderUpdated = Order::find($orderId);

            $dataInvoice = [
                'total'     => $orderUpdated->total,
                'tax'       => $orderUpdated->tax,
                'shipping'  => $orderUpdated->shipping,
                'discount'  => $orderUpdated->discount,
                'subtotal'  => $orderUpdated->subtotal,
                'received'  => $orderUpdated->received,
                'balance'   => $orderUpdated->balance,
            ];

        } else {
            $orderId = $id;
            $order = Order::where('id',$orderId)->first();
            if (!$order) {
                return response()->json(new JsonResponse([],trans('admin.data_not_found_detail', ['msg' => 'order#'.$orderId])), Response::HTTP_FORBIDDEN); 
            }

            $oldValue = $order->{$code};
            if ($code == 'status') {
                foreach (ShopOrderStatus::getIdAll($order->store_id) as $key => $stt) {
                    if ($stt['id'] == $value) {
                        $newvalue = $stt['name'];
                    }
                    if ($stt['id'] == $order->status) {
                        $oldValue = $stt['name'];
                    }
                }
            }

            if ($code == 'shipping_status') {
                foreach (ShopShippingStatus::getIdAll($order->store_id) as $key => $stt) {
                    if ($stt['id'] == $value) {
                        $newvalue = $stt['name'];
                    }
                    if ($stt['id'] == $order->shipping_status) {
                        $oldValue = $stt['name'];
                    }
                }
            }

            if ($code == 'payment_status') {
                foreach (ShopPaymentStatus::getIdAll($order->store_id) as $key => $stt) {
                    if ($stt['id'] == $value) {
                        $newvalue = $stt['name'];
                    }
                    if ($stt['id'] == $order->payment_status) {
                        $oldValue = $stt['name'];
                    }
                }
            }
            $order->update([$code => $value]);
        }
        //Add history
        $dataHistory = [
            'order_id' => $orderId,
            'content' => 'Change <b>' . trans('order.order_'.$code) . '</b> from <span style="color:#ff4949"> ' . $oldValue . ' </span> to <span style="color:#1890ff"> ' . $newvalue . ' </span>',
            'admin_id' => Admin::user()->id,
            'order_status_id' => $order->status,
        ];
        (new Order)->addOrderHistory($dataHistory);

        //data to render
        $dataHistory['staff']['fullname'] = Admin::user()->fullname;
        $dataHistory['add_date'] = date('Y-m-d H:i:s');
        return response()->json(new JsonResponse(['history' => $dataHistory, 'invoice' => $dataInvoice],trans('order.admin.update_success')), Response::HTTP_OK); 
    }

    /**
     * [postAddItem description]
     * @param   [description]
     * @return [type]           [description]
     */
    public function postAddProduct(Request $request)
    {
        $addIds     = $request->product_id;
        $add_price  = (float)$request->price;
        $add_qty    = (float)$request->qty;
        $add_att    = $request->add_att;
        $add_tax    = (float)$request->tax['value'];
        $orderId    = $request->order_id;

        $item = [];
        $order = Order::find($orderId);
        $other_price = 0;
        if ($request->attribute) {
            $attributes = json_decode($request->attribute);
            foreach ($attributes as $key => $attribute) {
                $other_price += array_reduce((array)$attribute, function($carry, $item) use ( $order)
                {   
                    $price = explode('__',$item);
                    return $carry + ((int) $price[1] * $order->exchange_rate);
                });
            }
        }
        //where exits id and qty > 0
        if ($addIds && $add_qty) {
            $product = Product::find($addIds);
            $product_name = $product->getText()->name;
            if (!$product) {
                return response()->json(new JsonResponse([],trans('admin.data_not_found_detail', ['msg' => '#'.$id]) ), Response::HTTP_FORBIDDEN); 
            }
            $total_price = ($add_price + $other_price) * $add_qty ;
            $item = array(
                'order_id' => $orderId,
                'product_id' => $addIds,
                'name' => $product_name,
                'qty' => $add_qty,
                'price' => $add_price,
                'total_price' => $total_price,
                'sku' => $product->sku,
                'tax' => $add_tax,
                'attribute' => $request->attribute ?? null,
                'currency' => $order->currency,
                'exchange_rate' => $order->exchange_rate,
                'created_at' => date('Y-m-d H:i:s'),
            );
        }
        if ($item) {
            try {
                $order_detail_id = (new ShopOrderDetail)->addNewDetail($item);
                $order_detail_id = $order_detail_id->id;
                //Add history
                $dataHistory = [
                    'order_id' => $orderId,
                    'content' => 'Add product <span style="color:#1890ff"> ' . $product_name . ' </span>',
                    'admin_id' => Admin::user()->id,
                    'order_status_id' => $order->status,
                ];
                (new Order)->addOrderHistory($dataHistory);

                Order::updateSubTotal($orderId);

                $orderUpdated = Order::find($orderId);

                $dataInvoice = [
                    'total'     => $orderUpdated->total,
                    'tax'       => $orderUpdated->tax,
                    'shipping'  => $orderUpdated->shipping,
                    'discount'  => $orderUpdated->discount,
                    'subtotal'  => $orderUpdated->subtotal,
                    'received'  => $orderUpdated->received,
                    'balance'   => $orderUpdated->balance,
                ];
                $dataHistory['staff']['fullname'] = Admin::user()->fullname;
                $dataHistory['add_date'] = date('Y-m-d H:i:s');
                //end update total price
                return response()->json(new JsonResponse(['history' => $dataHistory,'invoice' => $dataInvoice,'order_detail_id' => $order_detail_id],trans('order.admin.update_success')), Response::HTTP_OK); 
            } catch (\Throwable $e) {
                return response()->json(new JsonResponse([],$e->getMessage() ), Response::HTTP_FORBIDDEN); 
            }

        }
        return response()->json(new JsonResponse([]), Response::HTTP_FORBIDDEN); 
    }

    /**
     * [postDeleteItem description]
     * @param   [description]
     * @return [type]           [description]
     */
    public function postDeleteProduct(Request $request)
    {
        try {
            $id = $request->id ?? 0;
            $itemDetail = (new ShopOrderDetail)->where('id', $id)->first();
            if (!$itemDetail) {
                return response()->json(new JsonResponse([],trans('admin.data_not_found_detail', ['msg' => 'detail #'.$id]) ),Response::HTTP_FORBIDDEN);
            }
            $orderId = $itemDetail->order_id;
            $order = Order::find($orderId);
            if (!$order) {
                return response()->json(new JsonResponse([],trans('admin.data_not_found_detail', ['msg' => 'order #'.$orderId])),Response::HTTP_FORBIDDEN);
            }

            $pId = $itemDetail->product_id;
            $qty = $itemDetail->qty;
            $itemDetail->delete(); //Remove item from shop order detail
            //Update total price
            Order::updateSubTotal($orderId);
            //Update stock, sold
            Product::updateStock($pId, -$qty);

            //Add history
            $dataHistory = [
                'order_id' => $orderId,
                'content' => '<span style="color:#ff4949"> Remove </span> Product <b>ID #' . $pId.'</b>',
                'admin_id' => Admin::user()->id,
                'order_status_id' => $order->status,
            ];
            (new Order)->addOrderHistory($dataHistory);

            $orderUpdated = Order::find($orderId);

            $dataInvoice = [
                'total'     => $orderUpdated->total,
                'tax'       => $orderUpdated->tax,
                'shipping'  => $orderUpdated->shipping,
                'discount'  => $orderUpdated->discount,
                'subtotal'  => $orderUpdated->subtotal,
                'received'  => $orderUpdated->received,
                'balance'   => $orderUpdated->balance,
            ];

            $dataHistory['staff']['fullname'] = Admin::user()->fullname;
            $dataHistory['add_date'] = date('Y-m-d H:i:s');
            return response()->json(new JsonResponse(['history' => $dataHistory,'invoice' => $dataInvoice],trans('order.admin.update_success')), Response::HTTP_OK); 
        } catch (\Throwable $e) {
            return response()->json(new JsonResponse([],$e->getMessage()) ,Response::HTTP_FORBIDDEN);
        }
    }

    /*
    Delete list order ID
    Need mothod destroy to boot deleting in model
    */
    public function destroy($id)
    {
        $arrID = explode(',', $id);
        $arrDontPermission = [];
        foreach ($arrID as $key => $id) {
            if(!$this->checkPermisisonItem($id)) {
                $arrDontPermission[] = $id;
            }
        }
        if (count($arrDontPermission)) {
            return response()->json(new JsonResponse([],trans('admin.remove_dont_permisison'.': '.json_encode($arrDontPermission))) ,Response::HTTP_FORBIDDEN);
        } else {
            Order::destroy($arrID);
            return response()->json(new JsonResponse([]),Response::HTTP_OK);
        }
    }

    /*
    Export order detail order
    */
    public function exportExcel()
    {
        $type = request('type');
        $orderId = request('id') ?? 0;
        $order = Order::getOrderAdmin($orderId);
        if ($order) {
            $data                    = array();
            $data['name']            = $order['first_name'] . ' ' . $order['last_name'];
            $data['address']         = $order['address1'] . ', ' . $order['address2'] . ', ' . $order['address3'].', '.$order['country'];
            $data['phone']           = $order['phone'];
            $data['email']           = $order['email'];
            $data['comment']         = $order['comment'];
            $data['payment_method']  = $order['payment_method'];
            $data['shipping_method'] = $order['shipping_method'];
            $data['created_at']      = $order['created_at'];
            $data['currency']        = $order['currency'];
            $data['exchange_rate']   = $order['exchange_rate'];
            $data['subtotal']        = $order['subtotal'];
            $data['tax']             = $order['tax'];
            $data['shipping']        = $order['shipping'];
            $data['discount']        = $order['discount'];
            $data['total']           = $order['total'];
            $data['received']        = $order['received'];
            $data['balance']         = $order['balance'];
            $data['id']              = $order->id;
            $data['details'] = [];

            $attributesGroup =  ShopAttributeGroup::pluck('name', 'id')->all();

            if ($order->details) {
                foreach ($order->details as $key => $detail) {
                    $arrAtt = json_decode($detail->attribute, true);
                    if($arrAtt) {
                        $htmlAtt = '';
                        foreach ($arrAtt as $groupAtt => $att) {
                            $htmlAtt .= $attributesGroup[$groupAtt] .':'.lc_render_option_price($att, $order['currency'], $order['exchange_rate']);
                        }
                        $name = $detail->name.'('.strip_tags($htmlAtt).')';
                    } else {
                        $name = $detail->name;
                    }
                    $data['details'][] = [
                        $key + 1, $detail->sku, $name, $detail->qty, $detail->price, $detail->total_price,
                    ];
                }
            }
            $options = ['filename' => 'Order ' . $orderId];
            return Export::export($type, $data, $options);

        } else {
            return response()->json(new JsonResponse([],trans('admin.data_not_found')) ,Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Check permisison item
     */
    public function checkPermisisonItem($id) {
        return Order::getOrderAdmin($id);
    }

}
