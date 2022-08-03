<?php

Route::group(['prefix' => 'news'], function () {
	Route::get('/{id}', 'ShopNewsController@show');
	Route::get('/', 'ShopNewsController@index');
});
