<?php 
Route::group(['prefix' => 'discount'], function () {
	Route::post('/checkCoupon', 'ShopDiscountController@checkCoupon');
});
