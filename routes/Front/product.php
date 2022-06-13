<?php

Route::get('product/special', 'ShopProductController@special');
Route::apiResource('product', 'ShopProductController');