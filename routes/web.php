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

use App\Ally;
use App\Player;
use App\Util\BasicFunctions;
use Illuminate\Support\Carbon;

Route::get('/', function () {
//    $flags = explode('|', env('DS_SERVER'));
//    return view('content.index', compact('flags'));
    return view('welcome');
});

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

Route::get('/test', function (){
    $server = 'de';
    $world = '164';

    $playerModel = new Player();
    $replaceArray = array(
        '{server}' => $server,
        '{world}' => $world
    );

    $playerModel->setTable(str_replace(array_keys($replaceArray), array_values($replaceArray), env('DB_DATABASE_WORLD', 'c1welt_{server}{world}').'.player_latest'));

    $datas = $playerModel->newQuery();

    var_dump($datas);
})->name('test');
Route::get('/server', 'DBController@getWorld');
Route::get('/search/{search}', 'Controller@search');

Route::get('/{server}', 'Controller@server')->name('server');

Route::get('/{server}/{world}', 'Controller@world')->name('world');

Route::get('/{server}/{world}/allys', 'Controller@allys')->name('worldAlly');
Route::get('/{server}/{world}/players', 'Controller@players')->name('worldPlayer');
Route::get('/{server}/{world}/playersTest', 'Controller@playersTest')->name('worldPlayerTest');
Route::get('/{server}/{world}/player/{player}', 'PlayerController@player')->name('player');
Route::get('/{server}/{world}/ally/{ally}', 'AllyController@ally')->name('ally');
