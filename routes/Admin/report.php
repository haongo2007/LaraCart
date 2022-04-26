<?php
Route::group(['prefix' => 'report'], function () {
    Route::get('/product', 'ReportController@product');
});
