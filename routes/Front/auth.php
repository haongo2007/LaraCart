<?php
Route::group(['prefix' => 'auth'], function () {
    Route::post('/logout', 'Auth\AuthController@logout');
    Route::get('/info', 'Auth\AuthController@info');
});
