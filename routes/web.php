<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function(){
    return view('auth.login');
});
Auth::routes();


Route::get('/home', 'HomeController@index')->name('home');

Route::resource('eventos', 'ActividadController');
Route::get('/eventos', 'ActividadController@index')->name('eventosvista');
// Route::post('/agenda', 'ActividadController@store')->name('addEvento');
Route::get('/eventos/show', 'ActividadController@show')->name('eventos');
// Route::put('/agenda', 'ActividadController@update')->name('editEvento');