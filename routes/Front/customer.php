<?php
$prefixCustomer = lc_config('PREFIX_MEMBER') ?? 'customer';

if (lc_config('customer_verify')) {
    $midlware = ['auth','email.verify'];
} else {
    $midlware = ['auth'];
}
Route::group(
    [
        'prefix' => $prefixCustomer,
        'middleware' => $midlware
    ],
    function ($router){
        $prefixCustomerOrderList    = lc_config('PREFIX_MEMBER_ORDER_LIST')??'order-list';
        $prefixCustomerOrderDetail  = lc_config('PREFIX_MEMBER_ORDER_DETAIL')??'order-detail';
        $prefixCustomerAddresList   = lc_config('PREFIX_MEMBER_ADDRESS_LIST')??'address-list';
        $prefixCustomerUpdateAddres = lc_config('PREFIX_MEMBER_UPDATE_ADDRESS')??'update-address';
        $prefixCustomerDeleteAddres = lc_config('PREFIX_MEMBER_DELETE_ADDRESS')??'delete-address';
        $prefixCustomerChangePwd    = lc_config('PREFIX_MEMBER_CHANGE_PWD')??'change-password';
        $prefixCustomerChangeInfo   = lc_config('PREFIX_MEMBER_CHANGE_INFO')??'change-infomation';


        $router->get('/', 'ShopAccountController@showCustomer');
        
        $router->get('/'.$prefixCustomerOrderList, 'ShopAccountController@orderListProcessFront')
            ->name('customer.order_list');
        $router->get('/'.$prefixCustomerOrderDetail.'/{id}', 'ShopAccountController@orderDetailProcessFront')
            ->name('customer.order_detail');
        $router->get('/'.$prefixCustomerAddresList, 'ShopAccountController@addressListProcessFront')
            ->name('customer.address_list');
        $router->get('/'.$prefixCustomerUpdateAddres.'/{id}', 'ShopAccountController@updateAddressProcessFront')
            ->name('customer.update_address');
        $router->post('/'.$prefixCustomerUpdateAddres.'/{id}', 'ShopAccountController@postUpdateAddress')
            ->name('customer.post_update_address');
        $router->post('/'.$prefixCustomerDeleteAddres, 'ShopAccountController@deleteAddress')
            ->name('customer.delete_address');
        $router->get('/'.$prefixCustomerChangePwd, 'ShopAccountController@changePasswordProcessFront')
            ->name('customer.change_password');
        $router->post('/change_password', 'ShopAccountController@postChangePassword')
            ->name('customer.post_change_password');
        $router->get('/'.$prefixCustomerChangeInfo, 'ShopAccountController@changeInfomationProcessFront')
            ->name('customer.change_infomation');
        $router->get('/address_detail', 'ShopAccountController@getAddress')
            ->name('customer.address_detail');
        //$router->post('/change_infomation', 'ShopAccountController@postChangeInfomation')
        //    ->name('customer.post_change_infomation');
        $router->post('/change_infomation', 'ShopAccountController@postChangeInfomation')
            ->name('customer.update');

        // The Email Verification Notice
        $router->get('/email/verify', 'ShopAccountController@verificationProcessFront')
            ->name('customer.verify');
        $router->post('/email/verify', 'ShopAccountController@resendVerification')
            ->name('customer.verify_resend');
    }
);

//Process url verify
Route::get($prefixCustomer.'/email/verify/{id}/{token}', 'ShopAccountController@verificationProcessData')
->middleware($midlware)
->name('customer.verify_process');