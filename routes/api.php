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
Route::get('/{server}/activeWorlds', 'APIController@getActiveWorldByServer')->name('activeWorldByServer');
Route::get('/{server}/{world}/data', 'APIController@getWorld')->name('worldData');

Route::get('/{server}/{world}/villageCoords/{x}/{y}', 'APIController@getVillageByCoord')->name('villageByCoord');
Route::get('/{server}/{world}/playerName/{name}', 'APIController@getPlayerByName')->name('playerByName');
Route::get('/{server}/{world}/allyName/{name}', 'APIController@getAllyByName')->name('allyByName');

Route::get('/{server}/{world}/searchPlayer', 'APIController@getSearchPlayerByName')->name('searchPlayerByName');
Route::get('/{server}/{world}/searchAlly', 'APIController@getSearchAllyByName')->name('searchAllyByName');

Route::get('/{server}/{world}/players', 'APIController@getPlayers')->name('worldPlayer');
Route::get('/{server}/{world}/players/{days}', 'APIController@getPlayersHistory')->name('worldPlayerHistory');
Route::get('/{server}/{world}/allys', 'APIController@getAllys')->name('worldAlly');
Route::get('/{server}/{world}/allys/{days}', 'APIController@getAllysHistory')->name('worldAllyHistory');
Route::get('/{server}/{world}/ally/{ally}', 'APIController@getAllyPlayer')->name('allyPlayer');
Route::get('/{server}/{world}/player/{player}', 'APIController@getPlayerVillage')->name('playerVillage');

Route::get('/{server}/{world}/allyAllyChanges/{type}/{ally}', 'APIController@getAllyAllyChanges')->name('allyAllyChanges');
Route::get('/{server}/{world}/playerAllyChanges/{type}/{player}', 'APIController@getPlayerAllyChanges')->name('playerAllyChanges');

Route::get('/{server}/{world}/allyConquer/{type}/{ally}', 'APIController@getAllyConquer')->name('allyConquer');
Route::get('/{server}/{world}/playerConquer/{type}/{ally}', 'APIController@getPlayerConquer')->name('playerConquer');
Route::get('/{server}/{world}/villageConquer/{type}/{ally}', 'APIController@getVillageConquer')->name('villageConquer');

Route::get('/{server}/{world}/signature/{type}/{player}', 'APIController@signature')->name('signature');


/*
 * Picture API:
 * alternative methods
 *  can use:
 *  [**]-{width}-{height}
 *  [**]-w-{width}
 *  [**]-h-{height}
 */
Route::get('/map/{wantedMap}/{token}/{option}-{width}-{height}.{ext}', 'Tools\MapController@getOptionSizedMapByID')->name('map.options.sized');
Route::get('/map/{wantedMap}/{token}/{width}-{height}.{ext}', 'Tools\MapController@getSizedMapByID')->name('map.show.sized');
Route::get('/map/{wantedMap}/{token}/map.{ext}', 'Tools\MapController@getMapByID')->name('map.show');

Route::get('/map/overview/{server}/{world}/{type}/{id}/{width}-{height}.{ext}', 'Tools\MapController@getSizedOverviewMap')->name('map.overview.sized');
Route::get('/map/overview/{server}/{world}/{type}/{id}/map.{ext}', 'Tools\MapController@getOverviewMap')->name('map.overview');

Route::get('/picture/{server}-{world}-a-{allyID}-{type}-{width}-{height}.{ext}', 'PictureController@getAllySizedPic')->name('picture.ally.dimension');
Route::get('/picture/{server}-{world}-p-{playerID}-{type}-{width}-{height}.{ext}', 'PictureController@getPlayerSizedPic')->name('picture.player.dimension');
Route::get('/picture/{server}-{world}-v-{villageID}-{type}-{width}-{height}.{ext}', 'PictureController@getVillageSizedPic')->name('picture.village.dimension');

Route::get('/picture/{server}-{world}-a-{allyID}-{type}.{ext}', 'PictureController@getAllyPic')->name('picture.ally');
Route::get('/picture/{server}-{world}-p-{playerID}-{type}.{ext}', 'PictureController@getPlayerPic')->name('picture.player');
Route::get('/picture/{server}-{world}-v-{villageID}-{type}.{ext}', 'PictureController@getVillagePic')->name('picture.village');

