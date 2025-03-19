<?php
declare(strict_types=1);

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
Route::middleware("auth:api")->group(function () {
    Route::prefix("/files")->group(function () {
        Route::post('/', 'FileController@store')->name("api.file.store");
        Route::delete('/{file}', 'FileController@destroy')->name("api.file.delete");
    });
    Route::prefix("/homescan")->group(function () {
        Route::post('/', 'HomeScanController@search')->name("api.homescan.scan");
        Route::post('/byaddress', 'HomeScanController@searchByAddress')->name("api.homescan.scan.byaddress");
    });

    Route::prefix('/twilio')->group(function () {
        Route::post('/lookup', 'TwilioController@twilioResponse')->name('twilio.sms');
    });

    Route::prefix('/radio')->group(function () {
        Route::post('spots', 'HamAlertSpotsController@save');
    });
});
Route::prefix('radio')->group(function(){
    Route::get('qsos/band/{band?}/mode/{mode?}', 'LogbookController@getGeoJSON');
    Route::get('modes', 'LogbookController@getWorkedModes');
    Route::get('bands', 'LogbookController@getWorkedBands');
    Route::get('spots', 'HamAlertSpotsController@index');
});
