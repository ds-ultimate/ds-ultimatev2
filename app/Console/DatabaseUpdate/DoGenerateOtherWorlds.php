<?php

namespace App\Console\DatabaseUpdate;

use App\PlayerTop;
use App\PlayerOtherServers;
use App\Util\BasicFunctions;

class DoGenerateOtherWorlds
{
    public static function run($worlds, $progress=true){
        $servers = [];
        foreach($worlds as $model) {
            if(! isset($servers[$model->server->code])) {
                $servers[$model->server->code] = [
                    "m" => $model->server,
                    "w" => [],
                ];
            }
            
            $servers[$model->server->code]['w'][] = [
                "name" => $model->name,
                "id" => $model->id,
            ];
        }
        
        foreach($servers as $entry) {
            static::runInternally($entry['m'], $entry['w'], $progress);
        }
    }
    
    /**
     * @param $server the server that should be dealt with
     * @param $worlds multiple world ids from the same server
     */
    public static function runInternally($server, $worlds, $progress=true){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2000M');
        
        $players = [];
        foreach(PlayerOtherServers::prepareModel($server)->get() as $model) {
            $players[$model->playerID] = $model;
        }
        
        $toUpdate = [];
        $i = 0;
        foreach($worlds as $world) {
            //load all current data into memory
            $playerTopModel = new PlayerTop($world);
            foreach($playerTopModel->get() as $elm) {
                if(isset($players[$elm->playerID])) {
                    $model = $players[$elm->playerID];
                } else {
                    $model = PlayerOtherServers::prepareModel($server);
                    $model->playerID = $elm->playerID;
                    $players[$elm->playerID] = $model;
                }
                
                //that function contains a dup check
                $model->addWorld($world['id']);
                $model->name = $elm->name;
                
                if($model->isDirty()) {
                    $toUpdate[$model->playerID] = $model;
                }
                
                $i++;
                if($progress && $i % 100 == 0) {
                    echo "\r" . $world->serName() . " player doing: $i      ";
                }
            }
        }
        
        if($progress) {
            echo "\n";
        }
        
        $i = 0;
        $cnt = count($toUpdate);
        foreach($toUpdate as $model) {
            $i++;
            if($progress && $i % 100 == 0) {
                echo "\rWriting player: $i / $cnt      ";
            }
            $model->save();
        }
        
        if($progress) {
            echo "\n";
        }
    }
}
