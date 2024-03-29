<?php

namespace App\Console\Commands\MigrationHelper;

use App\Ally;
use App\Player;
use App\Conquer;
use App\World;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InsertMissingBashData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:insertMissingBash';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Füllt null werte in der Datenbank bei den Bashpunkten';

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
        $worlds = (new World())->get();
        foreach ($worlds as $world){
            $this->insertMissing($world);
        }
        
        return 0;
    }
    
    private function insertMissing(World $worldModel) {
        $this->doPlayer($worldModel, "latest");
        for ($num = 0; $num < $worldModel->hash_player; $num++){
            if(!BasicFunctions::hasWorldDataTable($worldModel, "player_$num")) {
                continue;
            }
            $this->doPlayer($worldModel, "$num");
        }
        $this->doAlly($worldModel, 'latest');
        for ($num = 0; $num < $worldModel->hash_ally; $num++){
            if(!BasicFunctions::hasWorldDataTable($worldModel, "ally_$num")) {
                continue;
            }
            $this->doAlly($worldModel, "$num");
        }
    }
    
    private function doPlayer(World $worldModel, $tableSuffix) {
        $tablePre = "player_";
        $playerModel = new Player($worldModel, "$tablePre$tableSuffix");
        echo "Run with " . $world->serName() . " player_$tableSuffix\n";
        
        while(($todo = $playerModel->whereNull('offBash')
                ->orWhereNull('defBash')
                ->orWhereNull('supBash')
                ->orWhereNull('gesBash')
                ->first()) !== null) {
            echo "Doing: " . $world->serName() . ".$tablePre$tableSuffix {$todo->created_at->timestamp} ";
            
            $similar = $this->getEntriesFromSameTime($worldModel, $tablePre, $tableSuffix, $playerModel, $todo, $worldModel->hash_player);
            foreach($similar as $entry) {
                if($entry->offBash === null) $entry->offBash = 0;
                if($entry->defBash === null) $entry->defBash = 0;
                if($entry->gesBash === null) $entry->gesBash = 0;
                if($entry->supBash === null) {
                    $entry->supBash = $entry->gesBash - $entry->defBash - $entry->offBash;
                    if($entry->supBash > 0) {
                        $entry->supBashRank = -1;
                    }
                }
            }
            
            $similar->sortByDesc('supBash');
            $num = $similar->count();
            echo $similar->count() . " ";
            for($i = 1; $i <= $num; $i++) {
                $entry = $similar->get($i - 1);
                if($entry->supBashRank == -1) {
                    $entry->supBashRank = $i;
                }
                if(count($entry->getDirty()) > 0) {
                    DB::update("UPDATE ".$entry->getTable()." SET gesBash=?, defBash=?, offBash=?, supBash=?, "
                            . "supBashRank=? WHERE playerID=? AND created_at=?;", [
                        $entry->gesBash, $entry->defBash, $entry->offBash,
                        $entry->supBash, $entry->supBashRank, $entry->playerID, $entry->created_at
                    ]);
                }
            }
            echo "Finished\n";
            $playerModel->setTable(BasicFunctions::getWorldDataTable($worldModel, "$tablePre$tableSuffix"));
        }
    }
    
    private function doAlly(World $worldModel, $tableSuffix) {
        $tablePre = "ally_";
        $allyModel = new Ally($worldModel, "$tablePre$tableSuffix");
        echo "Run with $server$world ally_$tableSuffix\n";
        
        while(($todo = $allyModel->whereNull('offBash')
                ->orWhereNull('defBash')
                ->orWhereNull('gesBash')
                ->first()) !== null) {
            echo "Doing: " . $world->serName() . ".$tablePre$tableSuffix {$todo->created_at->timestamp} ";
            
            $similar = $this->getEntriesFromSameTime($worldModel, $tablePre, $tableSuffix, $allyModel, $todo, $worldModel->hash_ally);
            echo $similar->count() . " ";
            foreach($similar as $entry) {
                if($entry->offBash === null) $entry->offBash = 0;
                if($entry->defBash === null) $entry->defBash = 0;
                if($entry->gesBash === null) $entry->gesBash = 0;
                
                if(count($entry->getDirty()) > 0) {
                    DB::update("UPDATE ".$entry->getTable()." SET gesBash=?, defBash=?, offBash=?"
                            . " WHERE allyID=? AND created_at=?;", [
                        $entry->gesBash, $entry->defBash, $entry->offBash,
                        $entry->allyID, $entry->created_at
                    ]);
                }
            }
            echo "Finished\n";
            $allyModel->setTable(BasicFunctions::getWorldDataTable($worldModel, "$tablePre$tableSuffix"));
        }
    }
    
    private function getEntriesFromSameTime(World $worldModel, $tablePre, $tableSuffix, $model, $similarOf, $amount) {
        $timeStart = $similarOf->created_at->copy()->subSeconds(10);
        $timeEnd = $similarOf->created_at->copy()->addSeconds(10);
        if($tableSuffix == 'latest') {
            return $model->where('created_at', '>', $timeStart)
                    ->where('created_at', '<', $timeEnd)->get();
        }
        
        $data = collect();
        for($i = 0; $i < $amount; $i++) {
            if(!BasicFunctions::hasWorldDataTable($worldModel, $tablePre . $i)) {
                continue;
            }
            $model->setTable(BasicFunctions::getWorldDataTable($worldModel, "$tablePre$i"));
            $res = $model->where('created_at', '>', $timeStart)
                    ->where('created_at', '<', $timeEnd)->get();
            $data = $data->merge($res);
        }
        return $data;
    }
}
