<?php
namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopEmailTemplate;
use App\Models\Front\ShopAttributeGroup;
use App\Models\Front\ShopCountry;
use App\Models\Front\ShopOrder;
use App\Models\Front\ShopOrderTotal;
use App\Models\Front\ShopProduct;
use App\Models\Front\ShopCustomer;
use App\Models\Front\ShopCustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;

class ShopCheckoutController extends Controller
{
    const ORDER_STATUS_NEW = 1;
    const PAYMENT_UNPAID = 1;
    const SHIPPING_NOTSEND = 1;

    public function __construct()
    {

    }


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
     * Get list cart: screen get cart
     * @return [type] [description]
     */
    private function _getCart()
    {
        session()->forget('paymentMethod'); //destroy paymentMethod
        session()->forget('shippingMethod'); //destroy shippingMethod
        session()->forget('orderID'); //destroy orderID
        
        //Shipping
        $moduleShipping = lc_get_plugin_installed('shipping');
        $sourcesShipping = lc_get_all_plugin('shipping');
        $shippingMethod = array();
        foreach ($moduleShipping as $module) {
            if (array_key_exists($module['key'], $sourcesShipping)) {
                $moduleClass = lc_get_class_plugin_config('shipping', $module['key']);
                $shippingMethod[$module['key']] = (new $moduleClass)->getData();
            }
        }

        //Payment
        $modulePayment = lc_get_plugin_installed('payment');
        $sourcesPayment = lc_get_all_plugin('payment');
        $paymentMethod = array();
        foreach ($modulePayment as $module) {
            if (array_key_exists($module['key'], $sourcesPayment)) {
                $moduleClass = $sourcesPayment[$module['key']].'\AppConfig';
                $paymentMethod[$module['key']] = (new $moduleClass)->getData();
            }
        }        

        //Total
        $moduleTotal = lc_get_plugin_installed('total');
        $sourcesTotal = lc_get_all_plugin('total');
        $totalMethod = array();
        foreach ($moduleTotal as $module) {
            if (array_key_exists($module['key'], $sourcesTotal)) {
                $moduleClass = $sourcesTotal[$module['key']].'\AppConfig';
                $totalMethod[$module['key']] = (new $moduleClass)->getData();
            }
        } 

        // Shipping address
        $customer = auth()->user();
        if ($customer) {
            $address = $customer->getAddressDefault();
            if ($address) {
                $addressDefaul = [
                    'first_name'      => $address->first_name,
                    'last_name'       => $address->last_name,
                    'first_name_kana' => $address->first_name_kana,
                    'last_name_kana'  => $address->last_name_kana,
                    'email'           => $customer->email,
                    'address1'        => $address->address1,
                    'address2'        => $address->address2,
                    'address3'        => $address->address3,
                    'postcode'        => $address->postcode,
                    'company'         => $customer->company,
                    'country'         => $address->country,
                    'phone'           => $address->phone,
                    'comment'         => '',
                ];
            } else {
                $addressDefaul = [
                    'first_name'      => $customer->first_name,
                    'last_name'       => $customer->last_name,
                    'first_name_kana' => $customer->first_name_kana,
                    'last_name_kana'  => $customer->last_name_kana,
                    'email'           => $customer->email,
                    'address1'        => $customer->address1,
                    'address2'        => $customer->address2,
                    'address3'        => $customer->address3,
                    'postcode'        => $customer->postcode,
                    'company'         => $customer->company,
                    'country'         => $customer->country,
                    'phone'           => $customer->phone,
                    'comment'         => '',
                ];
            }

        } else {
            $addressDefaul = [
                'first_name'      => '',
                'last_name'       => '',
                'first_name_kana' => '',
                'last_name_kana'  => '',
                'postcode'        => '',
                'company'         => '',
                'email'           => '',
                'address1'        => '',
                'address2'        => '',
                'address3'        => '',
                'country'         => '',
                'phone'           => '',
                'comment'         => '',
            ];
        }
        $shippingAddress = session('shippingAddress') ?? $addressDefaul;
        $objects = ShopOrderTotal::getObjectOrderTotal();
        $viewCaptcha = '';
        if(lc_captcha_method() && in_array('checkout', lc_captcha_page())) {
            if (view()->exists(lc_captcha_method()->pathPlugin.'::render')){
                $dataView = [
                    'titleButton' => trans('cart.checkout'),
                    'idForm' => 'form-process',
                    'idButtonForm' => 'button-form-process',
                ];
                $viewCaptcha = view(lc_captcha_method()->pathPlugin.'::render', $dataView)->render();
            }
        }

        lc_check_view($this->templatePath . '.Cart.index');
        return view(
            $this->templatePath . '.Cart.index',
            [
                'title'           => trans('front.cart_title'),
                'description'     => '',
                'keyword'         => '',
                // 'cart'            => Cart::instance('default')->content(),
                'shippingMethod'  => $shippingMethod,
                'paymentMethod'   => $paymentMethod,
                'totalMethod'     => $totalMethod,
                'addressList'     => $customer ? $customer->addresses : [],
                'dataTotal'       => ShopOrderTotal::processDataTotal($objects),
                'shippingAddress' => $shippingAddress,
                'countries'       => ShopCountry::getCodeAll(),
                'attributesGroup' => ShopAttributeGroup::pluck('name', 'id')->all(),
                'viewCaptcha'     => $viewCaptcha,
                'layout_page'     => 'Cart',
            ]
        );
    }


    /**
     * Process Cart, prepare for the checkout screen
     */
    private function _checkoutPrepare()
    {
        $customer = auth()->user();
        // if (Cart::instance('default')->count() == 0) {
        //     return redirect(lc_route('cart'));
        // }

        //Not allow for guest
        if (!lc_config('shop_allow_guest') && !$customer) {
            return redirect(lc_route('login'));
        }

        $data = request()->all();

        $validate = [
            'first_name'     => config('validation.customer.first_name', 'required|string|max:100'),
            'email'          => config('validation.customer.email', 'required|string|email|max:255'),
        ];
        //check shipping
        if (!lc_config('shipping_off')) {
            $validate['shippingMethod'] = 'required';
        }
        //check payment
        if (!lc_config('payment_off')) {
            $validate['paymentMethod'] = 'required';
        }

        if (lc_config('customer_lastname')) {
            if (lc_config('customer_lastname_required')) {
                $validate['last_name'] = config('validation.customer.last_name_required', 'required|string|max:100');
            } else {
                $validate['last_name'] = config('validation.customer.last_name_null', 'nullable|string|max:100');
            }
        }
        if (lc_config('customer_address1')) {
            if (lc_config('customer_address1_required')) {
                $validate['address1'] = config('validation.customer.address1_required', 'required|string|max:100');
            } else {
                $validate['address1'] = config('validation.customer.address1_null', 'nullable|string|max:100');
            }
        }

        if (lc_config('customer_address2')) {
            if (lc_config('customer_address2_required')) {
                $validate['address2'] = config('validation.customer.address2_required', 'required|string|max:100');
            } else {
                $validate['address2'] = config('validation.customer.address2_null', 'nullable|string|max:100');
            }
        }

        if (lc_config('customer_address3')) {
            if (lc_config('customer_address3_required')) {
                $validate['address3'] = config('validation.customer.address3_required', 'required|string|max:100');
            } else {
                $validate['address3'] = config('validation.customer.address3_null', 'nullable|string|max:100');
            }
        }

        if (lc_config('customer_phone')) {
            if (lc_config('customer_phone_required')) {
                $validate['phone'] = config('validation.customer.phone_required', 'required|regex:/^0[^0][0-9\-]{7,13}$/');
            } else {
                $validate['phone'] = config('validation.customer.phone_null', 'nullable|regex:/^0[^0][0-9\-]{7,13}$/');
            }
        }
        if (lc_config('customer_country')) {
            $arraycountry = (new ShopCountry)->pluck('code')->toArray();
            if (lc_config('customer_country_required')) {
                $validate['country'] = config('validation.customer.country_required', 'required|string|min:2').'|in:'. implode(',', $arraycountry);
            } else {
                $validate['country'] = config('validation.customer.country_null', 'nullable|string|min:2').'|in:'. implode(',', $arraycountry);
            }
        }

        if (lc_config('customer_postcode')) {
            if (lc_config('customer_postcode_required')) {
                $validate['postcode'] = config('validation.customer.postcode_required', 'required|min:5');
            } else {
                $validate['postcode'] = config('validation.customer.postcode_null', 'nullable|min:5');
            }
        }
        if (lc_config('customer_company')) {
            if (lc_config('customer_company_required')) {
                $validate['company'] = config('validation.customer.company_required', 'required|string|max:100');
            } else {
                $validate['company'] = config('validation.customer.company_null', 'nullable|string|max:100');
            }
        } 

        if (lc_config('customer_name_kana')) {
            if (lc_config('customer_name_kana_required')) {
                $validate['first_name_kana'] = config('validation.customer.name_kana_required', 'required|string|max:100');
                $validate['last_name_kana'] = config('validation.customer.name_kana_required', 'required|string|max:100');
            } else {
                $validate['first_name_kana'] = config('validation.customer.name_kana_null', 'nullable|string|max:100');
                $validate['last_name_kana'] = config('validation.customer.name_kana_null', 'nullable|string|max:100');
            }
        }

        $messages = [
            'last_name.required'      => trans('validation.required', ['attribute'=> trans('cart.last_name')]),
            'first_name.required'     => trans('validation.required', ['attribute'=> trans('cart.first_name')]),
            'email.required'          => trans('validation.required', ['attribute'=> trans('cart.email')]),
            'address1.required'       => trans('validation.required', ['attribute'=> trans('cart.address1')]),
            'address2.required'       => trans('validation.required', ['attribute'=> trans('cart.address2')]),
            'address3.required'       => trans('validation.required', ['attribute'=> trans('cart.address3')]),
            'phone.required'          => trans('validation.required', ['attribute'=> trans('cart.phone')]),
            'country.required'        => trans('validation.required', ['attribute'=> trans('cart.country')]),
            'postcode.required'       => trans('validation.required', ['attribute'=> trans('cart.postcode')]),
            'company.required'        => trans('validation.required', ['attribute'=> trans('cart.company')]),
            'sex.required'            => trans('validation.required', ['attribute'=> trans('cart.sex')]),
            'birthday.required'       => trans('validation.required', ['attribute'=> trans('cart.birthday')]),
            'email.email'             => trans('validation.email', ['attribute'=> trans('cart.email')]),
            'phone.regex'             => trans('customer.phone_regex'),
            'postcode.min'            => trans('validation.min', ['attribute'=> trans('cart.postcode')]),
            'country.min'             => trans('validation.min', ['attribute'=> trans('cart.country')]),
            'first_name.max'          => trans('validation.max', ['attribute'=> trans('cart.first_name')]),
            'email.max'               => trans('validation.max', ['attribute'=> trans('cart.email')]),
            'address1.max'            => trans('validation.max', ['attribute'=> trans('cart.address1')]),
            'address2.max'            => trans('validation.max', ['attribute'=> trans('cart.address2')]),
            'address3.max'            => trans('validation.max', ['attribute'=> trans('cart.address3')]),
            'last_name.max'           => trans('validation.max', ['attribute'=> trans('cart.last_name')]),
            'birthday.date'           => trans('validation.date', ['attribute'=> trans('cart.birthday')]),
            'birthday.date_format'    => trans('validation.date_format', ['attribute'=> trans('cart.birthday')]),
            'shippingMethod.required' => trans('cart.validation.shippingMethod_required'),
            'paymentMethod.required'  => trans('cart.validation.paymentMethod_required'),
        ];

        if(lc_captcha_method() && in_array('checkout', lc_captcha_page())) {
            $data['captcha_field'] = $data[lc_captcha_method()->getField()] ?? '';
            $validate['captcha_field'] = ['required', 'string', new \BlackCart\Core\Rules\CaptchaRule];
        }


        $v = Validator::make(
            $data, 
            $validate, 
            $messages
        );
        if ($v->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($v->errors());
        }

        //Set session shippingMethod
        if (!lc_config('shipping_off')) {
            session(['shippingMethod' => request('shippingMethod')]);
        }

        //Set session paymentMethod
        if (!lc_config('payment_off')) {
            session(['paymentMethod' => request('paymentMethod')]);
        }

        //Set session address process
        session(['address_process' => request('address_process')]);
        //Set session shippingAddressshippingAddress
        session(
            [
                'shippingAddress' => [
                    'first_name'      => request('first_name'),
                    'last_name'       => request('last_name'),
                    'first_name_kana' => request('first_name_kana'),
                    'last_name_kana'  => request('last_name_kana'),
                    'email'           => request('email'),
                    'country'         => request('country'),
                    'address1'        => request('address1'),
                    'address2'        => request('address2'),
                    'address3'        => request('address3'),
                    'phone'           => request('phone'),
                    'postcode'        => request('postcode'),
                    'company'         => request('company'),
                    'comment'         => request('comment'),
                ],
            ]
        );

        //Check minimum
        $arrCheckQty = [];
        // $cart = Cart::instance('default')->content()->toArray();
        foreach ($cart as $key => $row) {
            $arrCheckQty[$row['id']] = ($arrCheckQty[$row['id']] ?? 0) + $row['qty'];
        }
        $arrProductMinimum = ShopProduct::whereIn('id', array_keys($arrCheckQty))->pluck('minimum', 'id')->all();
        $arrErrorQty = [];
        foreach ($arrProductMinimum as $pId => $min) {
            if ($arrCheckQty[$pId] < $min) {
                $arrErrorQty[$pId] = $min;
            }
        }
        if (count($arrErrorQty)) {
            return redirect(lc_route('cart'))->with('arrErrorQty', $arrErrorQty);
        }
        //End check minimum

        return redirect(lc_route('checkout'));
    }

    
    /**
     * Complete order
     *
     * @return [redirect]
     */
    public function completeOrder()
    {
        $orderID = session('orderID') ??0;
        if ($orderID == 0){
            return redirect()->route('home', ['error' => 'Error Order ID!']);
        }
        // Cart::destroy(); // destroy cart

        $paymentMethod = session('paymentMethod');
        $shippingMethod = session('shippingMethod');
        $totalMethod = session('totalMethod', []);

        $classPaymentConfig = lc_get_class_plugin_config('Payment', $paymentMethod);
        if (method_exists($classPaymentConfig, 'endApp')) {
            (new $classPaymentConfig)->endApp();
        }

        $classShippingConfig = lc_get_class_plugin_config('Shipping', $shippingMethod);
        if (method_exists($classShippingConfig, 'endApp')) {
            (new $classShippingConfig)->endApp();
        }

        if ($totalMethod && is_array($totalMethod)) {
            foreach ($totalMethod as $keyMethod => $valueMethod) {
                $classTotalConfig = lc_get_class_plugin_config('Total', $keyMethod);
                if (method_exists($classTotalConfig, 'endApp')) {
                    (new $classTotalConfig)->endApp(['orderID' => $orderID, 'code' => $valueMethod]);
                }
            }
        }

        session()->forget('paymentMethod'); //destroy paymentMethod
        session()->forget('shippingMethod'); //destroy shippingMethod
        session()->forget('totalMethod'); //destroy totalMethod
        session()->forget('otherMethod'); //destroy otherMethod
        session()->forget('dataTotal'); //destroy dataTotal
        session()->forget('dataOrder'); //destroy dataOrder
        session()->forget('arrCartDetail'); //destroy arrCartDetail
        session()->forget('orderID'); //destroy orderID

        if (lc_config('order_success_to_admin') || lc_config('order_success_to_customer')) {
            $data = ShopOrder::with('details')->find($orderID)->toArray();
            $checkContent = (new ShopEmailTemplate)->where('group', 'order_success_to_admin')->where('status', 1)->first();
            $checkContentCustomer = (new ShopEmailTemplate)->where('group', 'order_success_to_customer')->where('status', 1)->first();
            if ($checkContent || $checkContentCustomer) {

                $orderDetail = '';
                $orderDetail .= '<tr>
                                    <td>' . trans('email.order.sort') . '</td>
                                    <td>' . trans('email.order.sku') . '</td>
                                    <td>' . trans('email.order.name') . '</td>
                                    <td>' . trans('email.order.price') . '</td>
                                    <td>' . trans('email.order.qty') . '</td>
                                    <td>' . trans('email.order.total') . '</td>
                                </tr>';
                foreach ($data['details'] as $key => $detail) {
                    $product = (new ShopProduct)->getDetail($detail['product_id']);
                    $pathDownload = $product->downloadPath->path ?? '';
                    $nameProduct = $detail['name'];
                    if ($product && $pathDownload && $product->property == LC_PROPERTY_DOWNLOAD) {
                        $nameProduct .="<br><a href='".lc_path_download_render($pathDownload)."'>Download</a>";
                    }

                    $orderDetail .= '<tr>
                                    <td>' . ($key + 1) . '</td>
                                    <td>' . $detail['sku'] . '</td>
                                    <td>' . $nameProduct . '</td>
                                    <td>' . lc_currency_render($detail['price'], '', '', '', false) . '</td>
                                    <td>' . number_format($detail['qty']) . '</td>
                                    <td align="right">' . lc_currency_render($detail['total_price'], '', '', '', false) . '</td>
                                </tr>';
                }
                $dataFind = [
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
                ];
                $dataReplace = [
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
                ];

                // Send mail order success to admin 
                if (lc_config('order_success_to_admin') && $checkContent) {
                    $content = $checkContent->text;
                    $content = preg_replace($dataFind, $dataReplace, $content);
                    $dataView = [
                        'content' => $content,
                    ];
                    $config = [
                        'to' => lc_store('email'),
                        'subject' => trans('order.send_mail.new_title') . '#' . $orderID,
                    ];
                    lc_send_mail($this->templatePath . '.Mail.order_success_to_admin', $dataView, $config, []);
                }

                // Send mail order success to customer
                if (lc_config('order_success_to_customer') && $checkContentCustomer) {
                    $contentCustomer = $checkContentCustomer->text;
                    $contentCustomer = preg_replace($dataFind, $dataReplace, $contentCustomer);
                    $dataView = [
                        'content' => $contentCustomer,
                    ];
                    $config = [
                        'to' => $data['email'],
                        'replyTo' => lc_store('email'),
                        'subject' => trans('order.send_mail.new_title'),
                    ];

                    $attach = [];
                    if (lc_config('order_success_to_customer_pdf')) {
                        // Invoice pdf
                        \PDF::loadView($this->templatePath . '.Mail.order_success_to_customer_pdf', $dataView)
                            ->save(\Storage::disk('invoice')->path('order-'.$orderID.'.pdf'));
                        $attach['attachFromStorage'] = [
                            [
                                'file_storage' => 'invoice',
                                'file_path' => 'order-'.$orderID.'.pdf',
                            ]
                        ];
                    }

                    lc_send_mail($this->templatePath . '.Mail.order_success_to_customer', $dataView, $config, $attach);
                }
            }

        }

        return redirect(lc_route('order.success'))->with('orderID', $orderID);
    }



}
