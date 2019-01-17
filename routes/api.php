<?php

use Illuminate\Http\Request;

Route::prefix('user')->group(function () {
  Route::put('login', 'API\UserController@login');
  Route::post('', 'API\UserController@register');
  Route::group(['middleware' => 'auth:api'], function(){
    Route::put('logout', 'API\UserController@logout');
    Route::get('/{id}', 'API\UserController@details');
    Route::delete('/{id}', 'API\UserController@delete');
    Route::put('/{id}', 'API\UserController@update');
  });
});
