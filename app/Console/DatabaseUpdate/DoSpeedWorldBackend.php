<?php

namespace App\Console\DatabaseUpdate;

use App\Server;
use App\SpeedWorld;
use App\World;
use App\Notifications\DiscordNotificationQueueElement;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DoSpeedWorldBackend
{
    /**
     * used for updating backend information about speed worlds
     * should be run whenever there is a speed world active or one will get active soon
     */
    public static function run(){
        if (BasicFunctions::existTable(null, 'worlds') === false){
            TableGenerator::worldTable();
        }

        $serverArray = Server::getServer();

        foreach ($serverArray as $serverModel){
            if(! $serverModel->speed_active) {
                continue;
            }
            
            $worldFile = file_get_contents($serverModel->url.'/backend/get_servers.php');
            $worldArray = unserialize($worldFile);
            foreach ($worldArray as $world => $link){
                if($serverModel->code != substr($world, 0, 2)) {
                    echo "Ignoring {$serverModel->code} / {$world}\n";
                    continue;
                }
                $worldName = substr($world, 2);
                if(! World::isSpeedName($worldName)) {
                    //ignore everything other than speed
                    continue;
                }

                $curActive = (new SpeedWorld())
                        ->where('server_id', $serverModel->id)
                        ->where('planned_start', '<=', time())
                        ->where('planned_end', '>=', time())
                        ->get();
                
                if(count($curActive) < 1) {
                    $input = new \Exception("World in backend but not in planned " . $link . " at " . $serverModel->code);
                    DiscordNotificationQueueElement::exception($input);
                    continue;
                }
                if(count($curActive) > 1) {
                    $input = new \Exception("Please add support for more than one speed world " . $serverModel->code);
                    DiscordNotificationQueueElement::exception($input);
                    continue;
                }
                $worldName = $curActive[0]->name;
                $curActive[0]->started = true;
                $curActive[0]->worldCheck_at = Carbon::now();
                $curActive[0]->update();
                
                if($curActive[0]->world_id !== null) {
                    //world exists already -> update
                    $create = false;
                    $worldNew = $curActive[0]->world;
                    if($worldNew->active == null) {
                        $worldNew->active = 1;
                    }
                    $worldNew->worldCheck_at = Carbon::now();
                    $worldNew->update();
                } else {
                    //create new entry
                    $create = true;
                    $worldNew = new World();
                    $worldNew->worldUpdated_at = Carbon::now()->subHours(24);
                    $worldNew->worldCleaned_at = Carbon::now()->subHours(24);
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
                    
                    $world = $serverModel->code . $worldNew->name;
                    if ($worldNew->save() !== true){
                        BasicFunctions::createLog('ERROR_insert[World]', "Welt $world konnte nicht der Tabelle 'worlds' hinzugefügt werden.");
                        continue;
                    }
                    
                    $curActive[0]->world_id = $worldNew->id;
                    $curActive[0]->update();

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
                    TableGenerator::allyTopTable($name);
                    TableGenerator::playerTopTable($name);
                    BasicFunctions::createLog("createBD[$world]", "DB '$name' wurde erfolgreich erstellt.");
                }
            }
        }

        $worldModel = new World();
        
        foreach ($worldModel->where('worldCheck_at', '<', Carbon::now()->subMinutes(20))->get() as $world ){
            if(! $world->isSpeed()) {
                continue;
            }
            if($world->active != null) {
                BasicFunctions::createLog("Status[$world->name]", "$world->name ist nicht mehr aktiv");
            }
            $world->active = null;
            $world->update();
        }
        foreach ((new SpeedWorld())->where('worldCheck_at', '<', Carbon::now()->subMinutes(20))->get() as $world ){
            if($world->started) {
                BasicFunctions::createLog("Status[$world->name]", "$world->name ist nicht mehr aktiv");
            }
            $world->started = false;
            $world->update();
        }

    }
}
