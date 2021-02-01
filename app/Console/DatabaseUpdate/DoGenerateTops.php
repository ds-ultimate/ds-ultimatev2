<?php

namespace App\Console\DatabaseUpdate;

use App\Ally;
use App\AllyTop;
use App\Player;
use App\PlayerTop;
use App\Util\BasicFunctions;

class DoGenerateTops
{
    /**
     * @param \App\World $world model
     * @param String $type can be a for ally or p for player
     */
    public static function run($world, $type, $progress=true){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2000M');
        $dbName = BasicFunctions::getDatabaseName($world->server->code, $world->name);
        
        switch ($type) {
            case 'a':
                $model = new Ally();
                $values = [
                    ['member_count', 'member_count_top', 'member_count_date', 1],
                    ['village_count', 'village_count_top', 'village_count_date', 1],
                    ['points', 'points_top', 'points_date', 1],
                    ['rank', 'rank_top', 'rank_date', -1],
                    ['offBash', 'offBash_top', 'offBash_date', 1],
                    ['offBashRank', 'offBashRank_top', 'offBashRank_date', -1],
                    ['defBash', 'defBash_top', 'defBash_date', 1],
                    ['defBashRank', 'defBashRank_top', 'defBashRank_date', -1],
                    ['gesBash', 'gesBash_top', 'gesBash_date', 1],
                    ['gesBashRank', 'gesBashRank_top', 'gesBashRank_date', -1],
                ];
                $copy = [
                    'name',
                    'tag',
                ];
                $typeN = 'ally';
                $hashSize = config('dsUltimate.hash_ally');
                $generateCallback = function() {
                    return new AllyTop();
                };
                break;
            case 'p':
                $model = new Player();
                $values = [
                    ['village_count', 'village_count_top', 'village_count_date', 1],
                    ['points', 'points_top', 'points_date', 1],
                    ['rank', 'rank_top', 'rank_date', -1],
                    ['offBash', 'offBash_top', 'offBash_date', 1],
                    ['offBashRank', 'offBashRank_top', 'offBashRank_date', -1],
                    ['defBash', 'defBash_top', 'defBash_date', 1],
                    ['defBashRank', 'defBashRank_top', 'defBashRank_date', -1],
                    ['supBash', 'supBash_top', 'supBash_date', 1],
                    ['supBashRank', 'supBashRank_top', 'supBashRank_date', -1],
                    ['gesBash', 'gesBash_top', 'gesBash_date', 1],
                    ['gesBashRank', 'gesBashRank_top', 'gesBashRank_date', -1],
                ];
                $copy = [
                    'name',
                ];
                $typeN = 'player';
                $hashSize = config('dsUltimate.hash_player');
                $generateCallback = function() {
                    return new PlayerTop();
                };
                break;
            default:
                return;
        }
        
        //load all current top data into memory
        $curData = [];
        $idCol = $typeN . "ID";
        $model->setTable("$dbName.{$typeN}_top");
        foreach($model->get() as $elm) {
            $curData[$elm->$idCol] = $elm;
        }
        
        for($num = 0; $num < $hashSize; $num++) {
            if (BasicFunctions::existTable($dbName, "{$typeN}_$num") === false){
                continue;
            }
            
            $curModel = null;
            $model->setTable("$dbName.{$typeN}_$num");
            if($world->worldTop_at !== null) {
                $query = $model->where("created_at", ">", $world->worldTop_at)->orderBy($idCol);
            } else {
                $query = $model->orderBy($idCol);
            }
            
            $i = 0;
            $changed = 0;
            foreach($query->get() as $elm) {
                if($curModel !== null && $curModel->$idCol != $elm->$idCol) {
                    //finished processing that one move to next
                    if(count($curModel->getDirty()) > 0) {
                        $changed++;
                    }
                    $curModel->save();
                    $curModel = null;
                }
                if($curModel == null) {
                    //start with next one
                    if(isset($curData[$elm->$idCol])) {
                        $curModel = $curData[$elm->$idCol];
                    } else {
                        $curModel = $generateCallback();
                        $curModel->setTable("$dbName.{$typeN}_top");
                        $curModel->$idCol = $elm->$idCol;
                        foreach($copy as $cp) {
                            $curModel->$cp = $elm->$cp;
                        }
                        foreach($values as $val) {
                            $curModel->{$val[1]} = $elm->{$val[0]} ?? 0;
                            $curModel->{$val[2]} = $elm->created_at;
                        }
                        $curData[$elm->$idCol] = $curModel;
                        continue;
                    }
                }
                
                foreach($copy as $cp) {
                    if($curModel->$cp != $elm->$cp) {
                        $curModel->$cp = $elm->$cp;
                    }
                }
                foreach($values as $val) {
                    if( ($val[3] > 0 && $curModel->{$val[1]} < $elm->{$val[0]}) ||
                        ($val[3] < 0 && $curModel->{$val[1]} > $elm->{$val[0]})) {
                        $curModel->{$val[1]} = $elm->{$val[0]};
                        $curModel->{$val[2]} = $elm->created_at;
                    }
                }
                $i++;
                if($progress && $i % 100 == 0) {
                    echo "\r$dbName $typeN doing: $num at: $i  inserted:$changed      ";
                }
            }
            if($curModel !== null) {
                $curModel->save();
            }
        }
        if($progress) {
            echo "\n";
        }
    }
}
