<?php

use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$suffix = Lc_config('SUFFIX_URL')??'';

Route::group([
        'prefix' => LC_ADMIN_PREFIX,
        'namespace' => 'Api\Admin'
    ],function() {
    
    Route::post('auth/login', 'Auth\AuthController@login');
    Route::get('getFile', 'UserController@getFileFromS3');

    Route::get('test',  function () {
        
    });
    Route::group(['middleware' => LC_ADMIN_MIDDLEWARE], function () {
        foreach (glob(__DIR__."/Admin/*.php") as $filename) {
            require_once $filename;
        }
    });
    
});



Route::group([
        'namespace' => 'Api\Front',
        'middleware' => LC_FRONT_MIDDLEWARE,
    ],function() {
    

    Route::post('auth/login', 'Auth\LoginController@login');

        
    foreach (glob(__DIR__ . '/Front/*.php') as $filename) {
        require_once $filename;
    }

    
});