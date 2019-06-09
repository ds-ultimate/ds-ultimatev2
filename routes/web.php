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
    $worldName = 'de156';
    $dbName = str_replace('{server}{world}', '',env('DB_DATABASE_WORLD')).$worldName;
    if (BasicFunctions::existTable($dbName, 'ally_latest_temp') === false){
        $g =new \App\Http\Controllers\DBController();
        $g->allyTable($dbName, 'latest_temp');
    }

    $lines = gzfile("https://$worldName.die-staemme.de/map/ally.txt.gz");
    if(!is_array($lines)) die("Datei ally konnte nicht ge&ouml;ffnet werden");

    $allys = collect();
    $allyOffs = collect();
    $allyDefs = collect();
    $allyTots = collect();

    foreach ($lines as $line){
        list($id, $name, $tag, $members, $points, $villages, $rank) = explode(',', $line);
        $ally = collect();
        $ally->put('id', (int)$id);
        $ally->put('name', $name);
        $ally->put('tag', $tag);
        $ally->put('member_count', (int)$members);
        $ally->put('points', (int)$points);
        $ally->put('village_count', (int)$villages);
        $ally->put('rank', (int)$rank);
        $allys->put($ally->get('id'),$ally);
    }

    $offs = gzfile("https://$worldName.die-staemme.de/map/kill_att_tribe.txt.gz");
    if(!is_array($offs)) die("Datei kill_off konnte nicht ge&ouml;ffnet werden");
    foreach ($offs as $off){
        list($rank, $id, $kills) = explode(',', $off);
        $allyOff = collect();
        $allyOff->put('offRank', (int)$rank);
        $allyOff->put('off', (int)$kills);
        $allyOffs->put($id, $allyOff);

    }

    $defs = gzfile("https://$worldName.die-staemme.de/map/kill_def_tribe.txt.gz");
    if(!is_array($defs)) die("Datei kill_def konnte nicht ge&ouml;ffnet werden");
    foreach ($defs as $def){
        list($rank, $id, $kills) = explode(',', $def);
        $allyDef = collect();
        $allyDef->put('defRank', (int)$rank);
        $allyDef->put('def', (int)$kills);
        $allyDefs->put($id, $allyDef);
    }

    $tots = gzfile("https://$worldName.die-staemme.de/map/kill_all_tribe.txt.gz");
    if(!is_array($tots)) die("Datei kill_all konnte nicht ge&ouml;ffnet werden");
    foreach ($tots as $tot){
        list($rank, $id, $kills) = explode(',', $tot);
        $allyTot = collect();
        $allyTot->put('totRank', (int)$rank);
        $allyTot->put('tot', (int)$kills);
        $allyTots->put($id, $allyTot);
    }

    $insert = new Ally();
    $insert->setTable($dbName.'.ally_latest_temp');
    $array = array();
    foreach ($allys as $ally) {
        $id = $ally->get('id');
        $data = [
            'allyID' => $ally->get('id'),
            'name' => $ally->get('name'),
            'tag' => $ally->get('tag'),
            'member_count' => $ally->get('member_count'),
            'points' => $ally->get('points'),
            'village_count' => $ally->get('village_count'),
            'rank' => $ally->get('rank'),
            'offBash' => (is_null($allyOffs->get($id)))? null :$allyOffs->get($id)->get('off'),
            'offBashRank' => (is_null($allyOffs->get($id)))? null : $allyOffs->get($id)->get('offRank'),
            'defBash' => (is_null($allyDefs->get($id)))? null : $allyDefs->get($id)->get('def'),
            'defBashRank' => (is_null($allyDefs->get($id)))? null : $allyDefs->get($id)->get('defRank'),
            'gesBash' => (is_null($allyTots->get($id)))? null : $allyTots->get($id)->get('tot'),
            'gesBashRank' => (is_null($allyTots->get($id)))? null : $allyTots->get($id)->get('totRank'),
            'created_at' => Carbon::createFromTimestamp(time()),
            'updated_at' => Carbon::createFromTimestamp(time()),
        ];
        $array []= $data;
    }
    foreach (array_chunk($array,3000) as $t){
        $insert->insert($t);
    }

    DB::statement("DROP TABLE $dbName.ally_latest");
    DB::statement("ALTER TABLE $dbName.ally_latest_temp RENAME TO $dbName.ally_latest");
})->name('test');
Route::get('/server', 'DBController@getWorld');

Route::get('/{server}', 'Controller@server')->name('server');

Route::get('/{server}/{world}', 'Controller@world')->name('world');

Route::get('/{server}/{world}/allys', 'Controller@allys')->name('worldAlly');
Route::get('/{server}/{world}/players', 'Controller@players')->name('worldPlayer');
Route::get('/{server}/{world}/playersTest', 'Controller@playersTest')->name('worldPlayerTest');
Route::get('/{server}/{world}/player/{player}', 'PlayerController@player')->name('player');
Route::get('/{server}/{world}/ally/{ally}', 'AllyController@ally')->name('ally');
