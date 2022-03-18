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


Route::get('/', 'PageController@home')->name("home");
Route::get('/photos', 'PageController@photos')->name('photography');

Auth::routes(['register' => false, ]);

//protected routes.
Route::middleware(['auth'])->group(function(){
    Route::prefix('/files')->group(function(){
        Route::get('/', 'FileController@index')->name("file.index");
        Route::get('/new', 'FileController@create')->name("file.create");
        Route::put('/files', 'FileController@store')->name('file.store');
        Route::get('{file:filename}/delete', 'FileController@delete')->name('file.delete');
        Route::post('{file:filename}/delete', 'FileController@destroy')->name("file.destroy");
    });
    Route::prefix("/lookup")->group(function(){
        Route::get('/{phone_number}', 'TwilioController@lookup')->name("phone.lookup");
        Route::get('/{phone_number}/raw', 'TwilioController@lookupRaw')->name("phone.lookup.raw");
    });
});

//public routes.
Route::get('/files/{file:filename}', 'FileController@show')->name("file.show");

//same thing as above, for backwards compatability. make any indexed uploads point to the new endpoint
Route::get('/uploads/{file_name}', function ($file_name) {
    return redirect(route('file.show', ['filename' => $file_name]), 301);
});

