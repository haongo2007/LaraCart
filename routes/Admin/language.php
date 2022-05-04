<?php

    Route::get('/languages/changeLanguage/{lang}', 'LanguageController@changeLanguages');
    Route::get('/languages/getActiveLanguage/{storeId?}', 'LanguageController@getActiveLanguages');
	Route::apiResource('/languages', 'LanguageController');
    // Route::get('create', function () {
    //     return redirect()->route('admin_language.index');
    // });
    // Route::post('/create', 'AdminLanguageController@postCreate')->name('admin_language.create');
    // Route::get('/edit/{id}', 'AdminLanguageController@edit')->name('admin_language.edit');
    // Route::post('/edit/{id}', 'AdminLanguageController@postEdit')->name('admin_language.edit');
    // Route::post('/delete', 'AdminLanguageController@deleteList')->name('admin_language.delete');
