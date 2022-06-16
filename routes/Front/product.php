<?php

Route::get('product/special', 'ShopProductController@special');
Route::post('product/rating', 'ShopProductController@rating');
Route::apiResource('product', 'ShopProductController');