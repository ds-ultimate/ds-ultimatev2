<?php

namespace App\Console\Commands\MigrationHelper;

use App\Ally;
use App\AllyTop;
use App\Player;
use App\PlayerTop;
use App\HistoryIndex;
use App\World;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Console\Command;


class GenerateTopsFromHist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:generateTopsFromHist {server=null} {world=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Erstellt die Top werte aus den History werten';

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
        static::generateTopsFromHist($this->argument('server'), $this->argument('world'), $this->output);
        return 0;
    }
    
    public static function generateTopsFromHist($server, $world, $output) {
        if ($server != null && $world != null && $server != "null" && $world != "null") {
            $worldMod = World::getWorld($server, $world);
            static::internalGenerateTops($worldMod, 'p');
            static::internalGenerateTops($worldMod, 'a');
        } else {
            $worlds = BasicFunctions::getWorldQuery()->get();
            
            foreach ($worlds as $world){
                static::internalGenerateTops($world, 'p');
                static::internalGenerateTops($world, 'a');
            }
        }
    }
    
    public static function internalGenerateTops($world, $type) {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2000M');
        $dbName = BasicFunctions::getDatabaseName($world->server->code, $world->name);
        
        switch ($type) {
            case 'a':
                $model = new Ally();
                $values = [
                    ['member_count', 'member_count_top', 'member_count_date', 1, 3],
                    ['village_count', 'village_count_top', 'village_count_date', 1, 5],
                    ['points', 'points_top', 'points_date', 1, 4],
                    ['rank', 'rank_top', 'rank_date', -1, 6],
                    ['offBash', 'offBash_top', 'offBash_date', 1, 7],
                    ['offBashRank', 'offBashRank_top', 'offBashRank_date', -1, 8],
                    ['defBash', 'defBash_top', 'defBash_date', 1, 9],
                    ['defBashRank', 'defBashRank_top', 'defBashRank_date', -1, 10],
                    ['gesBash', 'gesBash_top', 'gesBash_date', 1, 11],
                    ['gesBashRank', 'gesBashRank_top', 'gesBashRank_date', -1, 12],
                ];
                $copy = [
                    ['name', 1],
                    ['tag', 2],
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
                    ['village_count', 'village_count_top', 'village_count_date', 1, 4],
                    ['points', 'points_top', 'points_date', 1, 3],
                    ['rank', 'rank_top', 'rank_date', -1, 5],
                    ['offBash', 'offBash_top', 'offBash_date', 1, 6],
                    ['offBashRank', 'offBashRank_top', 'offBashRank_date', -1, 7],
                    ['defBash', 'defBash_top', 'defBash_date', 1, 8],
                    ['defBashRank', 'defBashRank_top', 'defBashRank_date', -1, 9],
                    ['supBash', 'supBash_top', 'supBash_date', 1, 10],
                    ['supBashRank', 'supBashRank_top', 'supBashRank_date', -1, 11],
                    ['gesBash', 'gesBash_top', 'gesBash_date', 1, 12],
                    ['gesBashRank', 'gesBashRank_top', 'gesBashRank_date', -1, 13],
                ];
                $copy = [
                    ['name', 1],
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
        
        $hist = new HistoryIndex();
        $hist->setTable("$dbName.index");
        
        foreach($hist->get() as $history) {
            $file = gzopen(config('dsUltimate.history_directory') . "{$dbName}/{$typeN}_{$history->date}.gz", "r");
            
            $i = 0;
            if(! $world->isSpeed()) {
                $date = Carbon::parse($history->date);
            } else {
                $date = Carbon::parse(str_replace("_", " ", $history->date) . ":00:00");
            }
            while(! gzeof($file)) {
                $line = str_replace("\n", "", gzgets($file));
                if($line == "") continue;
                $elm = explode(";", $line);
                
                if(isset($curData[$elm[0]])) {
                    $curModel = $curData[$elm[0]];
                } else {
                    $curModel = $generateCallback();
                    $curModel->setTable("$dbName.{$typeN}_top");
                    $curModel->$idCol = $elm[0];
                    foreach($copy as $cp) {
                        $curModel->{$cp[0]} = $elm[$cp[1]];
                    }
                    foreach($values as $val) {
                        $curModel->{$val[1]} = $elm[$val[4]] ?? 0;
                        if($elm[$val[4]] == "") {
                            $curModel->{$val[1]} = 0;
                        }
                        $curModel->{$val[2]} = $date;
                    }
                    $curData[$elm[0]] = $curModel;
                    continue;
                }
                
                foreach($values as $val) {
                    if($elm[$val[4]] == "") continue;
                    if( ($val[3] > 0 && $curModel->{$val[1]} < $elm[$val[4]]) ||
                        ($val[3] < 0 && $curModel->{$val[1]} > $elm[$val[4]])) {
                        $curModel->{$val[1]} = $elm[$val[4]];
                        $curModel->{$val[2]} = $date;
                    }
                }
                $i++;
                if($i % 100 == 0) {
                    echo "\r$dbName $typeN doing: {$history->date} at: $i";
                }
            }
            gzclose($file);
        }
        echo "\n";
        
        $changed = 0;
        foreach($curData as $entry) {
            if(count($entry->getDirty()) > 0) {
                $changed++;
            }
            $entry->save();
            echo "\r$dbName $typeN Inserting: $changed";
        }
        echo "\n";
    }
}
