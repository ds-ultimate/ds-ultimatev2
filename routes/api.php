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

Route::group(['middleware' => 'throttle:10,1'], function() {
    Route::post('/git/webhook', [\App\Http\Controllers\API\GitController::class, 'index'])->name('git');
});

Route::get('/worldPopup/{world}/{playerId}', [\App\Http\Controllers\API\FindModelController::class, 'getWorldPopup'])->name('worldPopup');
Route::middleware(['throttle:24,0.1'])->group(function() {
    Route::get('/{server}/activeWorlds', [\App\Http\Controllers\API\FindModelController::class, 'getActiveWorldByServer'])->name('activeWorldByServer');

    Route::middleware(['worldMaintenance'])->group(function() {
        Route::get('/{world}/villageCoordsPreview/{histIdx}/{x}/{y}', [\App\Http\Controllers\API\FindModelController::class, 'getVillagePreviewByCoord'])->name('villagePreviewByCoord');
        Route::get('/{world}/villageCoords/{x}/{y}', [\App\Http\Controllers\API\FindModelController::class, 'getVillageByCoord'])->name('villageByCoord');

        Route::get('/{world}/select2Player', [\App\Http\Controllers\API\FindModelController::class, 'getSelect2Player'])->name('select2Player');
        Route::get('/{world}/select2Ally', [\App\Http\Controllers\API\FindModelController::class, 'getSelect2Ally'])->name('select2Ally');
        Route::get('/{world}/select2PlayerTop', [\App\Http\Controllers\API\FindModelController::class, 'getSelect2PlayerTop'])->name('select2PlayerTop');
        Route::get('/{world}/select2AllyTop', [\App\Http\Controllers\API\FindModelController::class, 'getSelect2AllyTop'])->name('select2AllyTop');
    });
});

Route::middleware(['throttle:12,0.2', 'worldMaintenance'])->group(function() {
    Route::get('/{world}/players', [\App\Http\Controllers\API\DatatablesController::class, 'getPlayers'])->name('worldPlayer');
    Route::get('/{world}/players/{days}', [\App\Http\Controllers\API\DatatablesController::class, 'getPlayersHistory'])->name('worldPlayerHistory');
    Route::get('/{world}/allys', [\App\Http\Controllers\API\DatatablesController::class, 'getAllys'])->name('worldAlly');
    Route::get('/{world}/allys/{days}', [\App\Http\Controllers\API\DatatablesController::class, 'getAllysHistory'])->name('worldAllyHistory');
    Route::get('/{world}/ally/{ally}/player', [\App\Http\Controllers\API\DatatablesController::class, 'getAllyPlayer'])->name('allyPlayer');
    Route::get('/{world}/ally/{ally}/history', [\App\Http\Controllers\API\DatatablesController::class, 'getAllyHistory'])->name('allyHistory');
    Route::get('/{world}/ally/{ally}/bashRanking', [\App\Http\Controllers\API\DatatablesController::class, 'getAllyPlayerBashRanking'])->name('allyPlayerBashRanking');
    Route::get('/{world}/player/{player}/villages', [\App\Http\Controllers\API\DatatablesController::class, 'getPlayerVillage'])->name('playerVillage');
    Route::get('/{world}/player/{player}/history', [\App\Http\Controllers\API\DatatablesController::class, 'getPlayerHistory'])->name('playerHistory');

    Route::get('/{world}/allyAllyChanges/{type}/{ally}', [\App\Http\Controllers\API\AllyChangeController::class, 'getAllyAllyChanges'])->name('allyAllyChanges');
    Route::get('/{world}/playerAllyChanges/{type}/{player}', [\App\Http\Controllers\API\AllyChangeController::class, 'getPlayerAllyChanges'])->name('playerAllyChanges');

    Route::get('/{world}/allyConquer/{type}/{ally}', [\App\Http\Controllers\API\ConquerController::class, 'getAllyConquer'])->name('allyConquer');
    Route::get('/{world}/playerConquer/{type}/{ally}', [\App\Http\Controllers\API\ConquerController::class, 'getPlayerConquer'])->name('playerConquer');
    Route::get('/{world}/villageConquer/{type}/{ally}', [\App\Http\Controllers\API\ConquerController::class, 'getVillageConquer'])->name('villageConquer');
    Route::get('/{world}/worldConquer/{type}', [\App\Http\Controllers\API\ConquerController::class, 'getWorldConquer'])->name('worldConquer');
    Route::get('/{world}/conquerDaily/{type}', [\App\Http\Controllers\API\ConquerController::class, 'getConquerDaily'])->name('conquerDaily');
    Route::get('/{world}/conquerDaily/{type}/{day}', [\App\Http\Controllers\API\ConquerController::class, 'getConquerDaily']);
});

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

Route::group(['middleware' => 'throttle:6,0.1'], function() {
    Route::get('/map/overview/{server}/{world}/{type}/{id}/{width}-{height}.{ext}', [\App\Http\Controllers\Tools\MapController::class, 'getSizedOverviewMap'])->name('map.overview.sized');
    Route::get('/map/overview/{server}/{world}/{type}/{id}/map.{ext}', [\App\Http\Controllers\Tools\MapController::class, 'getOverviewMap'])->name('map.overview');
});

Route::group(['middleware' => 'throttle:24,0.1'], function() {
    Route::get('/picture/{server}-{world}-a-{allyID}-{type}-{width}-{height}.{ext}', [\App\Http\Controllers\API\PictureController::class, 'getAllySizedPic'])->name('picture.ally.dimension');
    Route::get('/picture/{server}-{world}-p-{playerID}-{type}-{width}-{height}.{ext}', [\App\Http\Controllers\API\PictureController::class, 'getPlayerSizedPic'])->name('picture.player.dimension');
    Route::get('/picture/{server}-{world}-v-{villageID}-{type}-{width}-{height}.{ext}', [\App\Http\Controllers\API\PictureController::class, 'getVillageSizedPic'])->name('picture.village.dimension');

    Route::get('/picture/{server}-{world}-ally-{allyID}-{type}-{width}-{height}.{ext}', [\App\Http\Controllers\API\PictureController::class, 'getAllySizedPic'])->name('picture.ally.dimension');
    Route::get('/picture/{server}-{world}-player-{playerID}-{type}-{width}-{height}.{ext}', [\App\Http\Controllers\API\PictureController::class, 'getPlayerSizedPic'])->name('picture.player.dimension');
    Route::get('/picture/{server}-{world}-village-{villageID}-{type}-{width}-{height}.{ext}', [\App\Http\Controllers\API\PictureController::class, 'getVillageSizedPic'])->name('picture.village.dimension');

    Route::get('/picture/{server}-{world}-a-{allyID}-{type}.{ext}', [\App\Http\Controllers\API\PictureController::class, 'getAllyPic'])->name('picture.ally');
    Route::get('/picture/{server}-{world}-p-{playerID}-{type}.{ext}', [\App\Http\Controllers\API\PictureController::class, 'getPlayerPic'])->name('picture.player');
    Route::get('/picture/{server}-{world}-v-{villageID}-{type}.{ext}', [\App\Http\Controllers\API\PictureController::class, 'getVillagePic'])->name('picture.village');

    Route::get('/picture/{server}-{world}-ally-{allyID}-{type}.{ext}', [\App\Http\Controllers\API\PictureController::class, 'getAllyPic'])->name('picture.ally');
    Route::get('/picture/{server}-{world}-player-{playerID}-{type}.{ext}', [\App\Http\Controllers\API\PictureController::class, 'getPlayerPic'])->name('picture.player');
    Route::get('/picture/{server}-{world}-village-{villageID}-{type}.{ext}', [\App\Http\Controllers\API\PictureController::class, 'getVillagePic'])->name('picture.village');
});
