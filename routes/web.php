<?php
declare(strict_types=1);


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


use App\Models\LostPet;
use Carbon\Carbon;

Route::get('/', 'PageController@home')->name("home");
Route::get('/photos', 'PageController@photos')->name('photography');
Route::get('/qsos', 'PageController@qsos')->name('qsos');

Auth::routes(['register' => false,]);

//protected routes.
Route::middleware(['auth'])->group(function () {
    Route::prefix('/files')->group(function () {
        Route::get('/', 'FileController@index')->name("file.index");
        Route::get('/new', 'FileController@create')->name("file.create");
        Route::put('/files', 'FileController@store')->name('file.store');
        Route::get('{file:filename}/delete', 'FileController@delete')->name('file.delete');
        Route::post('{file:filename}/delete', 'FileController@destroy')->name("file.destroy");
    });
    Route::prefix("/lookup")->group(function () {
        Route::get('/{phone_number}', 'TwilioController@lookup')->name("phone.lookup");
        Route::get('/{phone_number}/raw', 'TwilioController@raw')->name("phone.lookup.raw");
    });
});

//public routes.
Route::get('/files/{file:filename}', 'FileController@show')->name("file.show");

//same thing as above, for backwards compatability. make any indexed uploads point to the new endpoint
Route::get('/uploads/{file_name}', function ($file_name) {
    return redirect(route('file.show', ['file' => $file_name]), 301);
});

Route::get('/lost-pets', function(){
    return view('pages.lost-pets', [
        'pets' => LostPet::query()
            ->where('intake_date', '>=', Carbon::parse('2023-12-21')->startOfDay())
            ->orderByDesc('intake_date')
            ->get()
    ]);
});

