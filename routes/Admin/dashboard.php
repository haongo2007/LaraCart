<?php
Route::group(['prefix' => 'dashboard'], function () {
    Route::get('/overview', 'DashboardController@index');
	Route::get('/orders', 'DashboardController@orders');
});
