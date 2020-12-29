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

Route::group(['middleware' => 'throttle:60,1'], function() {
    Route::middleware('auth:api')->get('/user', function (Request $request) {
        return $request->user();
    });
});

Route::group(['middleware' => 'throttle:10,1'], function() {
    Route::post('/git/webhook', [\App\Http\Controllers\API\GitController::class, 'index'])->name('git');
});

Route::group(['middleware' => 'throttle:240,1'], function() {
    Route::get('/{server}/activeWorlds', [\App\Http\Controllers\API\FindModelController::class, 'getActiveWorldByServer'])->name('activeWorldByServer');

    Route::get('/{server}/{world}/villageCoords/{x}/{y}', [\App\Http\Controllers\API\FindModelController::class, 'getVillageByCoord'])->name('villageByCoord');
    Route::get('/{server}/{world}/playerName/{name}', [\App\Http\Controllers\API\FindModelController::class, 'getPlayerByName'])->name('playerByName');
    Route::get('/{server}/{world}/allyName/{name}', [\App\Http\Controllers\API\FindModelController::class, 'getAllyByName'])->name('allyByName');

    Route::get('/{server}/{world}/select2Player', [\App\Http\Controllers\API\FindModelController::class, 'getSelect2Player'])->name('select2Player');
    Route::get('/{server}/{world}/select2Ally', [\App\Http\Controllers\API\FindModelController::class, 'getSelect2Ally'])->name('select2Ally');
});

Route::group(['middleware' => 'throttle:120,1'], function() {
    Route::get('/{server}/{world}/players', [\App\Http\Controllers\API\DatatablesController::class, 'getPlayers'])->name('worldPlayer');
    Route::get('/{server}/{world}/players/{days}', [\App\Http\Controllers\API\DatatablesController::class, 'getPlayersHistory'])->name('worldPlayerHistory');
    Route::get('/{server}/{world}/allys', [\App\Http\Controllers\API\DatatablesController::class, 'getAllys'])->name('worldAlly');
    Route::get('/{server}/{world}/allys/{days}', [\App\Http\Controllers\API\DatatablesController::class, 'getAllysHistory'])->name('worldAllyHistory');
    Route::get('/{server}/{world}/ally/{ally}', [\App\Http\Controllers\API\DatatablesController::class, 'getAllyPlayer'])->name('allyPlayer');
    Route::get('/{server}/{world}/player/{player}', [\App\Http\Controllers\API\DatatablesController::class, 'getPlayerVillage'])->name('playerVillage');

    Route::get('/{server}/{world}/allyAllyChanges/{type}/{ally}', [\App\Http\Controllers\API\AllyChangeController::class, 'getAllyAllyChanges'])->name('allyAllyChanges');
    Route::get('/{server}/{world}/playerAllyChanges/{type}/{player}', [\App\Http\Controllers\API\AllyChangeController::class, 'getPlayerAllyChanges'])->name('playerAllyChanges');

    Route::get('/{server}/{world}/allyConquer/{type}/{ally}', [\App\Http\Controllers\API\ConquerController::class, 'getAllyConquer'])->name('allyConquer');
    Route::get('/{server}/{world}/playerConquer/{type}/{ally}', [\App\Http\Controllers\API\ConquerController::class, 'getPlayerConquer'])->name('playerConquer');
    Route::get('/{server}/{world}/villageConquer/{type}/{ally}', [\App\Http\Controllers\API\ConquerController::class, 'getVillageConquer'])->name('villageConquer');
    Route::get('/{server}/{world}/worldConquer/{type}', [\App\Http\Controllers\API\ConquerController::class, 'getWorldConquer'])->name('worldConquer');
});


Route::group(['middleware' => 'throttle:60,1'], function() {
    /*
     * Picture API:
     * alternative methods
     *  can use:
     *  [**]-{width}-{height}
     *  [**]-w-{width}
     *  [**]-h-{height}
     */
    Route::get('/map/{wantedMap}/{token}/{option}-{width}-{height}.{ext}', [\App\Http\Controllers\Tools\MapController::class, 'getOptionSizedMapByID'])->name('map.options.sized');
    Route::get('/map/{wantedMap}/{token}/{width}-{height}.{ext}', [\App\Http\Controllers\Tools\MapController::class, 'getSizedMapByID'])->name('map.show.sized');
    Route::get('/map/{wantedMap}/{token}/map.{ext}', [\App\Http\Controllers\Tools\MapController::class, 'getMapByID'])->name('map.show');

    Route::get('/map/overview/{server}/{world}/{type}/{id}/{width}-{height}.{ext}', [\App\Http\Controllers\Tools\MapController::class, 'getSizedOverviewMap'])->name('map.overview.sized');
    Route::get('/map/overview/{server}/{world}/{type}/{id}/map.{ext}', [\App\Http\Controllers\Tools\MapController::class, 'getOverviewMap'])->name('map.overview');

    Route::get('/picture/{server}-{world}-a-{allyID}-{type}-{width}-{height}.{ext}', [\App\Http\Controllers\API\PictureController::class, 'getAllySizedPic'])->name('picture.ally.dimension');
    Route::get('/picture/{server}-{world}-p-{playerID}-{type}-{width}-{height}.{ext}', [\App\Http\Controllers\API\PictureController::class, 'getPlayerSizedPic'])->name('picture.player.dimension');
    Route::get('/picture/{server}-{world}-v-{villageID}-{type}-{width}-{height}.{ext}', [\App\Http\Controllers\API\PictureController::class, 'getVillageSizedPic'])->name('picture.village.dimension');

    Route::get('/picture/{server}-{world}-a-{allyID}-{type}.{ext}', [\App\Http\Controllers\API\PictureController::class, 'getAllyPic'])->name('picture.ally');
    Route::get('/picture/{server}-{world}-p-{playerID}-{type}.{ext}', [\App\Http\Controllers\API\PictureController::class, 'getPlayerPic'])->name('picture.player');
    Route::get('/picture/{server}-{world}-v-{villageID}-{type}.{ext}', [\App\Http\Controllers\API\PictureController::class, 'getVillagePic'])->name('picture.village');
});
