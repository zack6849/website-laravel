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

Route::middleware(['auth'])->group(function(){
    Route::prefix('/files')->group(function(){
        Route::get('/', 'FileController@index')->name("file.index");
        Route::get('/new', 'FileController@create')->name("file.create");
        Route::put('/files', 'FileController@store')->name('file.store');
        Route::get('/{file}/delete', 'FileController@requestDestroy')->name("file.request.delete");
        Route::delete('/{file}', 'FileController@destroy')->name("file.delete");
    });
    Route::prefix("/lookup")->group(function(){
        Route::get('/{phone_number}', 'TwilioController@lookup')->name("phone.lookup");
        Route::get('/{phone_number}/raw', 'TwilioController@lookupRaw')->name("phone.lookup.raw");
    });

    //let you logout.
    Route::get('logout', ['as' => 'logout', 'uses' => 'Auth\LoginController@logout']);
});

Route::get('/files/{filename}', 'FileController@show')->name("file.show")->where('filename', '.*');;
//same thing as above, for backwards compat, moved permanently, for search indexers, I guess...
Route::get('/uploads/{file_name}', function ($file_name) {
    return redirect(route('file.show', ['filename' => $file_name]), 301);
});

