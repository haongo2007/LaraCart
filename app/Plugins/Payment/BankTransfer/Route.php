<?php
/**
 * Route admin
 */
Route::group(
    [
        'prefix' => BC_DB_PREFIX.'/bank_transfer',
        'middleware' => BC_ADMIN_MIDDLEWARE,
        'namespace' => 'App\Plugins\Payment\BankTransfer\Admin',
    ], 
    function () {
        Route::get('/', 'AdminController@index')
            ->name('admin_bank_transfer.index');
    }
);
