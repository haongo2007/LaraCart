<?php
namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopEmailTemplate;
use App\Models\Front\ShopAttributeGroup;
use App\Models\Front\ShopCountry;
use App\Models\Front\ShopOrder;
use App\Models\Front\ShopOrderTotal;
use App\Models\Front\ShopProduct;
use App\Models\Front\ShopDiscount;
use App\Models\Front\ShopCustomer;
use App\Models\Front\ShopCustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\Front\ShopOrderStatus;
use App\Models\Front\ShopPaymentStatus;
use App\Models\Front\ShopShippingStatus;

class ShopCheckoutController extends Controller
{
    /**
     * Checkout screen
     * @return [view]
     */
    public function getInfo()
    {
        //Shipping method
        $storeId = request()->header('x-store');
        
        $moduleShipping = lc_get_plugin_installed('shipping');
        $sourcesShipping = lc_get_all_plugin('shipping');
        $shippingMethod = array();
        foreach ($moduleShipping as $module) {
            if (array_key_exists($module['key'], $sourcesShipping)) {
                $moduleClass = lc_get_class_plugin_config('shipping', $module['key']);
                $shippingMethod[$module['key']] = (new $moduleClass)->getData();
            }
        }

        //Payment method
        $modulePayment = lc_get_plugin_installed('payment');
        $sourcesPayment = lc_get_all_plugin('payment');
        $paymentMethod = array();
        foreach ($modulePayment as $module) {
            if (array_key_exists($module['key'], $sourcesPayment)) {
                $moduleClass = $sourcesPayment[$module['key']].'\AppConfig';
                $paymentMethod[$module['key']] = (new $moduleClass)->getData();
            }
        }     

        $data = [   
                    'paymentMethod'     => $paymentMethod,
                    'shippingMethod'    => $shippingMethod,
                ];
        return response()->json(new JsonResponse($data), Response::HTTP_OK);
    }


    /**
     * Create new order
     * @return [redirect]
     */
    public function store(Request $request)
    {
        $customer = $request->user();
        $storeId = request()->header('x-store');
        $uID = $customer->id ?? 0;
        //if cart empty
        if (count($request->products) == 0) {
            return response()->json(new JsonResponse([],'Error'), Response::HTTP_FORBIDDEN);
        }
        //Not allow for guest
        if (!lc_config('shop_allow_guest',$storeId) && !$customer) {
            return response()->json(new JsonResponse([],'Must be login'), Response::HTTP_FORBIDDEN);
        } //

        $products = $request->products;
        $shippingAddress = $request->customer;
        $address_process = $request->customer['createAccount'];
        $paymentMethod = $request->paymentMethod;
        $shippingMethod = $request->shippingMethod;
        $tax      = $request->priceTax;

        $received = 0; //sum received
        $discount = array_sum(array_column($request->couponInUse, 'value')) ?? 0; //sum //sum discount

        $variant  = array_sum(array_column($products, 'sumVariantPrice')); //sum variant
        $product  = array_sum(array_column($products, 'sum')); //sum product price
        $subtotal = $product + $variant; //sum subtotal

        $shippingClass = lc_get_class_plugin_config('shipping', $shippingMethod);
        $shipping = (new $shippingClass)->getData();
        $shipping_fee = (int)$subtotal < (int)$shipping->shipping_free || (int)$shipping->shipping_free == 0 ? $shipping->fee : 0 ;//get shipping fee

        $total    = $subtotal + $shipping_fee + $tax + $discount; //end total

        //Process total

        $objects = ShopOrderTotal::getObjectOrderTotal($shippingMethod,$discount);
        $dataTotal = ShopOrderTotal::processDataTotal($objects,$subtotal,$tax);


        $payment_unpaid = ShopOrderStatus::getIdAll($storeId)[0];
        $satus_notsend = ShopPaymentStatus::getIdAll($storeId)[0];
        $order_status_new = ShopShippingStatus::getIdAll($storeId)[0];

        $dataOrder['customer_id']     = $uID;
        $dataOrder['subtotal']        = lc_currency_value($subtotal);
        $dataOrder['shipping']        = lc_currency_value($shipping_fee);
        $dataOrder['discount']        = lc_currency_value($discount);
        $dataOrder['received']        = lc_currency_value($received);
        $dataOrder['tax']             = lc_currency_value($tax);
        $dataOrder['payment_status']  = $payment_unpaid->id;
        $dataOrder['shipping_status'] = $satus_notsend->id;
        $dataOrder['status']          = $order_status_new->id;
        $dataOrder['currency']        = lc_currency_code();
        $dataOrder['exchange_rate']   = lc_currency_rate();
        $dataOrder['total']           = lc_currency_value($total);
        $dataOrder['balance']         = lc_currency_value($total + $received);
        $dataOrder['email']           = $shippingAddress['email'];
        $dataOrder['first_name']      = $shippingAddress['first_name'];
        $dataOrder['payment_method']  = $paymentMethod;
        $dataOrder['shipping_method'] = $shippingMethod;
        $dataOrder['user_agent']      = $request->header('User-Agent');
        $dataOrder['ip']              = $request->ip();
        $dataOrder['created_at']      = date('Y-m-d H:i:s');
        $dataOrder['store_id']        = $storeId;
        $dataOrder['domain']          = request()->header('referer');

        if (!empty($shippingAddress['last_name'])) {
            $dataOrder['last_name']       = $shippingAddress['last_name'];
        }
        if (!empty($shippingAddress['first_name_kana'])) {
            $dataOrder['first_name_kana']       = $shippingAddress['first_name_kana'];
        }
        if (!empty($shippingAddress['last_name_kana'])) {
            $dataOrder['last_name_kana']       = $shippingAddress['last_name_kana'];
        }
        if (!empty($shippingAddress['address1'])) {
            $dataOrder['address1']       = $shippingAddress['address1'];
        }
        if (!empty($shippingAddress['address2'])) {
            $dataOrder['address2']       = $shippingAddress['address2'];
        }
        if (!empty($shippingAddress['address3'])) {
            $dataOrder['address3']       = $shippingAddress['address3'];
        }
        if (!empty($shippingAddress['country'])) {
            $dataOrder['country']       = $shippingAddress['country'];
        }
        if (!empty($shippingAddress['phone'])) {
            $dataOrder['phone']       = $shippingAddress['phone'];
        }
        if (!empty($shippingAddress['postcode'])) {
            $dataOrder['postcode']       = $shippingAddress['postcode'];
        }
        if (!empty($shippingAddress['company'])) {
            $dataOrder['company']       = $shippingAddress['company'];
        }
        if (!empty($shippingAddress['comment'])) {
            $dataOrder['comment']       = $shippingAddress['comment'];
        }

        $arrProductDetail = [];
        foreach ($products as $product) {
            if ($product['selectedVariant']) {
                $variants = [];
                foreach ($product['selectedVariant'] as $key1 => $variant) {
                    foreach ($variant['attributes'] as $key2 => $attribute) {
                        $index_val = $attribute['value_index'];
                        $reference = $product['variants'][$key2][$attribute['value_index']];
                        $variants[$key1][$attribute['group_id']] = $reference['name'].'__'.$reference['price'];
                    }
                }
            }
            $arrDetail['product_id']  = $product['id'];
            $arrDetail['name']        = $product['name'];
            $arrDetail['price']       = lc_currency_value($product['price']);
            $arrDetail['qty']         = $product['qty'];
            $arrDetail['store_id']    = $storeId;
            $arrDetail['attribute']   = (!empty($variants) ? json_encode($variants) : null);
            $arrDetail['total_price'] = lc_currency_value($product['sale_price'] ? $product['sale_price']['price_promotion'] : $product['price']) * $product['qty'] + $product['sumVariantPrice'];
            $arrProductDetail[]          = $arrDetail;
        }

        //Create new order
        $newOrder = (new ShopOrder)->createOrder($dataOrder, $dataTotal, $arrProductDetail);
        
        $orderID = $newOrder['orderID'];

        if ($newOrder['error'] == 1) {
            return response()->json(new JsonResponse([],$newOrder['msg']), Response::HTTP_FORBIDDEN);
        }

        //Create new address
        if ($address_process) {
            $addressNew = [
                'first_name'      => $shippingAddress['first_name'] ?? '',
                'last_name'       => $shippingAddress['last_name'] ?? '',
                'first_name_kana' => $shippingAddress['first_name_kana'] ?? '',
                'last_name_kana'  => $shippingAddress['last_name_kana'] ?? '',
                'postcode'        => $shippingAddress['postcode'] ?? '',
                'address1'        => $shippingAddress['address1'] ?? '',
                'address2'        => $shippingAddress['address2'] ?? '',
                'address3'        => $shippingAddress['address3'] ?? '',
                'country'         => $shippingAddress['country'] ?? '',
                'phone'           => $shippingAddress['phone'] ?? '',
            ];

            //Process escape
            $addressNew = lc_clean($addressNew);

            ShopCustomer::find($uID)->addresses()->save(new ShopCustomerAddress($addressNew));
        }

        if ($request->couponInUse) {
            $code = $request->couponInUse['code'];
            (new ShopDiscount)->createDiscount($orderID,$code,$uID,$storeId);
        }

        if ($paymentMethod) {
            // Check payment method
            $paymentMethodIns = lc_get_class_plugin_controller('Payment', $paymentMethod);
            return (new $paymentMethodIns)->processOrder($orderID,$shippingMethod,$paymentMethod);
        } else {
            return (new ShopCartController)->completeOrder($orderID,$shippingMethod,$paymentMethod);
        }
    }
    
    /**
     * Complete order
     *
     * @return [redirect]
     */
    public function completeOrder($orderID,$shippingMethod,$paymentMethod)
    {
        if ($orderID == 0){
            return response()->json(new JsonResponse([],'Error Order ID!'), Response::HTTP_FORBIDDEN);
        }
        // Cart::destroy(); // destroy cart

        $classPaymentConfig = lc_get_class_plugin_config('Payment', $paymentMethod);
        if (method_exists($classPaymentConfig, 'endApp')) {
            (new $classPaymentConfig)->endApp();
        }

        $classShippingConfig = lc_get_class_plugin_config('Shipping', $shippingMethod);
        if (method_exists($classShippingConfig, 'endApp')) {
            (new $classShippingConfig)->endApp();
        }

        if (lc_config('order_success_to_admin') || lc_config('order_success_to_customer')) {
            $data = ShopOrder::with('details')->find($orderID)->toArray();
            $checkContent = (new ShopEmailTemplate)->where('group', 'order_success_to_admin')->where('status', 1)->where('store_id', $data['store_id'])->first();
            $checkContentCustomer = (new ShopEmailTemplate)->where('group', 'order_success_to_customer')->where('status', 1)->where('store_id', $data['store_id'])->first();
            if ($checkContent || $checkContentCustomer) {
                $converted = lc_convert_apiLink_to_localLink(lc_store('logo',$data['store_id']));
                $logo = asset('storage/'.$converted['disk'].'/'.$converted['path']);
                $attribute_group = ShopAttributeGroup::all();

                foreach ($data['details'] as $key => $detail) {
                    $product = (new ShopProduct)->getDetail($detail['product_id']);
                    $pathDownload = $product->downloadPath->path ?? '';
                    $nameProduct = $detail['name'];
                    if ($product && $pathDownload && $product->property == LC_PROPERTY_DOWNLOAD) {
                        $nameProduct .="<br><a href='".lc_path_download_render($pathDownload)."'>Download</a>";
                    }

                    $orderDetail = '<tr>
                                        <td>' . trans('email.order.name') . '</td>
                                        <td>' . trans('email.order.image') . '</td>
                                        <td>' . trans('email.order.attribute') . '</td>
                                        <td>' . trans('email.order.price') . '</td>
                                        <td>' . trans('email.order.qty') . '</td>
                                        <td>' . trans('email.order.total') . '</td>
                                    </tr>';
                    foreach ($data['details'] as $key => $detail) {
                        $product = (new ShopProduct)->getDetail($detail['product_id']);
                        $pathDownload = $product->downloadPath->path ?? '';
                        $nameProduct = $detail['name'];
                        if ($product && $pathDownload && $product->property == BC_PROPERTY_DOWNLOAD) {
                            $nameProduct .="<br><a href='".bc_path_download_render($pathDownload)."'>Download</a>";
                        }
                        $attribute = json_decode(htmlspecialchars_decode($detail['attribute']));
                        $res_attb = '';
                        foreach ($attribute as $key => $att) {
                            foreach ($attribute_group as $key => $group) {
                                $group_value = (array)$att;
                                $group_value = explode('__', $group_value[$group->id])[0];
                                $res_attb .= $group->name.':'.$group_value.'<br>';
                            }
                        }

                        $converted = lc_convert_apiLink_to_localLink($product->image);
                        $product_image = asset('storage/'.$converted['disk'].'/'.$converted['path']);
                        $orderDetail .= '<tr>
                                            <td style="width: 140px;"><p style="display: block!important;width: 130px;white-space: nowrap;overflow: hidden !important;text-overflow: ellipsis;">' . $nameProduct . '</p></td>
                                            <td>'.'<img class="adapt-img" src="'.$product_image.'" alt style="display: block;" width="70">' .'</td>
                                            <td>'.$res_attb.'</td>
                                            <td>' . lc_currency_render($detail['price'], '', '', '', false) . '</td>
                                            <td>' . number_format($detail['qty']) . '</td>
                                            <td align="right">' . lc_currency_render($detail['total_price'], '', '', '', false) . '</td>
                                        </tr>';
                    }
                }
                $dataFind = [
                    '/\{\{\$logo\}\}/',
                    '/\{\{\$title\}\}/',
                    '/\{\{\$orderID\}\}/',
                    '/\{\{\$firstName\}\}/',
                    '/\{\{\$lastName\}\}/',
                    '/\{\{\$toname\}\}/',
                    '/\{\{\$address\}\}/',
                    '/\{\{\$address1\}\}/',
                    '/\{\{\$address2\}\}/',
                    '/\{\{\$address3\}\}/',
                    '/\{\{\$email\}\}/',
                    '/\{\{\$phone\}\}/',
                    '/\{\{\$comment\}\}/',
                    '/\{\{\$orderDetail\}\}/',
                    '/\{\{\$subtotal\}\}/',
                    '/\{\{\$shipping\}\}/',
                    '/\{\{\$discount\}\}/',
                    '/\{\{\$total\}\}/',
                    '/\{\{\$tax\}\}/',
                    '/\{\{\$shippingMethod\}\}/',
                    '/\{\{\$paymentMethod\}\}/',
                    '/\{\{\$currency\}\}/',
                    '/\{\{\$invoice_date\}\}/',
                ];

                $dataReplace = [
                    $logo,
                    trans('order.send_mail.new_title') . '#' . $orderID,
                    $orderID,
                    $data['first_name'],
                    $data['last_name'],
                    $data['first_name'].' '.$data['last_name'],
                    $data['address1'] . ' ' . $data['address2'].' '.$data['address3'],
                    $data['address1'],
                    $data['address2'],
                    $data['address3'],
                    $data['email'],
                    $data['phone'],
                    $data['comment'],
                    $orderDetail,
                    lc_currency_render($data['subtotal'], '', '', '', false),
                    lc_currency_render($data['shipping'], '', '', '', false),
                    lc_currency_render($data['discount'], '', '', '', false),
                    lc_currency_render($data['total'], '', '', '', false),
                    lc_currency_render($data['tax'], '', '', '', false),
                    $shippingMethod,
                    $paymentMethod,
                    $data['currency'],
                    Carbon::parse($data['created_at'])->format('d-m-Y H:i'),
                ];

                // Send mail order success to admin 
                if (lc_config('order_success_to_admin') && $checkContent) {
                    $content = $checkContent->text;
                    $content = preg_replace($dataFind, $dataReplace, $content);
                    $config = [
                        'to' => lc_store('email',$data['store_id']),
                        'subject' => trans('order.send_mail.new_title') . '#' . $orderID,
                    ];
                    lc_send_mail($content, $config, []);
                }

                // Send mail order success to customer
                if (lc_config('order_success_to_customer') && $checkContentCustomer) {
                    $contentCustomer = $checkContentCustomer->text;
                    $contentCustomer = preg_replace($dataFind, $dataReplace, $contentCustomer);
                    
                    $config = [
                        'to' => $data['email'],
                        'replyTo' => lc_store('email',$data['store_id']),
                        'subject' => trans('order.send_mail.new_title'),
                    ];

                    $attach = [];
                    if (lc_config('order_success_to_customer_pdf')) {
                        // Invoice pdf
                        \PDF::loadView($contentCustomer)
                            ->save(\Storage::disk('invoice')->path('order-'.$orderID.'.pdf'));
                        $attach['attachFromStorage'] = [
                            [
                                'file_storage' => 'invoice',
                                'file_path' => 'order-'.$orderID.'.pdf',
                            ]
                        ];
                    }

                    lc_send_mail($contentCustomer, $config, $attach);
                }
            }
            $response['message'] = trans('order.success',['store_name'=> lc_store('title',$data['store_id'])]);
            return response()->json(new JsonResponse($response), Response::HTTP_OK);
        }
    }



}
