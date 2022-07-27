<?php
Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', 'Auth\AuthController@login');
    Route::post('/logout', 'Auth\AuthController@logout');
});
