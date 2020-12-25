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
                echo "For $dbName\n";
                $date = Carbon::now()->format("Y-m-d");
                static::updateWorldHistory($dbName, $date, "v");
                static::updateWorldHistory($dbName, $date, "p");
                static::updateWorldHistory($dbName, $date, "a");

                $histIdx = new HistoryIndex();
                $histIdx->setTable($dbName . "_history.index");
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
            $histIdx->setTable($dbName . "_history.index");
            $histIdx->date = $date;
            $histIdx->save();
        }
        return 0;
    }
    
    public static function updateWorldHistory($dbName, $date, $part) {
        switch ($part) {
            case "village":
            case "v":
                $model = new Village();
                $fromTable = $dbName . ".village_latest";
                $toTable = $dbName . "_history.village_$date";
                if(BasicFunctions::existTable($dbName . "_history", "village_$date")) {
                    return;
                }
                DBController::villageLatestTable($dbName . "_history", $date);
                break;

            case "player":
            case "p":
                $model = new Player();
                $fromTable = $dbName . ".player_latest";
                $toTable = $dbName . "_history.player_$date";
                if(BasicFunctions::existTable($dbName . "_history", "player_$date")) {
                    return;
                }
                DBController::playerLatestTable($dbName . "_history", $date);
                break;

            case "ally":
            case "a":
                $model = new Ally();
                $fromTable = $dbName . ".ally_latest";
                $toTable = $dbName . "_history.ally_$date";
                if(BasicFunctions::existTable($dbName . "_history", "ally_$date")) {
                    return;
                }
                DBController::allyLatestTable($dbName . "_history", $date);
                break;
        }
        
        //READ
        $data = [];
        $res = DB::select("SELECT * FROM $fromTable");
        foreach($res as $entry) {
            //Check date
            $entryDate = explode(" ", $entry->created_at)[0];
            if($date == $entryDate) {
                $data[] = (array) $entry;
            } else {
                echo "Warning wrong date found $server$world $part@{$entry->id} -> $date / $entryDate\n";
            }
        }
        
        //WRITE
        $model->setTable($toTable);
        foreach (array_chunk($data, 3000) as $t){
            $model->insert($t);
        }
    }
}
