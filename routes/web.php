<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'web'], function () {
	Route::get('file-manager/url','LaraCartController@url');
	Route::get('file-manager/initialize','LaraCartController@initialize');
    Route::get(env('LARACART_PATH'), 'LaraCartController@index')->where('any', '.*')->name('laraCart');
});