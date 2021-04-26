<?php

namespace App\Console\Commands\MigrationHelper;

use App\HistoryIndex;
use App\World;
use App\Util\BasicFunctions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class HistoryDataToDisk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:historyToDisk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Speichert alle _history Datenbanken in Files ab';

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
        ini_set('memory_limit', '2000M');
        
        $worlds = (new World())->get();
        foreach ($worlds as $world){
            $this->toDisk($world);
        }
        
        return 0;
    }
    
    private function toDisk(World $worldModel) {
        $dbName = BasicFunctions::getDatabaseName($worldModel->server->code, $worldModel->name);
        echo "Doing world $dbName\n";
        
        if(! BasicFunctions::existDatabase("{$dbName}_history")) {
            //nothing to do
            return;
        }
        if(! BasicFunctions::existTable("{$dbName}_history", "index")) {
            //index missing
            echo "Found missing index Table $dbName\n";
            return;
        }
        
        $histIds = (new HistoryIndex())->setTable("{$dbName}_history.index")->get();
        $dates = [];
        foreach($histIds as $histId) {
            $dates[] = $histId->date;
        }
        
        $bar = $this->output->createProgressBar(3 * count($dates));
        
        mkdir(storage_path(config('dsUltimate.history_directory') . $dbName), 0777, true);
        
        static::partToDisk($dbName, $dates, "ally",
            function($entry) {
                return "{$entry->allyID};{$entry->name};{$entry->tag};{$entry->member_count};"
                    . "{$entry->points};{$entry->village_count};{$entry->rank};"
                    . "{$entry->offBash};{$entry->offBashRank};"
                    . "{$entry->defBash};{$entry->defBashRank};"
                    . "{$entry->gesBash};{$entry->gesBashRank}";
            }, $bar);
        
        static::partToDisk($dbName, $dates, "player",
            function($entry) {
                return "{$entry->playerID};{$entry->name};{$entry->ally_id};"
                    . "{$entry->points};{$entry->village_count};{$entry->rank};"
                    . "{$entry->offBash};{$entry->offBashRank};"
                    . "{$entry->defBash};{$entry->defBashRank};"
                    . "{$entry->supBash};{$entry->supBashRank};"
                    . "{$entry->gesBash};{$entry->gesBashRank}";
            }, $bar);
        
        static::partToDisk($dbName, $dates, "village",
            function($entry) {
                return "{$entry->villageID};{$entry->name};{$entry->x};{$entry->y};"
                    . "{$entry->points};{$entry->owner};{$entry->bonus_id};";
            }, $bar);
        
        $bar->finish();
        echo "\n";

        echo "Moving index\n";
        DB::statement("ALTER TABLE {$dbName}_history.index RENAME {$dbName}.index");
        echo "Deleting database\n";
        DB::statement("DROP DATABASE {$dbName}_history");
    }
    
    private static function partToDisk($dbName, $dates, $type, $entryToLineCallback, $bar) {
        foreach($dates as $date){
            $res = DB::select("SELECT * FROM {$dbName}_history.`{$type}_$date`");
            
            $fileName = storage_path(config('dsUltimate.history_directory') . "{$dbName}/{$type}_$date.gz");
            $file = gzopen($fileName, "w9");
            foreach($res as $entry) {
                gzwrite($file, $entryToLineCallback($entry) . "\n");
            }
            
            gzclose($file);
            $bar->advance();
        }
    }
}
