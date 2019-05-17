<?php

namespace App\Http\Controllers;

use App\Ally;
use App\Player;
use App\Server;
use App\Util\BasicFunctions;
use App\Village;
use App\World;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DBController extends Controller
{

    public function serverTable(){
        DB::statement('CREATE DATABASE '.env('DB_DATABASE_MAIN'));
        Schema::create(env('DB_DATABASE_MAIN').'.server', function (Blueprint $table){
            $table->integer('id')->autoIncrement();
            $table->char('code');
            $table->text('url');
        });
    }

    public function logTable(){
        Schema::create(env('DB_DATABASE_MAIN').'.log', function (Blueprint $table){
            $table->bigIncrements('id')->autoIncrement();
            $table->text('type');
            $table->text('msg');
            $table->timestamps();
        });
    }

    public function worldTable(){
        Schema::create(env('DB_DATABASE_MAIN').'.world', function (Blueprint $table){
            $table->integer('id')->autoIncrement();
            $table->text('name');
            $table->integer('ally_count')->nullable();
            $table->integer('player_count')->nullable();
            $table->integer('village_count')->nullable();
            $table->text('url');
            $table->timestamps();
        });
    }

    public function getWorld(){
        $serverArray = Server::getServer();

        foreach ($serverArray as $serverUrl){
            $worldFile = file_get_contents($serverUrl->url.'/backend/get_servers.php');
            $worldTable = new World();
            $worldTable->setTable(env('DB_DATABASE_MAIN').'.world');
            $worldArray = unserialize($worldFile);
            foreach ($worldArray as $world => $link){
                if ($worldTable->where('name', $world)->count() < 1){
                    $worldNew = new World();
                    $worldNew->setTable(env('DB_DATABASE_MAIN').'.world');
                    $worldNew->name = $world;
                    $worldNew->url = $link;
                    if($worldNew->save() === true){
                        BasicFunctions::createLog('insert[World]', "Welt $world wurde erfolgreich der Tabelle '$world' hinzugefügt.");
                        $name = str_replace('{server}{world}', '',env('DB_DATABASE_WORLD')).$world;
                        if (DB::statement('CREATE DATABASE '.$name) === true){
                            BasicFunctions::createLog("createBD[$world]", "DB '$name' wurde erfolgreich erstellt.");
                        }else{
                            BasicFunctions::createLog("ERROR_createBD[$world]", "DB '$name' konnte nicht erstellt werden.");
                        }
                    }else{
                        BasicFunctions::createLog('ERROR_insert[World]', "Welt $world konnte nicht der Tabelle 'world' hinzugefügt werden.");
                    }
                }
            }
        }
    }

    public function latestPlayer($worldName){
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        ini_set('max_execution_time', 1800);
        ini_set('max_input_time', 1800);
        ini_set('memory_limit', '300M');
        $dbName = str_replace('{server}{world}', '',env('DB_DATABASE_WORLD')).$worldName;
        if (BasicFunctions::existTable($dbName, 'player_latest') === false){
            Schema::create($dbName.'.player_latest', function (Blueprint $table) {
                $table->integer('playerID');
                $table->string('name');
                $table->integer('ally_id');
                $table->integer('village_count');
                $table->integer('points');
                $table->integer('rank');
                $table->bigInteger('offBash')->nullable();
                $table->integer('offBashRank')->nullable();
                $table->bigInteger('defBash')->nullable();
                $table->integer('defBashRank')->nullable();
                $table->bigInteger('gesBash')->nullable();
                $table->integer('gesBashRank')->nullable();
                $table->timestamps();
            });
        }
        $time = time();
        $lines = gzfile("https://$worldName.die-staemme.de/map/player.txt.gz");
        if(!is_array($lines)) die("Datei player konnte nicht ge&ouml;ffnet werden");
        $players = collect();
        foreach ($lines as $line){
            list($id, $name, $ally, $villages, $points, $rank) = explode(',', $line);
            $player = collect();
            $player->put('id', (int)$id);
            $player->put('name', $name);
            $player->put('ally', (int)$ally);
            $player->put('villages', (int)$villages);
            $player->put('points', (int)$points);
            $player->put('rank', (int)$rank);
            $player->put('off', (int)null);
            $player->put('offRank', (int)null);
            $player->put('def', (int)null);
            $player->put('defRank', (int)null);
            $player->put('tot', (int)null);
            $player->put('totRank', (int)null);
            $players->put($player->get('id'),$player);
        }

        $offs = gzfile("https://$worldName.die-staemme.de/map/kill_att.txt.gz");
        if(!is_array($offs)) die("Datei kill_off konnte nicht ge&ouml;ffnet werden");
        foreach ($offs as $off){
            list($rank, $id, $kills) = explode(',', $off);
            $player = $players->firstWhere('id', '=', $id);
            if($player == null) {
                $player->put('offRank', (int)$rank);
                $player->put('off', (int)$kills);
                $players->put($player->get('id'), $player);
            }
        }

        $defs = gzfile("https://$worldName.die-staemme.de/map/kill_def.txt.gz");
        if(!is_array($defs)) die("Datei kill_def konnte nicht ge&ouml;ffnet werden");
        foreach ($defs as $def){
            list($rank, $id, $kills) = explode(',', $def);
            $player = $players->firstWhere('id', '=', $id);
            if($player == null) {
                $player->put('defRank', (int)$rank);
                $player->put('def', (int)$kills);
                $players->put($player->get('id'), $player);
            }
        }

        $tots = gzfile("https://$worldName.die-staemme.de/map/kill_all.txt.gz");
        if(!is_array($tots)) die("Datei kill_all konnte nicht ge&ouml;ffnet werden");
        foreach ($tots as $tot){
            list($rank, $id, $kills) = explode(',', $tot);
            $player = $players->firstWhere('id', '=', $id);
            if($player == null) {
                $player->put('totRank', (int)$rank);
                $player->put('tot', (int)$kills);
                $players->put($player->get('id'), $player);
            }
        }

        $insert = new Player();
        $insert->setTable($dbName.'.player_latest');
        $array = array();
        foreach ($players as $player) {
            $data = [
                'playerID' => $player->get('id'),
                'name' => $player->get('name'),
                'ally_id' => $player->get('ally'),
                'village_count' => $player->get('villages'),
                'points' => $player->get('points'),
                'rank' => $player->get('rank'),
                'offBash' => $player->get('off'),
                'offBashRank' => $player->get('offRank'),
                'defBash' => $player->get('def'),
                'defBashRank' => $player->get('defRank'),
                'gesBash' => $player->get('tot'),
                'gesBashRank' => $player->get('totRank'),
                'created_at' => Carbon::createFromTimestamp(time()),
                'updated_at' => Carbon::createFromTimestamp(time()),
            ];
            $array []= $data;
        }
        foreach (array_chunk($array,3000) as $t){
            $insert->insert($t);
        }

        $world = new World();
        $world->setTable(env('DB_DATABASE_MAIN').'.world');
        $worldUpdate = $world->where('name', $worldName)->first();
        $worldUpdate->player_count = count($array);
        $worldUpdate->save();

        echo time()-$time;
    }

    public function latestVillages($worldName){
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        ini_set('max_execution_time', 1800);
        ini_set('max_input_time', 1800);
        ini_set('memory_limit', '500M');
        $dbName = str_replace('{server}{world}', '',env('DB_DATABASE_WORLD')).$worldName;
        if (BasicFunctions::existTable($dbName, 'village_latest') === false){
            Schema::create($dbName.'.village_latest', function (Blueprint $table) {
                $table->integer('villageID');
                $table->string('name');
                $table->integer('x');
                $table->integer('y');
                $table->integer('points');
                $table->integer('owner');
                $table->integer('bonus_id');
                $table->timestamps();
            });
        }
        $time = time();
        $lines = gzfile("https://$worldName.die-staemme.de/map/village.txt.gz");
        if(!is_array($lines)) die("Datei village konnte nicht ge&ouml;ffnet werden");
        $villages = collect();
        foreach ($lines as $line){
            list($id, $name, $x, $y, $points, $owner, $bonus_id) = explode(',', $line);
            $village = collect();
            $village->put('id', (int)$id);
            $village->put('name', $name);
            $village->put('x', (int)$x);
            $village->put('y', (int)$y);
            $village->put('points', (int)$points);
            $village->put('owner', (int)$owner);
            $village->put('bonus_id', (int)$bonus_id);
            $villages->put($village->get('id'),$village);
        }

        $insert = new Village();
        $insert->setTable($dbName.'.village_latest');
        $array = array();
        foreach ($villages as $village) {
            $data = [
                'villageID' => $village->get('id'),
                'name' => $village->get('name'),
                'x' => $village->get('x'),
                'y' => $village->get('y'),
                'points' => $village->get('points'),
                'owner' => $village->get('owner'),
                'bonus_id' => $village->get('bonus_id'),
                'created_at' => Carbon::createFromTimestamp(time()),
                'updated_at' => Carbon::createFromTimestamp(time()),
            ];
            $array []= $data;
        }
        foreach (array_chunk($array,3000) as $t){
            $insert->insert($t);
        }

        $world = new World();
        $world->setTable(env('DB_DATABASE_MAIN').'.world');
        $worldUpdate = $world->where('name', $worldName)->first();
        $worldUpdate->village_count = count($array);
        $worldUpdate->save();

        echo time()-$time;
    }

    public function latestAlly($worldName){
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        ini_set('max_execution_time', 1800);
        ini_set('max_input_time', 1800);
        ini_set('memory_limit', '200M');
        $dbName = str_replace('{server}{world}', '',env('DB_DATABASE_WORLD')).$worldName;
        if (BasicFunctions::existTable($dbName, 'ally_latest') === false){
            Schema::create($dbName.'.ally_latest', function (Blueprint $table) {
                $table->integer('allyID');
                $table->string('name');
                $table->string('tag');
                $table->integer('member_count');
                $table->integer('points');
                $table->integer('village_count');
                $table->integer('rank');
                $table->bigInteger('offBash')->nullable();
                $table->integer('offBashRank')->nullable();
                $table->bigInteger('defBash')->nullable();
                $table->integer('defBashRank')->nullable();
                $table->bigInteger('gesBash')->nullable();
                $table->integer('gesBashRank')->nullable();
                $table->timestamps();
            });
        }
        $time = time();
        $lines = gzfile("https://$worldName.die-staemme.de/map/ally.txt.gz");
        if(!is_array($lines)) die("Datei ally konnte nicht ge&ouml;ffnet werden");
        $allys = collect();
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
            $ally->put('off', (int)null);
            $ally->put('offRank', (int)null);
            $ally->put('def', (int)null);
            $ally->put('defRank', (int)null);
            $ally->put('tot', (int)null);
            $ally->put('totRank', (int)null);
            $allys->put($ally->get('id'),$ally);
        }

        $offs = gzfile("https://$worldName.die-staemme.de/map/kill_att_tribe.txt.gz");
        if(!is_array($offs)) die("Datei kill_off konnte nicht ge&ouml;ffnet werden");
        foreach ($offs as $off){
            list($rank, $id, $kills) = explode(',', $off);
            $ally = $allys->firstWhere('id', '=', $id);
            if($ally == null) {
                $ally->put('offRank', (int)$rank);
                $ally->put('off', (int)$kills);
                $allys->put($ally->get('id'), $ally);
            }
        }

        $defs = gzfile("https://$worldName.die-staemme.de/map/kill_def_tribe.txt.gz");
        if(!is_array($defs)) die("Datei kill_def konnte nicht ge&ouml;ffnet werden");
        foreach ($defs as $def){
            list($rank, $id, $kills) = explode(',', $def);
            $ally = $allys->firstWhere('id', '=', $id);
            if($ally == null) {
                $ally->put('defRank', (int)$rank);
                $ally->put('def', (int)$kills);
                $allys->put($ally->get('id'), $ally);
            }
        }

        $tots = gzfile("https://$worldName.die-staemme.de/map/kill_all_tribe.txt.gz");
        if(!is_array($tots)) die("Datei kill_all konnte nicht ge&ouml;ffnet werden");
        foreach ($tots as $tot){
            list($rank, $id, $kills) = explode(',', $tot);
            $ally = $allys->firstWhere('id', '=', $id);
            if($ally == null) {
                $ally->put('totRank', (int)$rank);
                $ally->put('tot', (int)$kills);
                $allys->put($ally->get('id'), $ally);
            }
        }

        $insert = new Ally();
        $insert->setTable($dbName.'.ally_latest');
        $array = array();
        foreach ($allys as $ally) {
            $data = [
                'allyID' => $ally->get('id'),
                'name' => $ally->get('name'),
                'tag' => $ally->get('tag'),
                'member_count' => $ally->get('member_count'),
                'points' => $ally->get('points'),
                'village_count' => $ally->get('village_count'),
                'rank' => $ally->get('rank'),
                'offBash' => $ally->get('off'),
                'offBashRank' => $ally->get('offRank'),
                'defBash' => $ally->get('def'),
                'defBashRank' => $ally->get('defRank'),
                'gesBash' => $ally->get('tot'),
                'gesBashRank' => $ally->get('totRank'),
                'created_at' => Carbon::createFromTimestamp(time()),
                'updated_at' => Carbon::createFromTimestamp(time()),
            ];
            $array []= $data;
        }
        foreach (array_chunk($array,3000) as $t){
            $insert->insert($t);
        }

        $world = new World();
        $world->setTable(env('DB_DATABASE_MAIN').'.world');
        $worldUpdate = $world->where('name', $worldName)->first();
        $worldUpdate->ally_count = count($array);
        $worldUpdate->save();

        echo time()-$time;
    }

    public function hash($Array, $type){
        switch($type) {
            case 'player':
                $hash = env('HASH_PLAYER');
                break;
            case 'ally':
                $hash = env('HASH_ALLY');
                break;
            case 'village':
                $hash = env('HASH_VILLAGE');
                break;
        }
    }
}
