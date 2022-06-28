<?php
$prefixCustomer = lc_config('PREFIX_MEMBER') ?? 'customer';

if (lc_config('customer_verify')) {
    $midlware = ['auth','email.verify'];
} else {
    $midlware = ['auth'];
}

Route::group(['prefix' => $prefixCustomer,'middleware' => $midlware], function ($router) {
    $prefixCustomerOrderList    = lc_config('PREFIX_MEMBER_ORDER_LIST')??'order-list';
    $prefixCustomerOrderDetail  = lc_config('PREFIX_MEMBER_ORDER_DETAIL')??'order-detail';
    $prefixCustomerAddresList   = lc_config('PREFIX_MEMBER_ADDRESS_LIST')??'address-list';
    $prefixCustomerUpdateAddres = lc_config('PREFIX_MEMBER_UPDATE_ADDRESS')??'update-address';
    $prefixCustomerDeleteAddres = lc_config('PREFIX_MEMBER_DELETE_ADDRESS')??'delete-address';
    $prefixCustomerChangePwd    = lc_config('PREFIX_MEMBER_CHANGE_PWD')??'change-password';
    $prefixCustomerChangeInfo   = lc_config('PREFIX_MEMBER_CHANGE_INFO')??'change-infomation';


    $router->get('/', 'ShopAccountController@showCustomer');
    
    $router->get('/'.$prefixCustomerOrderList, 'ShopAccountController@orderListProcessFront');
    $router->get('/'.$prefixCustomerOrderDetail.'/{id}', 'ShopAccountController@orderDetailProcessFront');
    $router->get('/'.$prefixCustomerAddresList, 'ShopAccountController@addressListProcessFront');
    $router->get('/'.$prefixCustomerUpdateAddres.'/{id}', 'ShopAccountController@updateAddressProcessFront');
    $router->post('/'.$prefixCustomerUpdateAddres.'/{id}', 'ShopAccountController@postUpdateAddress');
    $router->post('/'.$prefixCustomerDeleteAddres, 'ShopAccountController@deleteAddress');
    $router->get('/'.$prefixCustomerChangePwd, 'ShopAccountController@changePasswordProcessFront');
    $router->post('/change_password', 'ShopAccountController@postChangePassword');
    $router->get('/'.$prefixCustomerChangeInfo, 'ShopAccountController@changeInfomationProcessFront');
    $router->get('/address_detail', 'ShopAccountController@getAddress');
    $router->post('/change_infomation', 'ShopAccountController@postChangeInfomation');

    // The Email Verification Notice
    $router->get('/email/verify', 'ShopAccountController@verificationProcessFront');
    $router->post('/email/verify', 'ShopAccountController@resendVerification');
});

//Process url verify
Route::get($prefixCustomer.'/email/verify/{id}/{token}', 'ShopAccountController@verificationProcessData')->middleware($midlware);