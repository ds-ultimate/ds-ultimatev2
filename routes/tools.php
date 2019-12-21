<?php

/*
|--------------------------------------------------------------------------
| Tool Routes
|--------------------------------------------------------------------------
|
|
*/

//Distance calculator
Route::get('/{server}/{world}/tools/distanceCalc', 'Tools\DistanceCalcController@index')->name('distanceCalc');


//Attack planner
Route::get('/{server}/{world}/tools/attackPlanner', 'Tools\AttackPlannerController@index')->name('attackPlannerNew');

Route::post('/tools/attackPlanner/{attackList}/importWB/{key}', 'Tools\AttackPlannerController@importWB')->name('attackPlannerImportWB');
Route::post('/tools/attackPlanner/{attackList}/title/{key}/{title}', 'Tools\AttackPlannerController@title')->name('attackPlannerTitle');
Route::get('/tools/attackPlanner/{attackList}/{mode}/{key}', 'Tools\AttackPlannerController@mode')->name('attackPlannerMode');
Route::resource('/tools/attackPlanner/attackListItem','Tools\AttackPlannerItemController', [
    'only' => ['store', 'destroy'],
]);
Route::get('/tools/attackPlanner/attackListItem/data/{attackList}/{key}','Tools\AttackPlannerItemController@data')->name('attackListItem.data');

Route::post('/tools/follow', 'FollowController@createFollowTool')->name('follow');


//Map Tool
Route::get('/{server}/{world}/tools/map/create', 'Tools\MapController@new')->name('mapNew');
Route::post('/tools/map/{wantedMap}/title/{key}/{title}', 'Tools\MapController@title')->name('mapTitle');
Route::get('/tools/map/{wantedMap}/{action}/{key}', 'Tools\MapController@mode')->name('mapToolMode');
Route::post('/tools/map/{wantedMap}/{action}/{key}', 'Tools\MapController@modePost')->name('mapToolMode.post');

Route::get('/{server}/{world}/maptop10', 'Tools\MapController@mapTop10')->name('top10');
Route::get('/{server}/{world}/maptop10p', 'Tools\MapController@mapTop10P')->name('top10p');


//Point calculator
Route::get('/{server}/{world}/tools/pointCalc', 'Tools\PointCalcController@index')->name('pointCalc');


//Data Collection
Route::get('/tools/datacollectionHQ/index', 'Tools\collectDataController@index')->name('collectData');
Route::get('/tools/datacollectionHQ/stats', 'Tools\collectDataController@stats')->name('collectDataStats');
Route::post('/tools/datacollectionHQ/post/{pServer}', 'Tools\collectDataController@post')->name('collectData.post');
