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

Route::get('/test', function () {
});

Route::get('/userskripte/verlaufe_und_karten.js', function() {
    return Redirect::to('/userskripte/verlaufe_und_karten.user.js', 301);
});


Route::get('/setlocale/{locale}',function($lang){
    $validLocale = in_array($lang, ['de', 'en']);
    if ($validLocale) {
        \Session::put('locale',$lang);
    }
    return redirect()->back();
})->name('locale');


Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['dashboard']], function () {
    Route::get('/', 'HomeController@index')->name('home');

    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    Route::delete('server/destroy', 'ServerController@massDestroy')->name('server.massDestroy');
    Route::resource('server', 'ServerController');

    Route::delete('worlds/destroy', 'WorldsController@massDestroy')->name('worlds.massDestroy');
    Route::resource('worlds', 'WorldsController');

    Route::delete('news/destroy', 'NewsController@massDestroy')->name('news.massDestroy');
    Route::resource('news', 'NewsController');

    Route::delete('changelogs/destroy', 'ChangelogsController@massDestroy')->name('changelogs.massDestroy');
    Route::resource('changelogs', 'ChangelogsController');

    Route::delete('bugreports/destroy', 'BugreportsController@massDestroy')->name('bugreports.massDestroy');
    Route::get('bugreports/priority/{priority}', 'BugreportsController@indexPriority')->name('bugreports.priority');
    Route::get('bugreports/status/{status}', 'BugreportsController@indexStatus')->name('bugreports.status');
    Route::get('bugreports/new', 'BugreportsController@indexNew')->name('bugreports.new');
    Route::resource('bugreports', 'BugreportsController');

    Route::resource('bugreportsComments', 'BugreportsCommentController');

    Route::get('/appLog', 'AppLogController@index')->name('appLog');
});

Route::group(['prefix' => 'form', 'as' => 'form.', 'middleware' => ['web']], function () {
    Route::get('/bugreport', 'FormController@bugreport')->name('bugreport');
    Route::post('/bugreport/store', 'FormController@bugreportStore')->name('bugreport.store');
});

Route::group(['prefix' => 'user', 'as' => 'user.', 'namespace' => 'User', 'middleware' => ['verified']], function () {
    Route::get('overview/{page}', 'HomeController@overview')->name('overview');
    Route::get('settings/{page}', 'HomeController@settings')->name('settings');
    Route::post('uploadeImage', 'SettingsController@imgUploade')->name('uploadeImage');
    Route::post('destroyImage', 'SettingsController@imgDestroy')->name('destroyImage');
    Route::post('addConnection', 'SettingsController@addConnection')->name('addConnection');
    Route::post('saveSettingsAccount', 'SettingsController@saveSettingsAccount')->name('saveSettingsAccount');
    Route::post('saveMapSettings', 'SettingsController@saveMapSettings')->name('saveMapSettings');
    Route::post('destroyConnection', 'SettingsController@destroyConnection')->name('destroyConnection');
    Route::get('DsConnection', '\App\Http\Controllers\APIController@getDsConnection')->name('DsConnection');
    Route::post('DsConnection', 'SettingsController@checkConnection')->name('checkDsConnection');
    Route::get('socialite/destroy/{driver}', 'LoginController@destroyDriver')->name('socialiteDestroy');
});

Route::get('redirect/{driver}', 'User\LoginController@redirectToProvider')->name('loginRedirect');
Route::get('redirect/{driver}/callback', 'User\LoginController@handleProviderCallback');

Route::get('/sitemap.xml', 'Controller@sitemap');
Route::get('/impressum', function () {
    return view("content.legalPage");
})->name('legalPage');

Route::get('/changelog', 'Controller@changelog')->name('changelog');

Route::view('/team', 'content.team')->name('team');

Route::post('/search/{server}', 'SearchController@searchForm')->name('searchForm');
Route::get('/search/{server}/{type}/{search}', 'Controller@search')->name('search');

Route::get('/{server}', 'Controller@server')->name('server');

Route::get('/{server}/{world}', 'Controller@world')->name('world');

Route::get('/{server}/{world}/allys', 'Controller@allys')->name('worldAlly');
Route::get('/{server}/{world}/allys/ranks', 'AllyController@rank')->name('rankAlly');
Route::get('/{server}/{world}/players', 'Controller@players')->name('worldPlayer');
Route::get('/{server}/{world}/players/ranks', 'PlayerController@rank')->name('rankPlayer');
Route::get('/{server}/{world}/player/{player}', 'PlayerController@player')->name('player');
Route::get('/{server}/{world}/ally/{ally}', 'AllyController@ally')->name('ally');
Route::get('/{server}/{world}/village/{village}', 'VillageController@village')->name('village');

Route::get('/{server}/{world}/ally/allyChanges/{type}/{ally}', 'AllyController@allyChanges')->name('allyAllyChanges');
Route::get('/{server}/{world}/player/allyChanges/{type}/{player}', 'PlayerController@allyChanges')->name('playerAllyChanges');

Route::get('/{server}/{world}/ally/conquer/{type}/{ally}', 'AllyController@conquer')->name('allyConquer');
Route::get('/{server}/{world}/player/conquer/{type}/{player}', 'PlayerController@conquer')->name('playerConquer');
Route::get('/{server}/{world}/village/conquer/{type}/{village}', 'VillageController@conquer')->name('villageConquer');
