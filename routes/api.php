<?php

use Illuminate\Http\Request;


Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');

/* Caso o email esteja configurado utilizar ->middleware('verified'); nas rotas
** que necesitam que o email estejam validado para serem acessadas
*/
Route::group(['middleware' => 'auth:api'], function(){
  Route::get('details', 'API\UserController@details');
  Route::get('logout', 'API\UserController@logout');
  Route::post('update', 'API\UserController@update');
  Route::post('changepassword', 'API\UserController@changepassword');
  Route::get('delete', 'API\UserController@delete');
});
