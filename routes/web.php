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
})->name('home');

//todo: this should probably be proper controllers
Route::get('/photos', function (){
    return view('photography');
})->name('photography');

Auth::routes(['register' => false]);

//let you logout.
Route::get('logout', ['as' => 'logout', 'uses' => 'Auth\LoginController@logout']);


Route::get('/files', 'FileController@index')->name("file.index")->middleware('auth');
Route::post('/ajax/files', 'FileController@ajaxIndex')->name("file.ajax.index")->middleware('auth');
Route::get('/files/new', 'FileController@create')->name("file.create")->middleware('auth');
Route::put('/files', 'FileController@store')->name('file.store')->middleware('auth');
Route::get('/files/{file_name}', 'FileController@show')->name("file.show");
//same thing as above, for backwards compat, moved permanently, for search indexers, I guess...
Route::get('/uploads/{file_name}', function ($file_name){
   return redirect(route('file.show', ['file_name' => $file_name]), 301);
});
Route::delete('/files/{file_id}', 'FileController@destroy')->name("file.delete")->middleware('auth');
Route::get('/files/{file_id}/delete', 'FileController@requestDestroy')->name("file.request.delete")->middleware('auth');

Route::get('/lookup/{phone_number}', 'TwilioController@lookup')->middleware('auth');
