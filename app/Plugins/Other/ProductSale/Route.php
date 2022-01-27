<?php
/**
 * Route front
 */
if(bc_config('ProductSale')) {
     
Route::group(
    [
        'prefix' => $langUrl
    ], 
    function ($router) {
        $router->get('flash-sale.html', '\App\Plugins\Other\ProductSale\Controllers\FrontController@flashSaleProcessFront')
            ->name('flash-sale');
    }
);

Route::group(
    [
        'prefix'    => 'plugin/productsale',
        'namespace' => 'App\Plugins\Other\ProductSale\Controllers',
    ],
    function () {
        Route::get('index', 'FrontController@index')
        ->name('productsale.index');
    }
);
}
/**
 * Route admin
 */
Route::group(
    [
        'prefix' => BC_ADMIN_PREFIX.'/productsale',
        'middleware' => BC_ADMIN_MIDDLEWARE,
        'namespace' => 'App\Plugins\Other\ProductSale\Admin',
    ], 
    function () {
        Route::get('/', 'AdminController@index')
        ->name('admin_productsale.index');
        Route::post('/create', 'AdminController@postCreate')->name('admin_productsale.create');
        Route::get('/edit/{id}', 'AdminController@edit')->name('admin_productsale.edit');
        Route::post('/edit/{id}', 'AdminController@postEdit')->name('admin_productsale.edit');
        Route::post('/delete', 'AdminController@deleteList')->name('admin_productsale.delete');
    }
);
