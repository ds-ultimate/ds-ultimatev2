<?php

namespace App\Console\DatabaseUpdate;

use App\Ally;
use App\HistoryIndex;
use App\Player;
use App\Village;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WorldHistory
{
    public static function run($server, $world, $isSpeed) {
        $dbName = BasicFunctions::getDatabaseName($server, $world);
        //echo "For $dbName\n";
        if($isSpeed) {
            $date = Carbon::now()->format("Y-m-d_H");
        } else {
            $date = Carbon::now()->format("Y-m-d");
        }
        $retVal = static::runInternal($dbName, $date, "v", $isSpeed);
        $retVal &= static::runInternal($dbName, $date, "p", $isSpeed);
        $retVal &= static::runInternal($dbName, $date, "a", $isSpeed);

        if($retVal) {
            $histIdx = new HistoryIndex();
            $histIdx->setTable($dbName . ".index");
            $histIdx->date = $date;
            $histIdx->save();
        }
    }
    
    private static function runInternal($dbName, $date, $part, $isSpeed) {
        if(!file_exists(storage_path(config('dsUltimate.history_directory') . $dbName))) {
            mkdir(storage_path(config('dsUltimate.history_directory') . $dbName), 0777, true);
        }
        
        switch ($part) {
            case "village":
            case "v":
                $model = new Village();
                $fromTable = $dbName . ".village_latest";
                $toFile = storage_path(config('dsUltimate.history_directory') . "{$dbName}/village_$date.gz");
                $entryCallback = function($entry) {
                    return "{$entry->villageID};{$entry->name};{$entry->x};{$entry->y};"
                        . "{$entry->points};{$entry->owner};{$entry->bonus_id};";
                };
                break;

            case "player":
            case "p":
                $model = new Player();
                $fromTable = $dbName . ".player_latest";
                $toFile = storage_path(config('dsUltimate.history_directory') . "{$dbName}/player_$date.gz");
                $entryCallback = function($entry) {
                    return "{$entry->playerID};{$entry->name};{$entry->ally_id};"
                        . "{$entry->points};{$entry->village_count};{$entry->rank};"
                        . "{$entry->offBash};{$entry->offBashRank};"
                        . "{$entry->defBash};{$entry->defBashRank};"
                        . "{$entry->supBash};{$entry->supBashRank};"
                        . "{$entry->gesBash};{$entry->gesBashRank}";
                };
                break;

            case "ally":
            case "a":
                $model = new Ally();
                $fromTable = $dbName . ".ally_latest";
                $toFile = storage_path(config('dsUltimate.history_directory') . "{$dbName}/ally_$date.gz");
                $entryCallback = function($entry) {
                    return "{$entry->allyID};{$entry->name};{$entry->tag};{$entry->member_count};"
                        . "{$entry->points};{$entry->village_count};{$entry->rank};"
                        . "{$entry->offBash};{$entry->offBashRank};"
                        . "{$entry->defBash};{$entry->defBashRank};"
                        . "{$entry->gesBash};{$entry->gesBashRank}";
                };
                break;
        }
        
        //READ
        $res = DB::select("SELECT * FROM $fromTable");
        $file = gzopen($toFile, "w9");
        if($file === false ) {
            echo "Unable to open file for $dbName";
            return false;
        }
        $written = 0;
        foreach($res as $entry) {
            //Check date
            if($isSpeed) {
                $exploded = explode(" ", $entry->created_at);
                $entryDate = $exploded[0] . "_" . explode(":", $exploded[1])[0];
            } else {
                $entryDate = explode(" ", $entry->created_at)[0];
            }
            if($date == $entryDate) {
                gzwrite($file, $entryCallback($entry) . "\n");
                $written++;
            } else {
                echo "Warning wrong date found $dbName $fromTable $part -> $date / $entryDate\n";
            }
        }
        gzclose($file);
        
        if($written == 0) {
            unlink($toFile);
            return false;
        }
        return true;
    }
}
