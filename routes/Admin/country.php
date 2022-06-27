<?php 
    
    Route::get('country', 'CountryController@index');
    Route::get('country/flags/{code}', 'CountryController@flags');