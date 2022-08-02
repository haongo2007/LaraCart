<?php
Route::group(['prefix' => 'store'], function () {
	Route::get('/home', 'ShopStoreController@index');
	Route::get('/getInfo', 'ShopStoreController@getInfo');
});