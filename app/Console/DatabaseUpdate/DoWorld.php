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
        $serverArray = (new Server())->where("active", 1)->get();

        foreach ($serverArray as $serverModel){
            $worldFile = file_get_contents($serverModel->url.'/backend/get_servers.php');
            $worldModel = new World();
            $worldArray = unserialize($worldFile);
            foreach ($worldArray as $world => $link){
                if($serverModel->code != substr($world, 0, 2)) {
                    echo "Ignoring {$serverModel->code} / {$world}\n";
                    continue;
                }
                $worldName = substr($world, 2);
                if(World::isSpecialServerName($worldName)) {
                    //ignore speed worlds here since we have a seperate update system for them
                    continue;
                }

                if ($worldModel->where('server_id', $serverModel->id)->where('name', $worldName)->count() >= 1){
                    //world exists already -> update
                    $create = false;
                    $worldNew = null;
                    foreach($worldModel->where('server_id', $serverModel->id)->where('name', $worldName)->get() as $world) {
                        if($worldNew == null) {
                            $worldNew = $world;
                            if($world->active == null) {
                                $world->active = 1;
                            }
                            $world->worldCheck_at = Carbon::now();
                            $world->update();
                        } else {
                            $world->delete();
                        }
                    }
                } else if($serverModel->active != 1) {
                    continue;
                } else {
                    //create new entry
                    $create = true;
                    $worldNew = new World();
                    $worldNew->worldUpdated_at = Carbon::now()->subHours(24);
                    $worldNew->worldCleaned_at = Carbon::now()->subHours(24);
                    $worldNew->setTable('worlds');
                }

                $worldNew->server_id = $serverModel->id;
                $worldNew->name = $worldName;
                $worldNew->url = $link;
                $txtConf = file_get_contents("$link/interface.php?func=get_config");
                $worldNew->config = $txtConf;
                $txtUnits = file_get_contents("$link/interface.php?func=get_unit_info");
                $worldNew->units = $txtUnits;
                $txtBuildings = file_get_contents("$link/interface.php?func=get_building_info");
                $worldNew->buildings = $txtBuildings;
                $worldNew->worldCheck_at = Carbon::now();
                $worldNew->display_name = $worldNew->generateDisplayName();
                $worldNew->hash_ally = config('dsUltimate.hash_ally');
                $worldNew->hash_player = config('dsUltimate.hash_player');
                $worldNew->hash_village = config('dsUltimate.hash_village');

                if ($worldNew->save() !== true){
                    BasicFunctions::createLog('ERROR_insert[World]', "Welt $world konnte nicht der Tabelle 'worlds' hinzugefügt werden.");
                    continue;
                }

                if(!$create) continue;

                BasicFunctions::createLog('insert[World]', "Welt $world wurde erfolgreich der Tabelle '$world' hinzugefügt.");
                $dbRaw = BasicFunctions::getWorldDataDatabase($worldNew);
                if (BasicFunctions::existDatabase($dbRaw) !== false) {
                    BasicFunctions::createLog("ERROR_createBD[$world]", "DB '$dbRaw' existierte bereits.");
                    continue;
                }
                if (DB::statement('CREATE DATABASE ' . $dbRaw) !== true) {
                    BasicFunctions::createLog("ERROR_createBD[$world]", "DB '$dbRaw' konnte nicht erstellt werden.");
                    continue;
                }
                TableGenerator::allyChangeTable($worldNew);
                TableGenerator::allyLatestTable($worldNew, 'latest');
                TableGenerator::conquerTable($worldNew);
                TableGenerator::historyIndexTable($worldNew);
                TableGenerator::playerLatestTable($worldNew, 'latest');
                TableGenerator::villageLatestTable($worldNew, 'latest');
                TableGenerator::playerTopTable($worldNew);
                TableGenerator::allyTopTable($worldNew);
                TableGenerator::attackPlannerTables($worldNew);
                BasicFunctions::createLog("createBD[$world]", "DB '$dbRaw' wurde erfolgreich erstellt.");
            }
        }

        $worldModel = new World();

        foreach ($worldModel->where('worldCheck_at', '<', Carbon::now()->subHours(2)->subMinutes(20))->get() as $world ){
            if($world->isSpecialServer()) {
                continue;
            }
            if($world->active != null) {
                BasicFunctions::createLog("Status[$world->name]", "$world->name ist nicht mehr aktiv");
            }
            $world->active = null;
            $world->update();
        }

    }
}
