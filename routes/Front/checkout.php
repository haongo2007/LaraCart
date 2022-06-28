<?php 
Route::group(['prefix' => 'checkout'], function () {
Route::get('/info', 'ShopCheckoutController@getInfo');
Route::post('/store', 'ShopCheckoutController@store');
});
