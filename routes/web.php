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


use App\Http\Controllers\FileController;
use App\Http\Controllers\TwilioController;

Route::get('/', 'PageController@home')->name("home");
Route::get('/photos', 'PageController@photos')->name('photography');
Route::get('/qsos', 'PageController@qsos')->name('qsos');

Auth::routes(['register' => false]);

Route::prefix('/files')->controller(FileController::class)->group(function () {
    //protected file routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/', 'index')->name("file.index");
        Route::get('/new', 'create')->name("file.create");
        Route::put('/files', 'store')->name('file.store');
    });
    //filename specific routes
    Route::prefix('/{file:filename}/')->group(function () {
        //public route
        Route::get('/', 'show')->name('file.show');
        //protected filename specific routes
        Route::middleware(['auth'])->group(function () {
            Route::get('/delete', 'delete')->name('file.delete');
            Route::post('/delete', 'destroy')->name("file.destroy");
        });
    });

});

Route::prefix('/lookup')->middleware(['throttle'])->controller(TwilioController::class)->group(function() {
    Route::get('/', 'index')->name("phone.lookup.index");
    Route::get('/{phone_number}', 'lookup')->name("phone.lookup");
    Route::get('/{phone_number}/raw', 'rawLookup')->name("phone.lookup.raw");
});

//Redirects for files to new URl format
Route::get('/uploads/{file:filename}', function(\App\Models\File $file){
    return redirect($file->url, 301)->name('file.old.redirect');
});

