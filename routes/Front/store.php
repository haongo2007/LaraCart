<?php

Route::group(['prefix' => 'store'], function () {
	Route::get('/getInfo', 'ShopStoreController@getInfo');
	Route::get('/getConfig', 'ShopStoreController@getConfig');
});