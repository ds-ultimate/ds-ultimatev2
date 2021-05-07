<?php

namespace App\Console\Commands\MigrationHelper;

use App\Ally;
use App\Console\DatabaseUpdate\TableGenerator;
use App\HistoryIndex;
use App\Player;
use App\Village;
use App\World;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InsertMissingSpeedHistoryData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:insertMissingSpeedHistoryData {server} {world}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix für Speed welten history daten';

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
        ini_set('max_execution_time', 1800);
        ini_set('memory_limit', '10000M');
        
        $server = $this->argument('server');
        $world = $this->argument('world');
        $worldMod = World::getWorld($server, $world);
        
        if($worldMod !== null) {
            if($worldMod->isSpeed()) {
                $this->insertMissing($worldMod);
            } else {
                echo "This is not a speed server";
            }
        } else {
            echo "Unable to find $server $world";
        }
        
        return 0;
    }
    
    private function insertMissing(World $worldModel) {
        $dbName = BasicFunctions::getDatabaseName($worldModel->server->code, $worldModel->name);
        echo "Doing world $dbName\n";

        $bar = $this->output->createProgressBar();
        mkdir(storage_path(config('dsUltimate.history_directory') . $dbName), 0777, true);
        
        echo "Doing 30-Ally\n";
        $playDate = static::doInserting($dbName, config('dsUltimate.hash_ally'), "ally",
            function($entry) {
                return "{$entry->allyID};{$entry->name};{$entry->tag};{$entry->member_count};"
                    . "{$entry->points};{$entry->village_count};{$entry->rank};"
                    . "{$entry->offBash};{$entry->offBashRank};"
                    . "{$entry->defBash};{$entry->defBashRank};"
                    . "{$entry->gesBash};{$entry->gesBashRank}";
            }, "allyID", $bar);
        
        echo "Doing 30-Player\n";
        $allyDate = static::doInserting($dbName, config('dsUltimate.hash_player'), "player",
            function($entry) {
                return "{$entry->playerID};{$entry->name};{$entry->ally_id};"
                    . "{$entry->points};{$entry->village_count};{$entry->rank};"
                    . "{$entry->offBash};{$entry->offBashRank};"
                    . "{$entry->defBash};{$entry->defBashRank};"
                    . "{$entry->supBash};{$entry->supBashRank};"
                    . "{$entry->gesBash};{$entry->gesBashRank}";
            }, "playerID", $bar);
        
        $dates = array_merge($playDate, $allyDate);
        static::specialVillageInsert($dbName, $dates,
            function($entry) {
                return "{$entry->villageID};{$entry->name};{$entry->x};{$entry->y};"
                    . "{$entry->points};{$entry->owner};{$entry->bonus_id};";
            }, $bar);
        
        $bar->finish();
        echo "\n";
        
        foreach($dates as $date) {
            $histIdx = new HistoryIndex();
            $histIdx->setTable($dbName . ".index");
            $histIdx->date = $date;
            $histIdx->save();
        }
    }
    
    private static function doInserting($dbName, $hashCnt, $type, $insertCallback, $idCol, $bar) {
        $toInsert = [];
        for ($num = 0; $num < $hashCnt; $num++){
            if(!BasicFunctions::existTable($dbName, "{$type}_$num")) {
                continue;
            }
            $res = DB::select("SELECT * FROM $dbName.{$type}_$num ORDER BY 'created_at' ASC");
            
            foreach($res as $entry) {
                $dateCarb = Carbon::parse($entry->created_at);
                $date = $dateCarb->format("Y-m-d_H");
                if(! isset($toInsert[$date])) {
                    $toInsert[$date] = [];
                }
                $toInsert[$date][$entry->$idCol] = $insertCallback($entry);
            }
            
            $bar->advance();
        }
        
        $foundDates = array_keys($toInsert);
        sort($foundDates);
        
        foreach($foundDates as $date) {
            $ids = array_keys($toInsert[$date]);
            sort($ids);
            
            $fileName = storage_path(config('dsUltimate.history_directory') . "{$dbName}/{$type}_$date.gz");
            $file = gzopen($fileName, "w9");
            foreach($ids as $id) {
                gzwrite($file, $toInsert[$date][$id] . "\n");
            }
            gzclose($file);
            $bar->advance();
        }
        return $foundDates;
    }
    
    private static function specialVillageInsert($dbName, $dates, $insertCallback, $bar) {
        $villages = [];
        foreach($dates as $date) {
            $villages[$date] = [];
        }
        
        echo "Reading Village\n";
        for ($num = 0; $num < config('dsUltimate.hash_village'); $num++){
            if(!BasicFunctions::existTable($dbName, "village_$num")) {
                continue;
            }
            
            $res = DB::select("SELECT * FROM $dbName.village_$num ORDER BY 'created_at' ASC");
            foreach($res as $entry) {
                $dateCarb = Carbon::parse($entry->created_at);
                $date = $dateCarb->format("Y-m-d_H");
                
                if(isset($villages[$date])) {
                    $villages[$date][$entry->villageID] = $entry;
                } else {
                    //echo "Village date without player: $date";
                    //die();
                }
            }
            $bar->advance();
        }
        unset($res);
        
        $keys = array_keys($villages);
        sort($keys);
        $running = [];
        $first = true;
        foreach($keys as $key) {
            if($first) {
                $first = false;
                continue;
            }
            foreach($villages[$key] as $id => $vill) {
                $running[$id] = $vill;
            }
            $villages[$key] = $running;
        }
	//dd($keys, $dates);
        unset($running);
        
        echo "Writing Village\n";
        $first = true;
        foreach($keys as $date) {
            if($first) {
                $first = false;
                continue;
            }
            $ids = array_keys($villages[$date]);
            sort($ids);
            
            $fileName = storage_path(config('dsUltimate.history_directory') . "{$dbName}/village_$date.gz");
            $file = gzopen($fileName, "w9");
            foreach($ids as $id) {
                gzwrite($file, $insertCallback($villages[$date][$id]) . "\n");
            }
            gzclose($file);
            $bar->advance();
        }
    }
    
    private static function getDates($dbName) {
        $res = (new Village())->setTable("$dbName.village_1")->orderBy('created_at', 'ASC')->first();
        $dates = [];
        $tempCarb = Carbon::parse($res->created_at);
        $target = Carbon::now()->format("Y-m-d_H");
        $tempStr = "";
        while($target !== $tempStr) {
            $tempStr = $tempCarb->format("Y-m-d_H");
            $dates[] = $tempStr;
            $tempCarb = $tempCarb->addHour();
        }
        return $dates;
    }
}
