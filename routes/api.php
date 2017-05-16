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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// API Group Routes
Route::group(array('prefix' => 'v1', 'middleware' => []), function () {
    // Custom route added to standard Resource
    Route::get('clients', ['uses' => 'ClientController@listV1']);
    Route::get('client/{id}', ['uses' => 'ClientController@find']);
    Route::post('clients', ['uses' => 'ClientController@storeV1']);
});

Route::group(array('prefix' => 'v2', 'middleware' => []), function () {
    Route::post('clients', ['uses' => 'ClientController@storeV2']);
});

Route::group(array('prefix' => 'v3', 'middleware' => []), function () {
    Route::post('clients', ['uses' => 'ClientController@storeV3']);
    Route::get('client/{id}', ['uses' => 'ClientController@findV3']);
});