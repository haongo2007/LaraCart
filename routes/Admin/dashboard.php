<?php
Route::group(['prefix' => 'dashboard'], function () {
    Route::get('/overview', 'DashboardController@index');
	Route::get('/orders', 'DashboardController@orders');
	Route::get('/productOutStock', 'DashboardController@productOutStock');
	Route::get('/orderCountry', 'DashboardController@orderAreaStatistics');
});
