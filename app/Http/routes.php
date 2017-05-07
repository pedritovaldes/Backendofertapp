<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'api/v1'], function()
{
    //Login y registro
    Route::post('registro', 'ApiController@registro');
    Route::post('login', 'ApiController@login');

    //Perfil del usuario
    Route::delete('user/{id}', 'ApiController@deleteUser');

    //Anuncios
    Route::post('anuncio', 'ApiController@createAnuncio');
    Route::get('anuncio/{user_id}', 'ApiController@getAnuncios');
    Route::delete('anuncio/{anuncio_id}', 'ApiController@deleteAnuncioById');

});
