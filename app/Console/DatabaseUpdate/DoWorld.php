<?php

namespace App\Console\DatabaseUpdate;

use App\Server;
use App\World;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DoWorld
{
    public static function run(){
        if (BasicFunctions::existTable(null, 'worlds') === false){
            TableGenerator::worldTable();
        }

        $serverArray = Server::getServer();

        foreach ($serverArray as $serverUrl){
            $worldFile = file_get_contents($serverUrl->url.'/backend/get_servers.php');
            $worldTable = new World();
            $worldTable->setTable('worlds');
            $worldArray = unserialize($worldFile);
            foreach ($worldArray as $world => $link){
                if($serverUrl->code != substr($world, 0, 2)) {
                    echo "Ignoring {$serverUrl->code} / {$world}\n";
                    continue;
                }
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
                } else if($serverUrl->active != 1) {
                    continue;
                } else {
                    //create new entry
                    $create = true;
                    $worldNew = new World();
                    $worldNew->worldUpdated_at = Carbon::now()->subHours(24);
                    $worldNew->worldCleaned_at = Carbon::now()->subHours(24);
                    $worldNew->setTable('worlds');
                }

                $worldNew->server_id = $serverUrl->id;
                $worldNew->name = $worldName;
                $worldNew->url = $link;
                $txtConf = file_get_contents("$link/interface.php?func=get_config");
                $worldNew->config = $txtConf;
                $txtUnits = file_get_contents("$link/interface.php?func=get_unit_info");
                $worldNew->units = $txtUnits;
                $txtBuildings = file_get_contents("$link/interface.php?func=get_building_info");
                $worldNew->buildings = $txtBuildings;
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
                TableGenerator::historyIndexTable($name);
                BasicFunctions::createLog("createBD[$world]", "DB '$name' wurde erfolgreich erstellt.");
            }
        }

        $worldModel = new World();

        foreach ($worldModel->where('worldCheck_at', '<', Carbon::createFromTimestamp(time() - (60 * 30)))->get() as $world ){
            if($world->active != null) {
                BasicFunctions::createLog("Status[$world->name]", "$world->name ist nicht mehr aktiv");
            }
            $world->active = null;
            $world->update();
        }

    }
}
