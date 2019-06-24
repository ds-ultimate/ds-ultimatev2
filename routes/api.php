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

Route::get('/{server}/{world}/players', 'APIController@getPlayers')->name('api.worldPlayer');
Route::get('/{server}/{world}/allys', 'APIController@getAllys')->name('api.worldAlly');
Route::get('/{server}/{world}/ally/{ally}', 'APIController@getAllyPlayer')->name('api.allyPlayer');
