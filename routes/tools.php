<?php

/*
|--------------------------------------------------------------------------
| Tool Routes
|--------------------------------------------------------------------------
|
|
*/

Route::post('/tools/follow', [\App\Http\Controllers\FollowController::class, 'createFollowTool'])->name('follow');

//Distance calculator
Route::get('/{server}/{world}/tools/distanceCalc', [\App\Http\Controllers\Tools\DistanceCalcController::class, 'index'])->name('distanceCalc');


//Attack planner
Route::get('/tools/create/attackPlanner/{server}/{world}', [\App\Http\Controllers\Tools\AttackPlannerController::class, 'index'])->name('attackPlannerNew');

Route::delete('/tools/attackPlanner/attackListItem/massDestroy', [\App\Http\Controllers\Tools\AttackPlannerItemController::class, 'massDestroy'])->name('attackListItem.massDestroy');
Route::resource('/tools/attackPlanner/attackListItem',\App\Http\Controllers\Tools\AttackPlannerItemController::class, [
    'only' => ['store', 'destroy', 'update'],
]);
Route::post('/tools/attackPlanner/attackListItem/multiedit', [\App\Http\Controllers\Tools\AttackPlannerItemController::class, 'multiedit'])->name('attackListItemMultiedit');
Route::post('/tools/attackPlanner/attackListItem/sendattack', [\App\Http\Controllers\Tools\AttackPlannerItemController::class, 'sendattack'])->name('attackListItemSendattack');
Route::get('/tools/attackPlanner/attackListItem/data/{attackList}/{key}',[\App\Http\Controllers\Tools\AttackPlannerItemController::class, 'data'])->name('attackListItem.data');

Route::post('/tools/attackPlanner/{attackList}/importWB/{key}', [\App\Http\Controllers\Tools\AttackPlannerController::class, 'importWB'])->name('attackPlannerImportWB');
Route::post('/tools/attackPlanner/{attackList}/title/{key}/{title}', [\App\Http\Controllers\Tools\AttackPlannerController::class, 'title'])->name('attackPlannerTitle');
Route::get('/tools/attackPlanner/{attackList}/{mode}/{key}', [\App\Http\Controllers\Tools\AttackPlannerController::class, 'mode'])->name('attackPlannerMode');
Route::post('/tools/attackPlanner/{attackList}/{mode}/{key}', [\App\Http\Controllers\Tools\AttackPlannerController::class, 'modePost'])->name('attackPlannerModePost');
Route::delete('/tools/attackPlanner/{attackList}/{key}', [\App\Http\Controllers\Tools\AttackPlannerController::class, 'destroy'])->name('attackPlannerDestroy');
Route::middleware('auth')->group(function() {
    Route::post('/tools/attackPlannerSound/upload', [\App\Http\Controllers\Tools\AttackPlannerSoundController::class, 'uploadSound'])->name('attackPlannerSound.upload');
    Route::get('/tools/attackPlannerSound/fetch/{sound}', [\App\Http\Controllers\Tools\AttackPlannerSoundController::class, 'getSound'])->name('attackPlannerSound.fetch');
    Route::post('/tools/attackPlannerSound/name/{sound}', [\App\Http\Controllers\Tools\AttackPlannerSoundController::class, 'editName'])->name('attackPlannerSound.editName');
    Route::post('/tools/attackPlannerSound/delete/{sound}', [\App\Http\Controllers\Tools\AttackPlannerSoundController::class, 'deleteSound'])->name('attackPlannerSound.delete');
});

Route::withoutMiddleware('localize')->group(function() {
    Route::post('/toolAPI/attackPlanner/create', [\App\Http\Controllers\Tools\AttackPlannerAPIController::class, 'create'])->name('attackPlannerAPICreate');
    Route::post('/toolAPI/attackPlanner/createItems', [\App\Http\Controllers\Tools\AttackPlannerAPIController::class, 'itemCreate'])->name('attackPlannerAPICreateItems');
    Route::post('/toolAPI/attackPlanner/destroyOutdated', [\App\Http\Controllers\Tools\AttackPlannerAPIController::class, 'destroyOutdated'])->name('attackPlannerAPIDestroyOutdated');
    Route::post('/toolAPI/attackPlanner/clear', [\App\Http\Controllers\Tools\AttackPlannerAPIController::class, 'clear'])->name('attackPlannerAPIClear');
    Route::post('/toolAPI/attackPlanner/fetch', [\App\Http\Controllers\Tools\AttackPlannerAPIController::class, 'fetchItems'])->name('attackPlannerAPIFetch');
});

//Map Tool
Route::get('/tools/create/map/{server}/{world}', [\App\Http\Controllers\Tools\MapController::class, 'new'])->name('mapNew');
Route::get('/tools/map/{wantedMap}/{action}/{key}', [\App\Http\Controllers\Tools\MapController::class, 'mode'])->name('map.mode');
Route::post('/tools/map/{wantedMap}/{action}/{key}', [\App\Http\Controllers\Tools\MapController::class, 'modePost'])->name('map.modePost');
Route::delete('/tools/map/{wantedMap}/{key}', [\App\Http\Controllers\Tools\MapController::class, 'destroy'])->name('mapDestroy');

Route::get('/{server}/{world}/maptop10', [\App\Http\Controllers\Tools\MapController::class, 'mapTop10'])->name('top10');
Route::get('/{server}/{world}/maptop10p', [\App\Http\Controllers\Tools\MapController::class, 'mapTop10P'])->name('top10p');


//Point calculator
Route::get('/{server}/{world}/tools/pointCalc', [\App\Http\Controllers\Tools\PointCalcController::class, 'index'])->name('pointCalc');


//TableGenerator
Route::get('/{server}/{world}/tools/tableGenerator', [\App\Http\Controllers\Tools\TableGeneratorController::class, 'index'])->name('tableGenerator');
Route::post('/tools/tableGenerator', [\App\Http\Controllers\Tools\TableGeneratorController::class, 'data'])->name('tableGeneratorData');


//Account manager database
Route::get('/tools/accMgrDB/index', [\App\Http\Controllers\Tools\AccMgrDB::class, 'index'])->name('accMgrDB.index');
Route::get('/tools/accMgrDB/create', [\App\Http\Controllers\Tools\AccMgrDB::class, 'create'])->name('accMgrDB.create');
Route::post('/tools/accMgrDB/import', [\App\Http\Controllers\Tools\AccMgrDB::class, 'import'])->name('accMgrDB.import');
Route::post('/tools/accMgrDB/save', [\App\Http\Controllers\Tools\AccMgrDB::class, 'save'])->name('accMgrDB.save');
Route::get('/tools/accMgrDB/show/{template}', [\App\Http\Controllers\Tools\AccMgrDB::class, 'show'])->name('accMgrDB.show');
Route::get('/tools/accMgrDB/show/{template}/{key}', [\App\Http\Controllers\Tools\AccMgrDB::class, 'show'])->name('accMgrDB.show_key');
Route::get('/tools/accMgrDB/edit/{template}', [\App\Http\Controllers\Tools\AccMgrDB::class, 'edit'])->name('accMgrDB.edit');
Route::delete('/tools/accMgrDB/delete', [\App\Http\Controllers\Tools\AccMgrDB::class, 'delete'])->name('accMgrDB.delete');
Route::get('/{server}/{world}/tools/accountmanagerdatabase/index', [\App\Http\Controllers\Tools\AccMgrDB::class, 'index_world'])->name('accMgrDB.index_world');
Route::get('/tools/accMgrDB/api/index', [\App\Http\Controllers\Tools\AccMgrDB::class, 'api'])->name('accMgrDB.index_api');
Route::post('/tools/accMgrDB/api/rating/{template}', [\App\Http\Controllers\Tools\AccMgrDB::class, 'apiRating'])->name('accMgrDB.rating_api');


//Animated world history map
Route::get('/tools/create/animHistMap/{server}/{world}', [\App\Http\Controllers\Tools\AnimatedHistoryMapController::class, 'create'])->name('animHistMap.create');
Route::get('/tools/animHistMap/renderStatus/{wantedJob}/{key}', [\App\Http\Controllers\Tools\AnimatedHistoryMapController::class, 'renderStatus'])->name('animHistMap.renderStatus');
Route::get('/tools/animHistMap/renderRerun/{wantedJob}/{key}', [\App\Http\Controllers\Tools\AnimatedHistoryMapController::class, 'renderRerun'])->name('animHistMap.renderRerun');
Route::get('/tools/animHistMap/api/renderStatus/{wantedJob}/{key}', [\App\Http\Controllers\Tools\AnimatedHistoryMapController::class, 'apiRenderStatus'])->name('animHistMap.apiRenderStatus');
Route::get('/tools/animHistMap/renderDownload/{wantedJob}/{key}/{format}', [\App\Http\Controllers\Tools\AnimatedHistoryMapController::class, 'download'])->name('animHistMap.download');
Route::get('/tools/animHistMap/{wantedMap}/preview/{key}/{histIdx}.{ext}', [\App\Http\Controllers\Tools\AnimatedHistoryMapController::class, 'preview'])->name('animHistMap.preview');
Route::get('/tools/animHistMap/{wantedMap}/{action}/{key}', [\App\Http\Controllers\Tools\AnimatedHistoryMapController::class, 'mode'])->name('animHistMap.mode');
Route::post('/tools/animHistMap/{wantedMap}/{action}/{key}', [\App\Http\Controllers\Tools\AnimatedHistoryMapController::class, 'modePost'])->name('animHistMap.modePost');
Route::delete('/tools/animHistMap/{wantedMap}/delete/{key}', [\App\Http\Controllers\Tools\AnimatedHistoryMapController::class, 'destroyAnimHistMapMap'])->name('animHistMap.destroyAnimHistMapMap');
Route::delete('/tools/animHistJob/{wantedJob}/delete/{key}', [\App\Http\Controllers\Tools\AnimatedHistoryMapController::class, 'destroyAnimHistMapJob'])->name('animHistMap.destroyAnimHistMapJob');


//The great siege calculator
Route::get('/{server}/{world}/tools/greatSiegeCalc', [\App\Http\Controllers\Tools\GreatSiegeCalcController::class, 'index'])->name('greatSiegeCalc');
