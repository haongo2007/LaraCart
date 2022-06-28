<?php
Route::group(['prefix' => 'categories'], function () {
	Route::get('/{parentId}', 'ShopCategoryController@getListChild');
});
