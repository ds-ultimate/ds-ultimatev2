<?php

namespace App\Console\DatabaseUpdate;

use App\Ally;
use App\HistoryIndex;
use App\Player;
use App\Village;
use App\World;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WorldHistory
{
    public static function run(World $world) {
        $isSpeed = $world->isSpeed();
//        echo "For " . $world->serName() . "\n";
        if($isSpeed) {
            $date = Carbon::now()->format("Y-m-d_H");
        } else {
            $date = Carbon::now()->format("Y-m-d");
        }
        $retVal = static::runInternal($world, $date, "v", $isSpeed);
        $retVal &= static::runInternal($world, $date, "p", $isSpeed);
        $retVal &= static::runInternal($world, $date, "a", $isSpeed);

        if($retVal) {
            $histIdx = new HistoryIndex($world);
            $histIdx->date = $date;
            $histIdx->save();
        }
    }
    
    private static function runInternal(World $world, $date, $part, $isSpeed) {
        $dirName = $world->serName();
        if(!file_exists(storage_path(config('dsUltimate.history_directory') . $dirName))) {
            mkdir(storage_path(config('dsUltimate.history_directory') . $dirName), 0777, true);
        }
        
        switch ($part) {
            case "village":
            case "v":
                $model = new Village($world);
                $toFile = storage_path(config('dsUltimate.history_directory') . "{$dirName}/village_$date.gz");
                $entryCallback = function($entry) {
                    return "{$entry->villageID};{$entry->name};{$entry->x};{$entry->y};"
                        . "{$entry->points};{$entry->owner};{$entry->bonus_id};";
                };
                break;

            case "player":
            case "p":
                $model = new Player($world);
                $toFile = storage_path(config('dsUltimate.history_directory') . "{$dirName}/player_$date.gz");
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
                $model = new Ally($world);
                $toFile = storage_path(config('dsUltimate.history_directory') . "{$dirName}/ally_$date.gz");
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
        $res = DB::select("SELECT * FROM " . $model->getTable());
        $file = gzopen($toFile, "w9");
        if($file === false ) {
            echo "Unable to open file for $toFile";
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
                echo "Warning wrong date found $dirName " . $model->getTable() . " $part -> $date / $entryDate\n";
            }
        }
        gzclose($file);
        
        if($written == 0) {
            unlink($toFile);
            echo "Nothing written returning error";
            return false;
        }
        return true;
    }
}
