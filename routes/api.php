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

Route::post('/git/webhook', 'GitController@index');

Route::get('/{server}/{world}/data', 'APIController@getWorld')->name('api.worldData');

Route::get('/{server}/{world}/villageCoords/{x}/{y}', 'APIController@getVillageByCoord')->name('api.villageByCoord');

Route::get('/{server}/{world}/players', 'APIController@getPlayers')->name('api.worldPlayer');
Route::get('/{server}/{world}/allys', 'APIController@getAllys')->name('api.worldAlly');
Route::get('/{server}/{world}/ally/{ally}', 'APIController@getAllyPlayer')->name('api.allyPlayer');
Route::get('/{server}/{world}/player/{player}', 'APIController@getPlayerVillage')->name('api.playerVillage');

Route::get('/{server}/{world}/allyAllyChanges/{type}/{ally}', 'APIController@getAllyAllyChanges')->name('api.allyAllyChanges');
Route::get('/{server}/{world}/playerAllyChanges/{type}/{player}', 'APIController@getPlayerAllyChanges')->name('api.playerAllyChanges');

Route::get('/{server}/{world}/allyConquer/{type}/{ally}', 'APIController@getAllyConquer')->name('api.allyConquer');
Route::get('/{server}/{world}/playerConquer/{type}/{ally}', 'APIController@getPlayerConquer')->name('api.playerConquer');
Route::get('/{server}/{world}/villageConquer/{type}/{ally}', 'APIController@getVillageConquer')->name('api.villageConquer');


Route::get('/map/{id}/{token}/{width}-{height}.{ext}', 'Tools\MapController@getSizedMapByID')->name('api.map.showPNG');
Route::get('/map/{id}/{token}/map.{ext}', 'Tools\MapController@getMapByID')->name('api.map.showPNG');
/*
 * Picture API:
 * alternative methods
 *  can use:
 *  [**]-{width}-{height}
 *  [**]-w-{width}
 *  [**]-h-{height}
 */
Route::get('/picture/{server}-{world}-a-{allyID}-{type}-{width}-{height}.{ext}', 'PictureController@getAllySizedPic')->name('api.picture.ally.dimension');
Route::get('/picture/{server}-{world}-p-{playerID}-{type}-{width}-{height}.{ext}', 'PictureController@getPlayerSizedPic')->name('api.picture.player.dimension');
Route::get('/picture/{server}-{world}-v-{villageID}-{type}-{width}-{height}.{ext}', 'PictureController@getVillageSizedPic')->name('api.picture.village.dimension');

Route::get('/picture/{server}-{world}-a-{allyID}-{type}.{ext}', 'PictureController@getAllyPic')->name('api.picture.ally');
Route::get('/picture/{server}-{world}-p-{playerID}-{type}.{ext}', 'PictureController@getPlayerPic')->name('api.picture.player');
Route::get('/picture/{server}-{world}-v-{villageID}-{type}.{ext}', 'PictureController@getVillagePic')->name('api.picture.village');

