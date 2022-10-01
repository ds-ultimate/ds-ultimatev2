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
        $serverArray = Server::getServer();

        foreach ($serverArray as $serverModel){
            if(! $serverModel->speed_active && ! $serverModel->classic_active) {
                continue;
            }
            
            $curActive = (new SpeedWorld())
                    ->where('server_id', $serverModel->id)
                    ->where('planned_start', '<=', time())
                    ->where(function($query) {
                        $query->orWhere('planned_end', '>=', time())
                            ->orWhere('planned_end', -1);
                    })
                    ->get();
            
            $worldFile = file_get_contents($serverModel->url.'/backend/get_servers.php');
            $worldArray = unserialize($worldFile);
            
            foreach ($worldArray as $world => $link){
                if($serverModel->code != substr($world, 0, 2)) {
                    echo "Ignoring {$serverModel->code} / {$world}\n";
                    continue;
                }
                $worldName = substr($world, 2);
                if(World::isSpeedName($worldName) && $serverModel->speed_active) {
                    //ok
                } else if(World::isClassicServerName($worldName) && $serverModel->classic_active) {
                    //ok
                } else {
                    //ignore everything other than speed / classic
                    continue;
                }
                
                //find coresponding speedWorld
                $match = null;
                foreach($curActive as $sWorld) {
                    if($sWorld->instance == null) {
                        //this world had not been assigned to an instance -> possible
                        if($match == null) {
                            $match = $sWorld;
                        } else {
                            $exception = new \Exception("Please add support for more than one speed world at the same time" . $serverModel->code);
                            DiscordNotificationQueueElement::exception($exception);
                            $match = null;
                            break;
                        }
                    } else if($sWorld->instance == $worldName) {
                        $match = $sWorld;
                        break;
                    }
                }
                
                if(isset($exception)) {
                    unset($exception);
                    continue;
                } else if($match == null) {
                    $exception = new \Exception("World in backend but not in planned " . $link . " at " . $serverModel->code);
                    DiscordNotificationQueueElement::exception($exception);
                    unset($exception);
                    continue;
                }
                
                $instanceName = $worldName;
                $worldName = $match->name;
                $match->started = true;
                $match->worldCheck_at = Carbon::now();
                $match->update();
                
                if($match->world_id !== null) {
                    //world exists already -> update
                    $create = false;
                    $worldNew = $match->world;
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
                    if($match->display_name !== null) {
                        $worldNew->display_name = $match->display_name;
                    } else {
                        $worldNew->display_name = $worldNew->generateDisplayName();
                    }
                    $worldNew->hash_ally = 1;
                    $worldNew->hash_player = 1;
                    $worldNew->hash_village = 4;
                    
                    $world = $serverModel->code . $worldNew->name;
                    if ($worldNew->save() !== true){
                        BasicFunctions::createLog('ERROR_insert[World]', "Welt $world konnte nicht der Tabelle 'worlds' hinzugefügt werden.");
                        continue;
                    }
                    
                    $match->instance = $instanceName;
                    $match->world_id = $worldNew->id;
                    $match->update();

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
                    BasicFunctions::createLog("createBD[$world]", "DB '$dbRaw' wurde erfolgreich erstellt.");
                }
            }
        }

        $worldModel = new World();
        
        foreach ($worldModel->where('worldCheck_at', '<', Carbon::now()->subHours(2)->subMinutes(20))->get() as $world ){
            if(! $world->isSpecialServer()) {
                continue;
            }
            if($world->active != null) {
                BasicFunctions::createLog("Status[$world->name]", "$world->name ist nicht mehr aktiv");
            }
            $world->active = null;
            $world->update();
        }
        foreach ((new SpeedWorld())->where('worldCheck_at', '<', Carbon::now()->subHours(2)->subMinutes(20))->get() as $world ){
            if($world->started) {
                BasicFunctions::createLog("Status[$world->name]", "$world->name ist nicht mehr aktiv");
            }
            $world->started = false;
            if($world->planned_end == -1) {
                $world->planned_end = Carbon::now()->timestamp;
            }
            $world->update();
        }

    }
}
