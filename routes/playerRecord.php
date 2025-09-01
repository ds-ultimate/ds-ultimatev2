<?php

/*
|--------------------------------------------------------------------------
| playerRecord Routes
|--------------------------------------------------------------------------
|
|
*/

// http://ds-ultimate.de/in/des1/player/%s
Route::get('/{server}/player/{playerID}', function ($server, $playerID) {
    $serCode = substr($server, 0, 2);
    $world = substr($server, 2);
    \App\Http\Controllers\API\PictureController::fixWorldName($serCode, $world);
    return redirect(route("player", [$serCode, $world, $playerID]), 302);
});

// http://ds-ultimate.de/in/des1/ally/%s
Route::get('/{server}/ally/{tribeID}', function ($server, $tribeID) {
    $serCode = substr($server, 0, 2);
    $world = substr($server, 2);
    \App\Http\Controllers\API\PictureController::fixWorldName($serCode, $world);
    return redirect(route("ally", [$serCode, $world, $tribeID]), 302);
});

// http://ds-ultimate.de/in/des1/village/%s
Route::get('/{server}/village/{villageID}', function ($server, $villageID) {
    $serCode = substr($server, 0, 2);
    $world = substr($server, 2);
    \App\Http\Controllers\API\PictureController::fixWorldName($serCode, $world);
    return redirect(route("village", [$serCode, $world, $villageID]), 302);
});


// http://ds-ultimate.de/in/des1/player/%s
Route::get('/{server}/playerConquer/{playerID}', function ($server, $playerID) {
    $serCode = substr($server, 0, 2);
    $world = substr($server, 2);
    \App\Http\Controllers\API\PictureController::fixWorldName($serCode, $world);
    return redirect(route("playerConquer", [$serCode, $world, $playerID]), 302);
});

// http://ds-ultimate.de/in/des1/ally/%s
Route::get('/{server}/allyConquer/{tribeID}', function ($server, $tribeID) {
    $serCode = substr($server, 0, 2);
    $world = substr($server, 2);
    \App\Http\Controllers\API\PictureController::fixWorldName($serCode, $world);
    return redirect(route("allyConquer", [$serCode, $world, $tribeID]), 302);
});

// http://ds-ultimate.de/in/des1/village/%s
Route::get('/{server}/villageConquer/{villageID}', function ($server, $villageID) {
    $serCode = substr($server, 0, 2);
    $world = substr($server, 2);
    \App\Http\Controllers\API\PictureController::fixWorldName($serCode, $world);
    return redirect(route("villageConquer", [$serCode, $world, $villageID]), 302);
});
