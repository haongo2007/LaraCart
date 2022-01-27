<?php
	use \App\Helper\Acl;
// Route::group(['prefix' => 'user'], function () {
//     Route::get('/', 'Auth\UsersController@index')->name('admin_user.index');
//     Route::get('create', 'Auth\UsersController@create')->name('admin_user.create');
//     Route::post('/create', 'Auth\UsersController@postCreate')->name('admin_user.create');
//     Route::get('/edit/{id}', 'Auth\UsersController@edit')->name('admin_user.edit');
//     Route::post('/edit/{id}', 'Auth\UsersController@postEdit')->name('admin_user.edit');
//     Route::post('/delete', 'Auth\UsersController@deleteList')->name('admin_user.delete');
// });

    Route::apiResource('users', 'UserController')->middleware('permission:' . Acl::PERMISSION_USER_MANAGE);
    Route::put('users/{user}', 'UserController@update');
    Route::get('users/{user}/permissions', 'UserController@permissions')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
    Route::put('users/{user}/permissions', 'UserController@updatePermissions')->middleware('permission:' .Acl::PERMISSION_PERMISSION_MANAGE);
    Route::get('user','UserController@show');
    Route::post('update/avatar', 'UserController@updateAvatar');