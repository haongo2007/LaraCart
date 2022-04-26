<?php
/**
 * Route front
 */
if(lc_config('Discount')) {
Route::group(
    [
        'prefix'    => 'plugin/discount',
        'namespace' => 'App\Plugins\Total\Discount\Controllers',
    ],
    function () {
        Route::post('/discount_process', 'FrontController@useDiscount')
            ->name('discount.process');
        Route::post('/discount_remove', 'FrontController@removeDiscount')
            ->name('discount.remove');
    }
);
}
/**
 * Route admin
 */
Route::group(
    [
        'prefix' => LC_ADMIN_PREFIX.'/coupon-discount',
        'middleware' => LC_ADMIN_MIDDLEWARE,
        'namespace' => 'App\Plugins\Total\Discount\Admin',
    ], 
    function () {
        Route::apiResource('/', 'AdminController');
        // Route::get('/', 'AdminController@index')
        // ->name('admin_discount.index');
        // Route::get('create', 'AdminController@create')
        //     ->name('admin_discount.create');
        // Route::post('/create', 'AdminController@postCreate')
        //     ->name('admin_discount.create');
        // Route::get('/edit/{id}', 'AdminController@edit')
        //     ->name('admin_discount.edit');
        // Route::post('/edit/{id}', 'AdminController@postEdit')
        //     ->name('admin_discount.edit');
        // Route::post('/delete', 'AdminController@deleteList')
        //     ->name('admin_discount.delete');
    }
);
