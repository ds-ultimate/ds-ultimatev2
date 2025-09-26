<?php

namespace App\Console\Commands\MigrationHelper;

use App\Conquer;
use App\World;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InsertMissingConquerPoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:insertMissingConquerPoints {world*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Füllt conquers mit punkte = 0';

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
        ini_set('memory_limit', '1800M');
        $worlds = $this->argument('world');
        foreach($worlds as $w) {
            if($w == "*") {
                $world = (new World())->get();
                foreach ($world as $wInner){
                    $this->insertMissing($wInner);
                }
            } else if($w == "active") {
                $world = BasicFunctions::getWorldQuery()->get();
                foreach ($world as $wInner){
                    $this->insertMissing($wInner);
                }
            } else {
                $world = World::getWorld(substr($w, 0, 2), substr($w, 2));
                if($world != null) {
                    $this->insertMissing($world);
                } else {
                    echo "World $w not found\n";
                }
            }
        }
        
        return 0;
    }

    private function insertMissing(World $worldModel) {
        echo "Doing {$worldModel->serName()}\n";
        if($worldModel->village_hisory_on_disk) {
            throw new \RuntimeException("Village history on disk not supported");
        }

        $conquerModel = new Conquer($worldModel);
        $todo = $conquerModel->where('points', -1)->get();
        $curConq = 0;
        $allConq = count($todo);
        foreach($todo as $conq) {
            $cHist = $this->loadVillageHistory($worldModel, $conq->village_id);
            if(count($cHist) == 0) {
                $conq->points = 0;
                $conq->save();
                continue;
            }
            $tim = Carbon::createFromTimestamp($conq->timestamp);
            $bestMatch = $cHist[0];
            for($i = 1; $i < count($cHist); $i++) {
                if($bestMatch['t']->gt($tim) && $cHist[$i]['t']->lt($bestMatch['t'])) {
                    $bestMatch = $cHist[$i];
                } else if($cHist[$i]['t']->lt($tim) && $bestMatch['t']->lt($cHist[$i]['t'])) {
                    $bestMatch = $cHist[$i];
                }
            }
            $conq->points = $bestMatch['p'];
            $conq->save();
            $curConq++;
            
            if($curConq % 10 == 0) {
                echo "\r" . $worldModel->serName() . " at: $curConq / $allConq      ";
            }
        }
        echo "\n";
    }
    
    private function loadVillageHistory(World $worldModel, $villID) {
        $tbl = $villID % $worldModel->hash_village;
        $hist = [];
        if(! BasicFunctions::hasWorldDataTable($worldModel, "village_$tbl")) {
            return $hist;
        }
        
        $data = DB::select("SELECT villageID, points, updated_at FROM " . BasicFunctions::getWorldDataTable($worldModel, "village_$tbl") .
                " WHERE villageID='". (intval($villID)) ."'");
        foreach($data as $d) {
            $hist[] = [
                "p" => $d->points,
                "t" => Carbon::parse($d->updated_at),
            ];
        }
        return $hist;
    }
}
