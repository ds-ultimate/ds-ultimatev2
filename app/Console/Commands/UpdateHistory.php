<?php

namespace App\Console\Commands;

use App\Ally;
use App\HistoryIndex;
use App\Player;
use App\Village;
use App\World;
use App\Http\Controllers\DBController;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:worldHistory {server=null} {world=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schreibt die aktuellen Infos einer Welt in die Welten History';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \App\Util\BasicFunctions::ignoreErrs();
        ini_set('max_execution_time', 1800);
        ini_set('memory_limit', '2000M');
        
        $server = $this->argument('server');
        $world = $this->argument('world');
        
        if($server == null || $world == null || $server == "null" || $world == "null" || ($server == "*" && $world == "*")) {
            foreach((new World())->where("active", 1)->get() as $dbWorld) {
                $server = $dbWorld->server->code;
                $world = $dbWorld->name;
                $dbName = BasicFunctions::getDatabaseName($server, $world);
                //echo "For $dbName\n";
                $date = Carbon::now()->format("Y-m-d");
                static::updateWorldHistory($dbName, $date, "v");
                static::updateWorldHistory($dbName, $date, "p");
                static::updateWorldHistory($dbName, $date, "a");

                $histIdx = new HistoryIndex();
                $histIdx->setTable($dbName . ".index");
                $histIdx->date = $date;
                $histIdx->save();
            }
        } else {
            $dbName = BasicFunctions::getDatabaseName($server, $world);
            $date = Carbon::now()->format("Y-m-d");
            static::updateWorldHistory($dbName, $date, "v");
            static::updateWorldHistory($dbName, $date, "p");
            static::updateWorldHistory($dbName, $date, "a");

            $histIdx = new HistoryIndex();
            $histIdx->setTable($dbName . ".index");
            $histIdx->date = $date;
            $histIdx->save();
        }
        return 0;
    }
    
    public static function updateWorldHistory($dbName, $date, $part) {
        if(!file_exists(config('dsUltimate.history_directory') . "{$dbName}")) {
            mkdir(config('dsUltimate.history_directory') . "{$dbName}", 0777, true);
        }
        
        switch ($part) {
            case "village":
            case "v":
                $model = new Village();
                $fromTable = $dbName . ".village_latest";
                $toFile = config('dsUltimate.history_directory') . "{$dbName}/village_$date.gz";
                $entryCallback = function($entry) {
                    return "{$entry->villageID};{$entry->name};{$entry->x};{$entry->y};"
                        . "{$entry->points};{$entry->owner};{$entry->bonus_id};";
                };
                break;

            case "player":
            case "p":
                $model = new Player();
                $fromTable = $dbName . ".player_latest";
                $toFile = config('dsUltimate.history_directory') . "{$dbName}/player_$date.gz";
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
                $toFile = config('dsUltimate.history_directory') . "{$dbName}/ally_$date.gz";
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
            return;
        }
        foreach($res as $entry) {
            //Check date
            $entryDate = explode(" ", $entry->created_at)[0];
            if($date == $entryDate) {
                gzwrite($file, $entryCallback($entry) . "\n");
            } else {
                echo "Warning wrong date found $server$world $part@{$entry->id} -> $date / $entryDate\n";
            }
        }
        gzclose($file);
    }
}
