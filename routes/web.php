<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'Controller@index')->name('index');

Auth::routes(['verify' => true]);

Route::get('/home', 'HomeController@index')->name('home')->middleware('verified');

Route::get('/setlocale/{locale}',function($lang){
    $validLocale = in_array($lang, ['de', 'en']);
    if ($validLocale) {
        \Session::put('locale',$lang);
    }
    return redirect()->back();
})->name('locale');

Route::get('/php', function () {
    phpinfo();
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');

    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');

    Route::resource('permissions', 'PermissionsController');

    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');

    Route::resource('roles', 'RolesController');

    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');

    Route::resource('users', 'UsersController');

    Route::delete('server/destroy', 'ServerController@massDestroy')->name('server.massDestroy');

    Route::resource('server', 'ServerController');
});


Route::get('/test', function (){
    echo env("REDIS_PASSWORD");
})->name('test');
Route::get('/server', 'DBController@getWorld');
Route::post('/search/{server}', 'SearchController@searchForm')->name('searchForm');
Route::get('/search/{server}/{type}/{search}', 'Controller@search')->name('search');

Route::get('/{server}', 'Controller@server')->name('server');

Route::get('/{server}/{world}', 'Controller@world')->name('world');

Route::get('/{server}/{world}/allys', 'Controller@allys')->name('worldAlly');
Route::get('/{server}/{world}/players', 'Controller@players')->name('worldPlayer');
Route::get('/{server}/{world}/playersTest', 'Controller@playersTest')->name('worldPlayerTest');
Route::get('/{server}/{world}/player/{player}', 'PlayerController@player')->name('player');
Route::get('/{server}/{world}/ally/{ally}', 'AllyController@ally')->name('ally');
Route::get('/{server}/{world}/village/{village}', 'VillageController@village')->name('village');

Route::get('/{server}/{world}/ally/allyChanges/{type}/{ally}', 'AllyController@allyChanges')->name('allyAllyChanges');
Route::get('/{server}/{world}/player/allyChanges/{type}/{player}', 'PlayerController@allyChanges')->name('playerAllyChanges');

Route::get('/{server}/{world}/ally/conquer/{type}/{ally}', 'AllyController@conquer')->name('allyConquer');
Route::get('/{server}/{world}/player/conquer/{type}/{player}', 'PlayerController@conquer')->name('playerConquer');
