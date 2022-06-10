<?php 

Route::get('checkout/info', 'ShopCheckoutController@getInfo');
Route::post('checkout/store', 'ShopCheckoutController@store');