<?php

/*
|--------------------------------------------------------------------------
| Tool Routes
|--------------------------------------------------------------------------
|
|
*/

Route::post('/tools/follow', 'FollowController@createFollowTool')->name('follow');

//Distance calculator
Route::get('/{server}/{world}/tools/distanceCalc', 'Tools\DistanceCalcController@index')->name('distanceCalc');


//Attack planner
Route::get('/{server}/{world}/tools/attackPlanner', 'Tools\AttackPlannerController@index')->name('attackPlannerNew');

Route::resource('/tools/attackPlanner/attackListItem','Tools\AttackPlannerItemController', [
    'only' => ['store', 'destroy', 'update'],
]);
Route::post('/tools/attackPlanner/attackListItem/multiedit', 'Tools\AttackPlannerItemController@multiedit')->name('attackListItemMultiedit');
Route::get('/tools/attackPlanner/attackListItem/data/{attackList}/{key}','Tools\AttackPlannerItemController@data')->name('attackListItem.data');

Route::post('/tools/attackPlanner/{attackList}/importWB/{key}', 'Tools\AttackPlannerController@importWB')->name('attackPlannerImportWB');
Route::post('/tools/attackPlanner/{attackList}/title/{key}/{title}', 'Tools\AttackPlannerController@title')->name('attackPlannerTitle');
Route::get('/tools/attackPlanner/{attackList}/{mode}/{key}', 'Tools\AttackPlannerController@mode')->name('attackPlannerMode');
Route::delete('/tools/attackPlanner/{attackList}/{key}', 'Tools\AttackPlannerController@destroy')->name('attackPlannerDestroy');


//Map Tool
Route::get('/{server}/{world}/tools/map/create', 'Tools\MapController@new')->name('mapNew');
Route::post('/tools/map/{wantedMap}/title/{key}/{title}', 'Tools\MapController@title')->name('mapTitle');
Route::get('/tools/map/{wantedMap}/{action}/{key}', 'Tools\MapController@mode')->name('mapToolMode');
Route::post('/tools/map/{wantedMap}/{action}/{key}', 'Tools\MapController@modePost')->name('mapToolMode.post');
Route::delete('/tools/map/{wantedMap}/{key}', 'Tools\MapController@destroy')->name('mapDestroy');

Route::get('/{server}/{world}/maptop10', 'Tools\MapController@mapTop10')->name('top10');
Route::get('/{server}/{world}/maptop10p', 'Tools\MapController@mapTop10P')->name('top10p');


//Point calculator
Route::get('/{server}/{world}/tools/pointCalc', 'Tools\PointCalcController@index')->name('pointCalc');

//TableGenerator
Route::get('/{server}/{world}/tools/tableGenerator', 'Tools\TableGeneratorController@index')->name('tableGenerator');
Route::post('/tools/tableGenerator', 'Tools\TableGeneratorController@data')->name('tableGeneratorData');
//Data Collection
Route::get('/tools/datacollectionHQ/index', 'Tools\CollectDataController@index')->name('collectData');
Route::get('/tools/datacollectionHQ/stats', 'Tools\CollectDataController@stats')->name('collectDataStats');
Route::post('/tools/datacollectionHQ/post/{pServer}', 'Tools\CollectDataController@post')->name('collectData.post');
