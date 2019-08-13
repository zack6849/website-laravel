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

Route::middleware("auth:api")->post("/files", "FileController@api_store")->name("api.file.store");
Route::middleware("auth:api")->delete("/files/{file_id}", "FileController@delete")->name("api.file.delete");
