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

Route::post('/git/webhook', 'GitController@index')->name('git');
Route::get('/{server}/activeWorlds', 'FindModelController@getActiveWorldByServer')->name('activeWorldByServer');

Route::get('/{server}/{world}/villageCoords/{x}/{y}', 'FindModelController@getVillageByCoord')->name('villageByCoord');
Route::get('/{server}/{world}/playerName/{name}', 'FindModelController@getPlayerByName')->name('playerByName');
Route::get('/{server}/{world}/allyName/{name}', 'FindModelController@getAllyByName')->name('allyByName');

Route::get('/{server}/{world}/select2Player', 'FindModelController@getSelect2Player')->name('select2Player');
Route::get('/{server}/{world}/select2Ally', 'FindModelController@getSelect2Ally')->name('select2Ally');

Route::get('/{server}/{world}/players', 'DatatablesController@getPlayers')->name('worldPlayer');
Route::get('/{server}/{world}/players/{days}', 'DatatablesController@getPlayersHistory')->name('worldPlayerHistory');
Route::get('/{server}/{world}/allys', 'DatatablesController@getAllys')->name('worldAlly');
Route::get('/{server}/{world}/allys/{days}', 'DatatablesController@getAllysHistory')->name('worldAllyHistory');
Route::get('/{server}/{world}/ally/{ally}', 'DatatablesController@getAllyPlayer')->name('allyPlayer');
Route::get('/{server}/{world}/player/{player}', 'DatatablesController@getPlayerVillage')->name('playerVillage');

Route::get('/{server}/{world}/allyAllyChanges/{type}/{ally}', 'AllyChangeController@getAllyAllyChanges')->name('allyAllyChanges');
Route::get('/{server}/{world}/playerAllyChanges/{type}/{player}', 'AllyChangeController@getPlayerAllyChanges')->name('playerAllyChanges');

Route::get('/{server}/{world}/allyConquer/{type}/{ally}', 'ConquerController@getAllyConquer')->name('allyConquer');
Route::get('/{server}/{world}/playerConquer/{type}/{ally}', 'ConquerController@getPlayerConquer')->name('playerConquer');
Route::get('/{server}/{world}/villageConquer/{type}/{ally}', 'ConquerController@getVillageConquer')->name('villageConquer');
Route::get('/{server}/{world}/worldConquer/{type}', 'ConquerController@getWorldConquer')->name('worldConquer');


/*
 * Picture API:
 * alternative methods
 *  can use:
 *  [**]-{width}-{height}
 *  [**]-w-{width}
 *  [**]-h-{height}
 */
Route::get('/map/{wantedMap}/{token}/{option}-{width}-{height}.{ext}', '\App\Http\Controllers\Tools\MapController@getOptionSizedMapByID')->name('map.options.sized');
Route::get('/map/{wantedMap}/{token}/{width}-{height}.{ext}', '\App\Http\Controllers\Tools\MapController@getSizedMapByID')->name('map.show.sized');
Route::get('/map/{wantedMap}/{token}/map.{ext}', '\App\Http\Controllers\Tools\MapController@getMapByID')->name('map.show');

Route::get('/map/overview/{server}/{world}/{type}/{id}/{width}-{height}.{ext}', '\App\Http\Controllers\Tools\MapController@getSizedOverviewMap')->name('map.overview.sized');
Route::get('/map/overview/{server}/{world}/{type}/{id}/map.{ext}', '\App\Http\Controllers\Tools\MapController@getOverviewMap')->name('map.overview');

Route::get('/picture/{server}-{world}-a-{allyID}-{type}-{width}-{height}.{ext}', 'PictureController@getAllySizedPic')->name('picture.ally.dimension');
Route::get('/picture/{server}-{world}-p-{playerID}-{type}-{width}-{height}.{ext}', 'PictureController@getPlayerSizedPic')->name('picture.player.dimension');
Route::get('/picture/{server}-{world}-v-{villageID}-{type}-{width}-{height}.{ext}', 'PictureController@getVillageSizedPic')->name('picture.village.dimension');

Route::get('/picture/{server}-{world}-a-{allyID}-{type}.{ext}', 'PictureController@getAllyPic')->name('picture.ally');
Route::get('/picture/{server}-{world}-p-{playerID}-{type}.{ext}', 'PictureController@getPlayerPic')->name('picture.player');
Route::get('/picture/{server}-{world}-v-{villageID}-{type}.{ext}', 'PictureController@getVillagePic')->name('picture.village');

