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
    Route::get('/prueba', 'ApiController@prueba');

    //Login y registro
    Route::post('registro', 'ApiController@registro');
    Route::post('login', 'ApiController@login');

    //Perfil del usuario
    Route::delete('user/{id}', 'ApiController@deleteUser');
    Route::put('user/{id}', 'ApiController@updateUser');

    //Anuncios
    Route::post('anuncio', 'ApiController@createAnuncio');
    Route::delete('anuncio/{anuncio_id}', 'ApiController@deleteAnuncioById');
    Route::put('anuncio/{anuncio_id}', 'ApiController@updateAnuncioById');
    Route::get('anuncio/{sector}/{provincia}/{precioMax}/{fecha}', 'ApiController@getAnuncios');

    //Anuncios del usuario
    Route::get('user/{user_id}/anuncios', 'ApiController@getAnunciosByUser');

});
