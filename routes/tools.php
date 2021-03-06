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
Route::get('/{server}/{world}/tools/attackPlanner', [\App\Http\Controllers\Tools\AttackPlannerController::class, 'index'])->name('attackPlannerNew');

Route::resource('/tools/attackPlanner/attackListItem',\App\Http\Controllers\Tools\AttackPlannerItemController::class, [
    'only' => ['store', 'destroy', 'update'],
]);
Route::post('/tools/attackPlanner/attackListItem/multiedit', [\App\Http\Controllers\Tools\AttackPlannerItemController::class, 'multiedit'])->name('attackListItemMultiedit');
Route::post('/tools/attackPlanner/attackListItem/sendattack', [\App\Http\Controllers\Tools\AttackPlannerItemController::class, 'sendattack'])->name('attackListItemSendattack');
Route::get('/tools/attackPlanner/attackListItem/data/{attackList}/{key}',[\App\Http\Controllers\Tools\AttackPlannerItemController::class, 'data'])->name('attackListItem.data');

Route::post('/tools/attackPlanner/{attackList}/importWB/{key}', [\App\Http\Controllers\Tools\AttackPlannerController::class, 'importWB'])->name('attackPlannerImportWB');
Route::post('/tools/attackPlanner/{attackList}/title/{key}/{title}', [\App\Http\Controllers\Tools\AttackPlannerController::class, 'title'])->name('attackPlannerTitle');
Route::get('/tools/attackPlanner/{attackList}/{mode}/{key}', [\App\Http\Controllers\Tools\AttackPlannerController::class, 'mode'])->name('attackPlannerMode');
Route::delete('/tools/attackPlanner/{attackList}/{key}', [\App\Http\Controllers\Tools\AttackPlannerController::class, 'destroy'])->name('attackPlannerDestroy');


//Map Tool
Route::get('/{server}/{world}/tools/map/create', [\App\Http\Controllers\Tools\MapController::class, 'new'])->name('mapNew');
Route::post('/tools/map/{wantedMap}/title/{key}/{title}', [\App\Http\Controllers\Tools\MapController::class, 'title'])->name('mapTitle');
Route::get('/tools/map/{wantedMap}/{action}/{key}', [\App\Http\Controllers\Tools\MapController::class, 'mode'])->name('mapToolMode');
Route::post('/tools/map/{wantedMap}/{action}/{key}', [\App\Http\Controllers\Tools\MapController::class, 'modePost'])->name('mapToolMode.post');
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

//Data Collection
Route::get('/tools/datacollectionHQ/index', [\App\Http\Controllers\Tools\CollectDataController::class, 'index'])->name('collectData');
Route::get('/tools/datacollectionHQ/stats', [\App\Http\Controllers\Tools\CollectDataController::class, 'stats'])->name('collectDataStats');
Route::post('/tools/datacollectionHQ/post/{pServer}', [\App\Http\Controllers\Tools\CollectDataController::class, 'post'])->name('collectData.post');
