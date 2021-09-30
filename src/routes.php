<?php

Route::group(['prefix' => 'hubspot/auth', 'as' => 'hubspot.auth.'], function(){
    Route::get('/redirect', 'Jordanbeattie\Hubspot\Controllers\LoginController@redirectToHubspot')
        ->name('login');
    Route::get('/callback', 'Jordanbeattie\Hubspot\Controllers\LoginController@hubspotCallback')
        ->name('callback');
});
