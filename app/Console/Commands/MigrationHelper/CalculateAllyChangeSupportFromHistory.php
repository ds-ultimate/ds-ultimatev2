<?php

namespace App\Console\Commands\MigrationHelper;

use App\AllyChanges;
use App\AllySupport;
use App\World;
use App\Console\DatabaseUpdate\TableGenerator;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * This needs to be run directly after the update
 * with all crons beeing disabled
 */
class CalculateAllyChangeSupportFromHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:calcAllyChangeSupport {--world=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Berechnet die unterstützungs-Werte für Stämme';
    
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
        ini_set('memory_limit', '5000M');
        $worlds = $this->option('world');
        foreach($worlds as $w) {
            if($w == "active") {
                $world = (new World())->where('active', 1)->get();
                foreach ($world as $wInner) {
                    $this->calc($wInner);
                }
            } else if($w == "*") {
                $world = (new World())->get();
                foreach ($world as $wInner) {
                    $this->calc($wInner);
                }
            } else {
                $world = World::getWorld(substr($w, 0, 2), substr($w, 2));
                if($world != null) {
                    $this->calc($world);
                } else {
                    echo "World $w not found\n";
                }
            }
        }
        
        return 0;
    }
    
    private function calc(World $worldModel) {
        $dirName = $worldModel->serName();
        
        $allyChangeTable = BasicFunctions::getWorldDataTable($worldModel, "ally_changes");
        $data = DB::select("SELECT `player_id`, `old_ally_id`, `new_ally_id`, `supBash`, `offBash`, `deffBash`, `points`, `created_at`, `updated_at` FROM $allyChangeTable");
        
        $historyCache = [];
        $historyCacheO = [];
        $historyCacheD = [];
        $dataNew = [];
        foreach($data as $d) {
            $date = Carbon::parse($d->created_at)->format("Y-m-d");
            if(! isset($historyCache[$date])) {
                $curDate = [];
                $curDateO = [];
                $curDateD = [];
                $toFile = storage_path(config('dsUltimate.history_directory') . "{$dirName}/player_$date.gz");
                if(is_file($toFile)) {
                    $file = gzopen($toFile, "r");
                    while(! gzeof($file)) {
                        $lineOrig = gzgets($file, 4096);
                        if($lineOrig === false) continue;
                        $line = explode(";", str_replace("\n", "", $lineOrig));
                        $curDate[$line[0]] = $line[10];
                        $curDateO[$line[0]] = $line[6];
                        $curDateD[$line[0]] = $line[8];
                    }
                    gzclose($file);
                }
                $historyCache[$date] = $curDate;
                $historyCacheO[$date] = $curDateO;
                $historyCacheD[$date] = $curDateD;
            }
            if(isset($historyCache[$date][$d->player_id])) {
                $d->supBash = $historyCache[$date][$d->player_id];
                $d->offBash = $historyCacheO[$date][$d->player_id];
                $d->deffBash = $historyCacheD[$date][$d->player_id];
            }
            $dataNew[] = (array) $d;
        }
        unset($historyCache);
        unset($historyCacheO);
        unset($historyCacheD);
        unset($data);
        
        DB::statement("ALTER TABLE $allyChangeTable RENAME TO {$allyChangeTable}_tmp");
        TableGenerator::allyChangeTable($worldModel);
        $insert = new AllyChanges($worldModel);
        foreach (array_chunk($dataNew, 3000) as $t){
            $insert->insert($t);
        }
        Schema::drop("{$allyChangeTable}_tmp");
        
        $data_support = [];
        $data_off= [];
        $data_deff = [];
        $player_running = [];
        $player_runningO = [];
        $player_runningD = [];
        foreach($dataNew as $change) {
            $plID = $change['player_id'];
            $oA_ID = $change['old_ally_id'];
            $nA_ID = $change['new_ally_id'];
            $sb = $change['supBash'];
            $ob = $change['offBash'];
            $db = $change['deffBash'];
            
            $curVal = $sb - ($player_running[$plID] ?? 0);
            $curValO = $ob - ($player_runningO[$plID] ?? 0);
            $curValD = $db - ($player_runningD[$plID] ?? 0);
            $player_running[$plID] = $sb;
            $player_runningO[$plID] = $ob;
            $player_runningD[$plID] = $db;
            
            if(! isset($data_support[$oA_ID])) {
                $data_support[$oA_ID] = [];
                $data_off[$oA_ID] = [];
                $data_deff[$oA_ID] = [];
            }
            $data_support[$oA_ID][$plID] = $curVal + ($data_support[$oA_ID][$plID] ?? 0);
            $data_off[$oA_ID][$plID] = $curValO + ($data_off[$oA_ID][$plID] ?? 0);
            $data_deff[$oA_ID][$plID] = $curValD + ($data_deff[$oA_ID][$plID] ?? 0);
        }
        
        //prepare for insert
        $insertTime = Carbon::now();
        $sup_insert = [];
        foreach($data_support as $ally_id => $sup_ally) {
            foreach($sup_ally as $player_id => $sup) {
                $sup_insert[] = [
                    "ally_id" => $ally_id,
                    "player_id" => $player_id,
                    "supBash" => $sup,
                    "offBash" => $data_off[$ally_id][$player_id],
                    "deffBash" => $data_deff[$ally_id][$player_id],
                    "created_at" => $insertTime,
                    "updated_at" => $insertTime,
                ];
            }
        }
        
        $insert = new AllySupport($worldModel);
        foreach (array_chunk($sup_insert, 3000) as $t){
            $insert->insert($t);
        }
        
        $ges = count($sup_insert);
        echo "{$worldModel->serName()} - $ges\n";
    }
}
