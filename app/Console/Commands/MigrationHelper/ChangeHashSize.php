<?php

namespace App\Console\Commands\MigrationHelper;

use App\World;
use App\Console\DatabaseUpdate\TableGenerator;
use App\Util\BasicFunctions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeHashSize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:changeHashSize {--hash=*} {--world=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ändert die HashSize auf den definierten Wert';

    private static $TARGET_TABLE_SIZE = 150000;
    
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
        $hash = $this->option('hash');
        $diff = 0;
        foreach($worlds as $w) {
            if($w == "*") {
                $world = (new World())->get();
                foreach ($world as $wInner){
                    $diff+= $this->changeHashes($wInner, $hash);
                }
            } else if($w == "inactive") {
                $world = (new World())->where('active', 0)->get();
                foreach ($world as $wInner){
                    $diff+= $this->changeHashes($wInner, $hash);
                }
            } else {
                $world = World::getWorld(substr($w, 0, 2), substr($w, 2));
                if($world != null) {
                    $diff+= $this->changeHashes($world, $hash);
                } else {
                    echo "World $w not found\n";
                }
            }
        }
        if($diff < 0) {
            $diff*= -1;
            echo "Added $diff Tables\n";
        } else {
            echo "Removed $diff Tables\n";
        }
        
        return 0;
    }
    
    private function changeHashes(World $worldModel, $hash) {
        $diff = 0;
        echo "Doing {$worldModel->serName()}\n";
        
        $worldModel->maintananceMode = true;
        $worldModel->save();
        foreach($hash as $h) {
            $expl = explode(":", $h);
            $newVal = $expl[1] ?? -1;
            switch ($expl[0]) {
                case "a":
                    $oldHash = $worldModel->hash_ally;
                    $newHash = $this->doChangeHash($worldModel, "ally_", $oldHash, $newVal, "allyID", [TableGenerator::class, "allyTable"]);
                    $worldModel->hash_ally = $newHash;
                    break;
                case "p":
                    $oldHash = $worldModel->hash_player;
                    $newHash = $this->doChangeHash($worldModel, "player_", $oldHash, $newVal, "playerID", [TableGenerator::class, "playerTable"]);
                    $worldModel->hash_player = $newHash;
                    break;
                case "v":
                    $oldHash = $worldModel->hash_village;
                    $newHash = $this->doChangeHash($worldModel, "village_", $oldHash, $newVal, "villageID", [TableGenerator::class, "villageTable"]);
                    $worldModel->hash_village = $newHash;
                    break;
            }
            $diff+= $oldHash - $newHash;
        }
        $worldModel->maintananceMode = false;
        $worldModel->save();
        return $diff;
    }
        
    private function doChangeHash(World $worldModel, $tblPrefix, $oldHash, $newHash, $idCol, Callable $createCallback) {
        //Step 1 load data into ram
        $allEntries = [];
        $dataSize = 0;
        for($i = 0; $i < $oldHash; $i++) {
            if (BasicFunctions::hasWorldDataTable($worldModel, "$tblPrefix$i") === false) {
                continue;
            }
            $data = DB::select("SELECT * FROM " . BasicFunctions::getWorldDataTable($worldModel, "$tblPrefix$i"));
            
            foreach($data as $r) {
                $allEntries[] = (array) $r;
                $dataSize++;
            }
        }
        
        //Step 1.1 determine new hash size
        if($newHash <= 0) {
            $newHash = intval($dataSize / static::$TARGET_TABLE_SIZE) + 1;
        }
        if($newHash == $oldHash) {
            return $oldHash;
        }
        
        //Step 2 move old tables out of the way
        for($i = 0; $i < $oldHash; $i++) {
            if (BasicFunctions::hasWorldDataTable($worldModel, "$tblPrefix$i") === false) {
                continue;
            }
            $tblTmp = BasicFunctions::getWorldDataTable($worldModel, "{$tblPrefix}old_$i");
            $tblReal = BasicFunctions::getWorldDataTable($worldModel, "$tblPrefix$i");
            DB::statement("ALTER TABLE $tblReal RENAME TO $tblTmp");
        }
        
        //Step 3 Create new tables
        $sorted = [];
        for($i = 0; $i < $newHash; $i++) {
            $createCallback($worldModel, $i);
            $sorted[$i] = [];
        }
        
        //Step 4 Insert data
        $keys = array_keys($allEntries);
        foreach($keys as $k) {
            $entry = $allEntries[$k];
            unset($allEntries[$k]);
            $sorted[$entry[$idCol] % $newHash][] = $entry;
        }
        for($i = 0; $i < $newHash; $i++) {
            if(count($sorted[$i]) <= 0) {
                continue;
            }
            
            $tbl = BasicFunctions::getWorldDataTable($worldModel, "$tblPrefix$i");
            foreach (array_chunk($sorted[$i], 3000) as $t) {
                DB::table($tbl)->insert($t);
            }
        }
        
        //Step 5 delete old tables
        for($i = 0; $i < $oldHash; $i++) {
            if (BasicFunctions::hasWorldDataTable($worldModel, "{$tblPrefix}old_$i") === false) {
                continue;
            }
            $tblTmp = BasicFunctions::getWorldDataTable($worldModel, "{$tblPrefix}old_$i");
            Schema::dropIfExists($tblTmp);
        }
        return $newHash;
    }
}
