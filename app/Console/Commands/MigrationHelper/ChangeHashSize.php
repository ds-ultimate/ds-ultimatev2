<?php

namespace App\Console\Commands\MigrationHelper;

use App\World;
use App\Console\DatabaseUpdate\TableGenerator;
use App\Util\BasicFunctions;
use Carbon\Carbon;
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

        if(count($worlds) == 1 && $worlds[0] == "*" &&
                count($hash) == 1 && $hash[0] == "*") {
            echo "Perfoming automated finishing of worlds\n";
            $world = (new World())->where(function($query) {
                return $query->where('active', NULL)->orWhere('active', 0);
            })->where('world_finalized_at', NULL)->get();
            foreach ($world as $wInner) {
                $diff+= $this->changeHashes($wInner, ["a", "p", "v"]);
                $wInner->world_finalized_at = Carbon::now();
                $wInner->save();
            }
            return 0;
        }

        foreach($worlds as $w) {
            if($w == "*") {
                echo "This tool can only be used on worlds were the updates have been disabled. Running through inactive worlds\n";
                $world = (new World())->where('active', NULL)->orWhere('active', 0)->get();
                foreach ($world as $wInner) {
                    $diff+= $this->changeHashes($wInner, $hash);
                }
            } else {
                $world = World::getWorld(substr($w, 0, 2), substr($w, 2));
                if($world != null) {
                    if($world->active === null || $world->active == 0) {
                        $diff+= $this->changeHashes($world, $hash);
                    } else {
                        echo "This tool can only be used on worlds were the updates have been disabled\n";
                    }
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
        $worldModel->world_finalized_at = null;
        $worldModel->save();
        return $diff;
    }

    private function doChangeHash(World $worldModel, $tblPrefix, $oldHash, $newHash, $idCol, Callable $createCallback) {
        echo "Doing {$worldModel->serName()} $idCol";
        //Step 1 determine new hash size
        $dataSize = 0;
        for($i = 0; $i < $oldHash; $i++) {
            if (BasicFunctions::hasWorldDataTable($worldModel, "$tblPrefix$i") === false) {
                continue;
            }
            $data = DB::select("SELECT COUNT(*) AS c FROM " . BasicFunctions::getWorldDataTable($worldModel, "$tblPrefix$i"));
            $dataSize += $data[0]->c;
            echo ".";
        }

        if($newHash <= 0) {
            $newHash = intval($dataSize / static::$TARGET_TABLE_SIZE) + 1;
        }
        if($newHash == $oldHash) {
            echo "\n";
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
        for($i = 0; $i < $newHash; $i++) {
            $createCallback($worldModel, $i);
        }

        //Step 4 Insert data
        echo "!";
        for($i = 0; $i < $oldHash; $i++) {
            if (BasicFunctions::hasWorldDataTable($worldModel, "{$tblPrefix}old_$i") === false) {
                continue;
            }

            $allEntries = [];
            $flushCount = 0;
            for($j = 0; $j < $newHash; $j++) {
                $allEntries[$j] = [];
            }

            foreach (DB::table(BasicFunctions::getWorldDataTable($worldModel, "{$tblPrefix}old_$i"))->cursor() as $entry) {
                $newHashIndex = $entry->$idCol % $newHash;
                $allEntries[$newHashIndex][] = (array) $entry;

                // Flush inserts in intervals
                if (count($allEntries[$newHashIndex]) >= 3000) {
                    $tbl = BasicFunctions::getWorldDataTable($worldModel, "{$tblPrefix}" . ($newHashIndex));
                    DB::table($tbl)->insert($allEntries[$newHashIndex]);
                    $allEntries[$newHashIndex] = []; // reset bucket

                    $flushCount++;
                    if($flushCount >= 10) {
                        echo ".";
                        $flushCount = 0;
                    }
                }
            }
            foreach ($allEntries as $j => $bucket) {
                if (!empty($bucket)) {
                    $tbl = BasicFunctions::getWorldDataTable($worldModel, "{$tblPrefix}{$j}");
                    DB::table($tbl)->insert($bucket);
                }
            }
            echo ".";

        }

        //Step 5 delete old tables
        for($i = 0; $i < $oldHash; $i++) {
            if (BasicFunctions::hasWorldDataTable($worldModel, "{$tblPrefix}old_$i") === false) {
                continue;
            }
            $tblTmp = BasicFunctions::getWorldDataTable($worldModel, "{$tblPrefix}old_$i");
            Schema::dropIfExists($tblTmp);
        }
        echo "\n";
        return $newHash;
    }
}
