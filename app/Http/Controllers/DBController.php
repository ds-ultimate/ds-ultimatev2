<?php

namespace App\Http\Controllers;

use App\Ally;
use App\Conquer;
use App\Player;
use App\Server;
use App\AllyChanges;
use App\Util\BasicFunctions;
use App\Village;
use App\World;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DBController extends Controller
{
    public static function worldTable(){
        Schema::create('worlds', function (Blueprint $table){
            $table->increments('id');
            $table->integer('server_id');
            $table->text('name');
            $table->integer('ally_count')->nullable();
            $table->integer('player_count')->nullable();
            $table->integer('village_count')->nullable();
            $table->text('url');
            $table->text('config');
            $table->text('units');
            $table->boolean('active')->default(1)->nullable();
            $table->timestamps();
            $table->timestamp('worldCheck_at')->useCurrent();
            $table->timestamp('worldUpdated_at')->useCurrent();
            $table->timestamp('worldCleaned_at')->useCurrent();
            $table->softDeletes();
        });
    }

    public static function playerTable($dbName, $tableName){
        Schema::create($dbName.'.player_'.$tableName, function (Blueprint $table) {
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
            $table->index('playerID');
        });
    }

    public static function playerLatestTable($dbName, $tableName){
        Schema::create($dbName.'.player_'.$tableName, function (Blueprint $table) {
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
            $table->primary('playerID');
        });
    }

    public static function allyTable($dbName, $tableName){
        Schema::create($dbName.'.ally_'.$tableName, function (Blueprint $table) {
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
            $table->index('allyID');
        });
    }

    public static function allyLatestTable($dbName, $tableName){
        Schema::create($dbName.'.ally_'.$tableName, function (Blueprint $table) {
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
            $table->primary('allyID');
        });
    }

    public static function villageTable($dbName, $tableName){
        Schema::create($dbName.'.village_'.$tableName, function (Blueprint $table) {
            $table->integer('villageID');
            $table->string('name');
            $table->integer('x');
            $table->integer('y');
            $table->integer('points');
            $table->integer('owner');
            $table->integer('bonus_id');
            $table->timestamps();
            $table->index('villageID');
        });
    }

    public static function villageLatestTable($dbName, $tableName){
        Schema::create($dbName.'.village_'.$tableName, function (Blueprint $table) {
            $table->integer('villageID');
            $table->string('name');
            $table->integer('x');
            $table->integer('y');
            $table->integer('points');
            $table->integer('owner');
            $table->integer('bonus_id');
            $table->timestamps();
            $table->primary('villageID');
        });
    }

    public static function allyChangeTable($dbName){
        Schema::create($dbName.'.ally_changes', function (Blueprint $table) {
            $table->integer('player_id');
            $table->integer('old_ally_id');
            $table->integer('new_ally_id');
            $table->integer('points');
            $table->timestamps();
        });
    }

    public static function conquerTable($dbName){
        Schema::create($dbName.'.conquer', function (Blueprint $table) {
            $table->integer('village_id');
            $table->bigInteger('timestamp');
            $table->integer('new_owner');
            $table->integer('old_owner');
            $table->timestamps();
        });
    }
    
    public static function updateNeeded() {
        if(!BasicFunctions::existTable(null, 'worlds')) return false;
        $worldModel = new World();
        return $worldModel->where('worldUpdated_at', '<', Carbon::createFromTimestamp(time()
                - (60 * 60) * config('dsUltimate.db_update_every_hours')))
                ->where('active', '=', 1)->count() > 0;
    }
    
    public static function cleanNeeded() {
        if(!BasicFunctions::existTable(null, 'worlds')) return false;
        $worldModel = new World();
        return $worldModel->where('worldCleaned_at', '<', Carbon::createFromTimestamp(time()
                - (60 * 60) * config('dsUltimate.db_clean_every_hours')))
                ->where('active', '=', 1)->count() > 0;
    }

    public static function getWorld(){
        if (BasicFunctions::existTable(null, 'worlds') === false){
            DBController::worldTable();
        }

        $serverArray = Server::getServer();

        foreach ($serverArray as $serverUrl){
            $worldFile = file_get_contents($serverUrl->url.'/backend/get_servers.php');
            $worldTable = new World();
            $worldTable->setTable('worlds');
            $worldArray = unserialize($worldFile);
            foreach ($worldArray as $world => $link){

                $worldName = substr($world, 2);

                if ($worldTable->where('server_id', $serverUrl->id)->where('name', $worldName)->count() >= 1){
                    //world exists already -> update
                    $create = false;
                    $worldNew = null;
                    foreach($worldTable->where('server_id', $serverUrl->id)->where('name', $worldName)->get() as $world) {
                        if($worldNew == null) {
                            $worldNew = $world;
                            $world->worldCheck_at = Carbon::createFromTimestamp(time());
                            $world->update();
                        } else {
                            $world->delete();
                        }
                    }
                } else {
                    //create new entry
                    $create = true;
                    $worldNew = new World();
                    $worldNew->setTable('worlds');
                }
                
                $worldNew->server_id = $serverUrl->id;
                $worldNew->name = $worldName;
                $worldNew->url = $link;
                $txtConf = file_get_contents("$link/interface.php?func=get_config");
                $worldNew->config = $txtConf;
                $txtUnits = file_get_contents("$link/interface.php?func=get_unit_info");
                $worldNew->units = $txtUnits;
                $worldNew->worldCheck_at = Carbon::createFromTimestamp(time());

                if ($worldNew->save() !== true){
                    BasicFunctions::createLog('ERROR_insert[World]', "Welt $world konnte nicht der Tabelle 'worlds' hinzugefügt werden.");
                    continue;
                }
                
                if(!$create) continue;
                
                BasicFunctions::createLog('insert[World]', "Welt $world wurde erfolgreich der Tabelle '$world' hinzugefügt.");
                $name = BasicFunctions::getDatabaseName('', '').$world;
                if (BasicFunctions::existDatabase($name) !== false) {
                    BasicFunctions::createLog("ERROR_createBD[$world]", "DB '$name' existierte bereits.");
                    continue;
                }
                if (DB::statement('CREATE DATABASE ' . $name) !== true) {
                    BasicFunctions::createLog("ERROR_createBD[$world]", "DB '$name' konnte nicht erstellt werden.");
                    continue;
                }
                BasicFunctions::createLog("createBD[$world]", "DB '$name' wurde erfolgreich erstellt.");
            }
        }

        $worldModel = new World();

        foreach ($worldModel->where('worldCheck_at', '<',\Carbon\Carbon::createFromTimestamp(time() - (60 * 30)))->get() as $world ){
            BasicFunctions::createLog("Status[$world->name]", "$world->name ist nicht mehr aktiv");
            $world->active = null;
            $world->update();
        }

    }
    
    public static function latestPlayer($server, $world){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '600M');
        $dbName = BasicFunctions::getDatabaseName($server, $world);
        $worldUpdate = World::getWorld($server, $world);

        Schema::dropIfExists("$dbName.player_latest_temp");
        if (BasicFunctions::existTable($dbName, 'player_latest_temp') === false){
            DBController::playerLatestTable($dbName, 'latest_temp');
        }
        
        if (BasicFunctions::existTable($dbName, 'ally_changes') === false){
            DBController::allyChangeTable($dbName);
        }

        if (BasicFunctions::existTable($dbName, 'player_latest') === false){
            DBController::playerLatestTable($dbName, 'latest');
        }
        
        $lines = gzfile("$worldUpdate->url/map/player.txt.gz");
        if(!is_array($lines)) {
            BasicFunctions::createLog("ERROR_update[$server$world]", "player.txt.gz konnte nicht ge&ouml;ffnet werden");
            return;
        }

        $players = collect();
        $playerOffs = collect();
        $playerDefs = collect();
        $playerTots = collect();

        foreach ($lines as $line){
            list($id, $name, $ally, $villages, $points, $rank) = explode(',', $line);
            $player = collect();
            $player->put('id', (int)$id);
            $player->put('name', $name);
            $player->put('ally', (int)$ally);
            $player->put('villages', (int)$villages);
            $player->put('points', (int)$points);
            $player->put('rank', (int)$rank);
            $players->put($player->get('id'),$player);
        }

        $offs = gzfile("$worldUpdate->url/map/kill_att.txt.gz");
        if(!is_array($offs)) {
            BasicFunctions::createLog("ERROR_update[$server$world]", "kill_att.txt.gz konnte nicht ge&ouml;ffnet werden");
            return;
        }
        foreach ($offs as $off){
            list($rank, $id, $kills) = explode(',', $off);
            $playerOff = collect();
            $playerOff->put('offRank', (int)$rank);
            $playerOff->put('off', (int)$kills);
            $playerOffs->put($id, $playerOff);
        }

        $defs = gzfile("$worldUpdate->url/map/kill_def.txt.gz");
        if(!is_array($defs)) {
            BasicFunctions::createLog("ERROR_update[$server$world]", "kill_def.txt.gz konnte nicht ge&ouml;ffnet werden");
            return;
        }
        foreach ($defs as $def){
            list($rank, $id, $kills) = explode(',', $def);
            $playerDef = collect();
            $playerDef->put('defRank', (int)$rank);
            $playerDef->put('def', (int)$kills);
            $playerDefs->put($id, $playerDef);
        }

        $tots = gzfile("$worldUpdate->url/map/kill_all.txt.gz");
        if(!is_array($tots)) {
            BasicFunctions::createLog("ERROR_update[$server$world]", "kill_all.txt.gz konnte nicht ge&ouml;ffnet werden");
            return;
        }
        foreach ($tots as $tot){
            list($rank, $id, $kills) = explode(',', $tot);
            $playerTot = collect();
            $playerTot->put('totRank', (int)$rank);
            $playerTot->put('tot', (int)$kills);
            $playerTots->put($id, $playerTot);
        }
        
        
        $playerChange = new Player();
        $playerChange->setTable($dbName . '.player_latest');
        $databasePlayer = array();
        foreach ($playerChange->get() as $player) {
            $databasePlayer[$player->playerID] = $player->ally_id;
        }

        $insert = new Player();
        $insert->setTable($dbName.'.player_latest_temp');
        $arrayAllyChange = array();
        foreach ($players as $player) {
            $id = $player->get('id');
            $dataPlayer = [
                'playerID' => $player->get('id'),
                'name' => $player->get('name'),
                'ally_id' => $player->get('ally'),
                'village_count' => $player->get('villages'),
                'points' => $player->get('points'),
                'rank' => $player->get('rank'),
                'offBash' => (is_null($playerOffs->get($id)))? null :$playerOffs->get($id)->get('off'),
                'offBashRank' => (is_null($playerOffs->get($id)))? null : $playerOffs->get($id)->get('offRank'),
                'defBash' => (is_null($playerDefs->get($id)))? null : $playerDefs->get($id)->get('def'),
                'defBashRank' => (is_null($playerDefs->get($id)))? null : $playerDefs->get($id)->get('defRank'),
                'gesBash' => (is_null($playerTots->get($id)))? null : $playerTots->get($id)->get('tot'),
                'gesBashRank' => (is_null($playerTots->get($id)))? null : $playerTots->get($id)->get('totRank'),
                'created_at' => Carbon::createFromTimestamp(time()),
                'updated_at' => Carbon::createFromTimestamp(time()),
            ];
            $arrayPlayer []= $dataPlayer;
            
            if((isset($databasePlayer[$player->get('id')]) && $databasePlayer[$player->get('id')] != $player->get('ally')) ||
                    (!isset($databasePlayer[$player->get('id')]) && $player->get('ally') != 0)) {
                $arrayAllyChange[] = [
                    'player_id' => $player->get('id'),
                    'old_ally_id' => (isset($databasePlayer[$player->get('id')]))?($databasePlayer[$player->get('id')]):(0),
                    'new_ally_id' => $player->get('ally'),
                    'points' => $player->get('points'),
                    'created_at' => Carbon::createFromTimestamp(time()),
                    'updated_at' => Carbon::createFromTimestamp(time()),
                ];
            }
        }

        foreach (array_chunk($arrayPlayer,3000) as $t){
            $insert->insert($t);
        }
        
        $allyChangeModel = new AllyChanges();
        $allyChangeModel->setTable($dbName.'.ally_changes');
        foreach (array_chunk($arrayAllyChange,3000) as $t){
            $allyChangeModel->insert($t);
        }

        Schema::dropIfExists("$dbName.player_latest");
        DB::statement("ALTER TABLE $dbName.player_latest_temp RENAME TO $dbName.player_latest");
        
        $hashPlayer = DBController::hashTable($arrayPlayer, 'p', 'playerID');

        for ($i = 0; $i < config('dsUltimate.hash_player'); $i++){
            if (array_key_exists($i ,$hashPlayer)) {
                if (BasicFunctions::existTable($dbName, 'player_' . $i) === false) {
                    DBController::playerTable($dbName, $i);
                }
                $insert->setTable($dbName . '.player_' . $i);
                foreach (array_chunk($hashPlayer[$i], 3000) as $t) {
                    $insert->insert($t);
                }
            }
        }

        $count = count($arrayPlayer);

        $worldUpdate->player_count = $count;
        $worldUpdate->worldUpdated_at = Carbon::createFromTimestamp(time());
        $worldUpdate->save();
    }

    public static function latestVillages($server, $world){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1800M');
        $dbName = BasicFunctions::getDatabaseName($server, $world);
        $worldUpdate = World::getWorld($server, $world);

        Schema::dropIfExists("$dbName.village_latest_temp");
        if (BasicFunctions::existTable($dbName, 'village_latest_temp') === false) {
            DBController::villageLatestTable($dbName, 'latest_temp');
        }

        $lines = gzfile("$worldUpdate->url/map/village.txt.gz");
        if (!is_array($lines)) {
            BasicFunctions::createLog("ERROR_update[$server$world]", "village.txt.gz konnte nicht ge&ouml;ffnet werden");
            return;
        }
        $villages = collect();
        foreach ($lines as $line) {
            list($id, $name, $x, $y, $owner, $points, $bonus_id) = explode(',', $line);
            $village = collect();
            $village->put('id', (int)$id);
            $village->put('name', $name);
            $village->put('x', (int)$x);
            $village->put('y', (int)$y);
            $village->put('points', (int)$points);
            $village->put('owner', (int)$owner);
            $village->put('bonus_id', (int)$bonus_id);
            $villages->put($village->get('id'), $village);
        }

        $insert = new Village();
        $insert->setTable($dbName . '.village_latest_temp');
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
            $array [] = $data;
        }
        foreach (array_chunk($array, 3000) as $t) {
            $insert->insert($t);
        }
        
        $villageDB = DBController::prepareVillageChangeCheck($dbName);
        Schema::dropIfExists("$dbName.village_latest");
        DB::statement("ALTER TABLE $dbName.village_latest_temp RENAME TO $dbName.village_latest");
        
        $hashVillage = DBController::hashTable($array, 'v', 'villageID', array(DBController::class, 'villageSameSinceLast'), $villageDB);
        for ($i = 0; $i < config('dsUltimate.hash_village'); $i++) {
            if (array_key_exists($i, $hashVillage)) {
                if (BasicFunctions::existTable($dbName, 'village_' . $i) === false) {
                    DBController::villageTable($dbName, $i);
                }
                $insert->setTable($dbName . '.village_' . $i);
                foreach (array_chunk($hashVillage[$i], 3000) as $t) {
                    $insert->insert($t);
                }
            }
        }

        $count = count($array);

        $worldUpdate->village_count = $count;
        $worldUpdate->worldUpdated_at = Carbon::createFromTimestamp(time());
        $worldUpdate->save();
    }

    public static function latestAlly($server, $world){
        ini_set('max_execution_time', 1800);
        ini_set('memory_limit', '1500M');
        $dbName = BasicFunctions::getDatabaseName($server, $world);
        $worldUpdate = World::getWorld($server, $world);

        Schema::dropIfExists("$dbName.ally_latest_temp");
        if (BasicFunctions::existTable($dbName, 'ally_latest_temp') === false){
            DBController::allyLatestTable($dbName, 'latest_temp');
        }

        $lines = gzfile("$worldUpdate->url/map/ally.txt.gz");
        if(!is_array($lines)) {
            BasicFunctions::createLog("ERROR_update[$server$world]", "ally.txt.gz konnte nicht ge&ouml;ffnet werden");
            return;
        }

        $allys = collect();
        $allyOffs = collect();
        $allyDefs = collect();
        $allyTots = collect();

        foreach ($lines as $line){
            list($id, $name, $tag, $members, $villages, $points, $points_all, $rank) = explode(',', $line);
            $ally = collect();
            $ally->put('id', (int)$id);
            $ally->put('name', $name);
            $ally->put('tag', $tag);
            $ally->put('member_count', (int)$members);
            $ally->put('points', (int)$points_all);
            $ally->put('village_count', (int)$villages);
            $ally->put('rank', (int)$rank);
            $allys->put($ally->get('id'),$ally);
        }

        $offs = gzfile("$worldUpdate->url/map/kill_att_tribe.txt.gz");
        if(!is_array($offs)) {
            BasicFunctions::createLog("ERROR_update[$server$world]", "kill_att_tribe.txt.gz konnte nicht ge&ouml;ffnet werden");
            return;
        }
        foreach ($offs as $off){
            list($rank, $id, $kills) = explode(',', $off);
            $allyOff = collect();
            $allyOff->put('offRank', (int)$rank);
            $allyOff->put('off', (int)$kills);
            $allyOffs->put($id, $allyOff);

        }

        $defs = gzfile("$worldUpdate->url/map/kill_def_tribe.txt.gz");
        if(!is_array($defs)) {
            BasicFunctions::createLog("ERROR_update[$server$world]", "kill_def_tribe.txt.gz konnte nicht ge&ouml;ffnet werden");
            return;
        }
        foreach ($defs as $def){
            list($rank, $id, $kills) = explode(',', $def);
            $allyDef = collect();
            $allyDef->put('defRank', (int)$rank);
            $allyDef->put('def', (int)$kills);
            $allyDefs->put($id, $allyDef);
        }

        $tots = gzfile("$worldUpdate->url/map/kill_all_tribe.txt.gz");
        if(!is_array($tots)) {
            BasicFunctions::createLog("ERROR_update[$server$world]", "kill_all_tribe.txt.gz konnte nicht ge&ouml;ffnet werden");
            return;
        }
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


        Schema::dropIfExists("$dbName.ally_latest");
        DB::statement("ALTER TABLE $dbName.ally_latest_temp RENAME TO $dbName.ally_latest");

        $hashAlly = DBController::hashTable($array, 'a', 'allyID');

        for ($i = 0; $i < config('dsUltimate.hash_ally'); $i++){
            if (array_key_exists($i ,$hashAlly)) {
                if (BasicFunctions::existTable($dbName, 'ally_' . $i) === false) {
                    DBController::allyTable($dbName, $i);
                }
                $insert->setTable($dbName . '.ally_' . $i);
                foreach (array_chunk($hashAlly[$i], 3000) as $t) {
                    $insert->insert($t);
                }
            }
        }

        $count = count($array);

        $worldUpdate->ally_count = $count;
        $worldUpdate->worldUpdated_at = Carbon::createFromTimestamp(time());
        $worldUpdate->save();
    }

    public static function conquer($server, $world){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1500M');
        
        $dbName = BasicFunctions::getDatabaseName($server, $world);
        $worldUpdate = World::getWorld($server, $world);

        if (BasicFunctions::existTable($dbName, 'conquer') === false) {
            DBController::conquerTable($dbName);
        }
        $conquer = new Conquer();
        $conquer->setTable($dbName.'.conquer');
        $first = $conquer->orderBy('timestamp', 'DESC')->first();
        if($first == null)
            $latest = 0;
        else
            $latest = $first->timestamp;
        
        if(time() - $latest > 60 * 60 * 23) {
            $lines = gzfile("$worldUpdate->url/map/conquer.txt.gz");
            if (!is_array($lines)) {
                BasicFunctions::createLog("ERROR_update[$server$world]", "conquer.txt.gz konnte nicht ge&ouml;ffnet werden");
                return;
            }
        } else {
            $lines = gzfile("$worldUpdate->url/interface.php?func=get_conquer&since=" . ($latest - 1));
            if (!is_array($lines)) {
                BasicFunctions::createLog("ERROR_update[$server$world]", "interface.php?func=get_conquer konnte nicht ge&ouml;ffnet werden");
                return;
            }
        }
        
        $array = array();
        $databaseConquer = DBController::prepareConquerDupCheck($dbName);
        
        foreach ($lines as $line) {
            $exploded = explode(',', trim($line));
            if(DBController::conquerInsideDB($databaseConquer, $exploded)) continue;
            
            $tempArr = array();
            list($tempArr['village_id'], $tempArr['timestamp'], $tempArr['new_owner'], $tempArr['old_owner']) = $exploded;
            $tempArr['created_at'] = Carbon::createFromTimestamp(time());
            $tempArr['updated_at'] = Carbon::createFromTimestamp(time());
            $array[] = $tempArr;
        }

        $insert = new Conquer();
        $insert->setTable($dbName . '.conquer');

        foreach (array_chunk($array, 3000) as $t) {
            $insert->insert($t);
        }
    }
    
    public static function cleanOldEntries(World $world, $type) {
        $dbName = BasicFunctions::getDatabaseName($world->server->code, $world->name);
        switch($type) {
            case 'a':
                $envHashIndex = 'hash_ally';
                $tablePrefix = 'ally';
                $model = new Ally();
                break;
                
            case 'p':
                $envHashIndex = 'hash_player';
                $tablePrefix = 'player';
                $model = new Player();
                break;

            case 'v':
                $envHashIndex = 'hash_village';
                $tablePrefix = 'village';
                $model = new Village();
                break;
        }
        
        for ($i = 0; $i < config('dsUltimate.'.$envHashIndex); $i++){
            if (BasicFunctions::existTable($dbName, "{$tablePrefix}_{$i}") === true) {
                $model->setTable("$dbName.{$tablePrefix}_{$i}");
                $delete = $model->where('updated_at', '<', Carbon::createFromTimestamp(time() - (60 * 60 * 24) * config('dsUltimate.db_save_day')));
                $delete->delete();
            }
        }
        $world->worldCleaned_at = Carbon::createFromTimestamp(time());
        $world->save();
    }
    
    private static function prepareVillageChangeCheck($dbName) {
        if(!BasicFunctions::existTable($dbName, 'village_latest')) {
            return array();
        }
        $villageModel = new Village();
        $villageModel->setTable($dbName . '.village_latest');
        
        $arrVil = array();
        foreach ($villageModel->get() as $village) {
            $arrVil[$village->villageID] = array($village->name, $village->points, $village->owner);
        }
        return $arrVil;
    }
    
    /**
     * 
     * @param type $arrVil
     * @param type $data
     * @return boolean true if same is already inside Database
     */
    private static function villageSameSinceLast($arrVil, $data) {
        if(!isset($arrVil[$data['villageID']])) return false;

        $possible_dup = $arrVil[$data['villageID']];
        if($possible_dup[0] == $data['name'] &&
                $possible_dup[1] == $data['points'] &&
                $possible_dup[2] == $data['owner']) {
            return true;
        }
        
        return false;
    }
    
    private static function prepareConquerDupCheck($dbName) {
        $conquerModel = new Conquer();
        $conquerModel->setTable($dbName . '.conquer');
        
        $arrCon = array();
        foreach ($conquerModel->get() as $conquer) {
            if(!isset($arrCon[$conquer->timestamp]))
                $arrCon[$conquer->timestamp] = array();
            
            $arrCon[$conquer->timestamp][] = array($conquer->village_id, $conquer->old_owner, $conquer->new_owner);
        }
        return $arrCon;
    }

    private static function conquerInsideDB($arrCon, $data) {
        if(!isset($arrCon[$data[1]])) return false;
        //echo "Found in DB\n";
        $possible_dups = $arrCon[$data[1]];

        foreach($possible_dups as $possible_dup) {
            if($possible_dup[0] == $data[0] &&
                    $possible_dup[1] == $data[3] &&
                    $possible_dup[2] == $data[2]) {
                return true;
            }
        }
        
        return false;
    }

    public static function hashTable($mainArray, $type, $index,callable $cmpFkt = null, $cmpArr = null){
        $hashArray = array();
        foreach ($mainArray as $main){
            if($cmpFkt != null && $cmpFkt($cmpArr, $main)) {
                //remove "unwanted" entries
                continue;
            }
            $id = $main[$index];
            if (! array_key_exists(BasicFunctions::hash($id, $type), $hashArray)) {
                $hashArray[BasicFunctions::hash($id, $type)] = array();
            }
            $hashArray[BasicFunctions::hash($id, $type)][] = $main;
        }

        return $hashArray;
    }
}
