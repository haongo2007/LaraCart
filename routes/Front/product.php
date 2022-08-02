<?php

Route::group(['prefix' => 'product'], function () {
	Route::post('/rating', 'ShopProductController@rating');
	Route::get('/', 'ShopProductController@index');
	Route::get('/{id}', 'ShopProductController@show');
});
