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

Route::get('/', function () {
    return view('home');
});

Auth::routes(['register' => false]);

//let you logout.
Route::get('logout', ['as' => 'logout', 'uses' => 'Auth\LoginController@logout']);


Route::get('/files', 'FileController@index')->name("file.index");
Route::get('/files/new', 'FileController@create')->name("file.create");
Route::put('/files', 'FileController@store')->name('file.store');
Route::get('/files/{file_id}', 'FileController@show')->name("file.show");
Route::delete('/files/{file_id}', 'FileController@destroy')->name("file.delete");
