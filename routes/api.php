<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware("auth:api")->group(function(){
    Route::prefix("/files")->group(function(){
        Route::post('/', 'FileController@store')->name("api.file.store");
        Route::delete('/{file}', 'FileController@destroy')->name("api.file.delete");
    });
    Route::prefix("/homescan")->group(function(){
        Route::post('/', 'HomeScanController@search')->name("api.homescan.scan");
        Route::post('/byaddress', 'HomeScanController@searchByAddress')->name("api.homescan.scan.byaddress");
    });

    Route::prefix('/twilio')->group(function(){
        Route::post('/lookup', 'TwilioController@sms')->name('twilio.sms');
    });
});
