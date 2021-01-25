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

Route::get('/', [\App\Http\Controllers\ContentController::class, 'index'])->name('index');

Auth::routes(['verify' => true]);

//Route::get('/test', function () {
//});

Route::get('/userskripte/verlaufe_und_karten.js', function() {
    return Redirect::to('/userskripte/verlaufe_und_karten.user.js', 301);
});

Route::post('/api/time', function (){
    return response()->json([
        'time' => \Carbon\Carbon::now()->timestamp,
        'millis' => \Carbon\Carbon::now()->milli,
    ]);
})->name('api.time');


Route::get('/setlocale/{locale}',function($lang){
    $validLocale = in_array($lang, ['de', 'en']);
    if ($validLocale) {
        \Session::put('locale',$lang);
    }
    return redirect()->back();
})->name('locale');


Route::group(['prefix' => 'admin', 'as' => 'admin.', 'Admin', 'middleware' => ['dashboard']], function () {
    Route::get('/', [\App\Http\Controllers\Admin\HomeController::class, 'index'])->name('home');

    Route::delete('roles/destroy', [\App\Http\Controllers\Admin\RolesController::class, 'massDestroy'])->name('roles.massDestroy');
    Route::resource('roles', \App\Http\Controllers\Admin\RolesController::class);

    Route::delete('users/destroy', [\App\Http\Controllers\Admin\UsersController::class,'massDestroy'])->name('users.massDestroy');
    Route::resource('users', \App\Http\Controllers\Admin\UsersController::class);

    Route::delete('server/destroy', [\App\Http\Controllers\Admin\ServerController::class, 'massDestroy'])->name('server.massDestroy');
    Route::resource('server', \App\Http\Controllers\Admin\ServerController::class);

    Route::delete('worlds/destroy', [\App\Http\Controllers\Admin\WorldsController::class, 'massDestroy'])->name('worlds.massDestroy');
    Route::resource('worlds', \App\Http\Controllers\Admin\WorldsController::class);

    Route::delete('news/destroy', [\App\Http\Controllers\Admin\NewsController::class, 'massDestroy'])->name('news.massDestroy');
    Route::resource('news', \App\Http\Controllers\Admin\NewsController::class);

    Route::delete('changelogs/destroy', [\App\Http\Controllers\Admin\ChangelogsController::class, 'massDestroy'])->name('changelogs.massDestroy');
    Route::resource('changelogs', \App\Http\Controllers\Admin\ChangelogsController::class);

    Route::delete('bugreports/destroy', [\App\Http\Controllers\Admin\BugreportsCommentController::class, 'massDestroy'])->name('bugreports.massDestroy');
    Route::get('bugreports/priority/{priority}', [\App\Http\Controllers\Admin\BugreportsController::class, 'indexPriority'])->name('bugreports.priority');
    Route::get('bugreports/status/{status}', [\App\Http\Controllers\Admin\BugreportsController::class, 'indexStatus'])->name('bugreports.status');
    Route::get('bugreports/new', [\App\Http\Controllers\Admin\BugreportsController::class, 'indexNew'])->name('bugreports.new');
    Route::resource('bugreports', \App\Http\Controllers\Admin\BugreportsController::class);

    Route::resource('bugreportsComments', \App\Http\Controllers\Admin\BugreportsCommentController::class);

    Route::get('/appLog', [\App\Http\Controllers\Admin\AppLogController::class, 'index'])->name('appLog');

    Route::group(['prefix' => 'api', 'as' => 'api.'], function () {
        Route::get('/news', [\App\Http\Controllers\Admin\APIController::class, 'news'])->name('news');
        Route::post('/newsReorder', [\App\Http\Controllers\Admin\NewsController::class, 'reorder'])->name('news.reorder');
        Route::get('/changelog', [\App\Http\Controllers\Admin\APIController::class, 'changelog'])->name('changelog');
        Route::get('/roles', [\App\Http\Controllers\Admin\APIController::class, 'roles'])->name('roles');
        Route::get('/users', [\App\Http\Controllers\Admin\APIController::class, 'users'])->name('users');
        Route::get('/servers', [\App\Http\Controllers\Admin\APIController::class, 'servers'])->name('servers');
        Route::get('/worlds', [\App\Http\Controllers\Admin\APIController::class, 'worlds'])->name('worlds');
        Route::get('/bugreports', [\App\Http\Controllers\Admin\APIController::class, 'bugreports'])->name('bugreports');
    });
});

Route::group(['prefix' => 'form', 'as' => 'form.', 'middleware' => ['web']], function () {
    Route::get('/bugreport', [\App\Http\Controllers\FormController::class, 'bugreport'])->name('bugreport');
    Route::post('/bugreport/store', [\App\Http\Controllers\FormController::class, 'bugreportStore'])->name('bugreport.store');
});

Route::group(['prefix' => 'user', 'as' => 'user.', 'middleware' => ['verified']], function () {
    Route::get('overview/{page}', [\App\Http\Controllers\User\HomeController::class, 'overview'])->name('overview');
    Route::get('settings/{page}', [\App\Http\Controllers\User\HomeController::class, 'settings'])->name('settings');
    Route::post('uploadeImage', [\App\Http\Controllers\User\SettingsController::class, 'imgUploade'])->name('uploadeImage');
    Route::post('destroyImage', [\App\Http\Controllers\User\SettingsController::class, 'imgDestroy'])->name('destroyImage');
    Route::post('addConnection', [\App\Http\Controllers\User\SettingsController::class, 'addConnection'])->name('addConnection');
    Route::post('saveSettingsAccount', [\App\Http\Controllers\User\SettingsController::class, 'saveSettingsAccount'])->name('saveSettingsAccount');
    Route::post('saveMapSettings', [\App\Http\Controllers\User\SettingsController::class, 'saveMapSettings'])->name('saveMapSettings');
    Route::post('destroyConnection', [\App\Http\Controllers\User\SettingsController::class, 'destroyConnection'])->name('destroyConnection');
    Route::get('DsConnection', [\App\Http\Controllers\User\SettingsController::class, 'getDsConnection'])->name('DsConnection');
    Route::post('DsConnection', [\App\Http\Controllers\User\SettingsController::class, 'checkConnection'])->name('checkDsConnection');
    Route::get('socialite/destroy/{driver}', [\App\Http\Controllers\User\LoginController::class, 'destroyDriver'])->name('socialiteDestroy');

    Route::post('saveConquerHighlighting/{type}', [\App\Http\Controllers\User\SettingsController::class, 'saveConquerHighlighting'])->name('saveConquerHighlighting');
});

Route::get('redirect/{driver}', [\App\Http\Controllers\User\LoginController::class, 'redirectToProvider'])->name('loginRedirect');
Route::get('redirect/{driver}/callback', [\App\Http\Controllers\User\LoginController::class, 'handleProviderCallback']);

Route::get('/sitemap.xml', [\App\Http\Controllers\ContentController::class, 'sitemap']);
Route::get('/impressum', function () {
    return view("content.legalPage");
})->name('legalPage');

Route::get('/changelog', [\App\Http\Controllers\ContentController::class, 'changelog'])->name('changelog');

Route::view('/team', 'content.team')->name('team');

Route::post('/search/{server}', [\App\Http\Controllers\SearchController::class, 'searchForm'])->name('searchForm');
Route::get('/search/{server}/{type}/{search}', [\App\Http\Controllers\SearchController::class, 'search'])->name('search');

Route::get('/{server}', [\App\Http\Controllers\ContentController::class, 'server'])->name('server');

Route::get('/{server}/{world}', [\App\Http\Controllers\ContentController::class, 'world'])->name('world');

Route::get('/{server}/{world}/allys', [\App\Http\Controllers\ContentController::class, 'allys'])->name('worldAlly');
Route::get('/{server}/{world}/allys/ranks', [\App\Http\Controllers\AllyController::class, 'rank'])->name('rankAlly');
Route::get('/{server}/{world}/players', [\App\Http\Controllers\ContentController::class, 'players'])->name('worldPlayer');
Route::get('/{server}/{world}/players/ranks', [\App\Http\Controllers\PlayerController::class, 'rank'])->name('rankPlayer');
Route::get('/{server}/{world}/player/{player}', [\App\Http\Controllers\PlayerController::class, 'player'])->name('player');
Route::get('/{server}/{world}/ally/{ally}', [\App\Http\Controllers\AllyController::class, 'ally'])->name('ally');
Route::get('/{server}/{world}/ally/{ally}/bashRanking', [\App\Http\Controllers\AllyController::class, 'allyBashRanking'])->name('allyBashRanking');
Route::get('/{server}/{world}/village/{village}', [\App\Http\Controllers\VillageController::class, 'village'])->name('village');

Route::get('/{server}/{world}/ally/allyChanges/{type}/{ally}', [\App\Http\Controllers\AllyController::class, 'allyChanges'])->name('allyAllyChanges');
Route::get('/{server}/{world}/player/allyChanges/{type}/{player}', [\App\Http\Controllers\PlayerController::class, 'allyChanges'])->name('playerAllyChanges');

Route::get('/{server}/{world}/conquer/{type}', [\App\Http\Controllers\ContentController::class, 'conquer'])->name('worldConquer');
Route::get('/{server}/{world}/ally/conquer/{type}/{ally}', [\App\Http\Controllers\AllyController::class, 'conquer'])->name('allyConquer');
Route::get('/{server}/{world}/player/conquer/{type}/{player}', [\App\Http\Controllers\PlayerController::class, 'conquer'])->name('playerConquer');
Route::get('/{server}/{world}/village/conquer/{type}/{village}', [\App\Http\Controllers\VillageController::class, 'conquer'])->name('villageConquer');
Route::get('/{server}/{world}/conquerDaily', [\App\Http\Controllers\ContentController::class, 'conquereDaily'])->name('conquerDaily');


Route::get('api/{server}/{world}/signature/{type}/{player}', [\App\Http\Controllers\API\SignatureController::class, 'signature'])->name('api.signature');
