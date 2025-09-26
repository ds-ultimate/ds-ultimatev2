<?php

namespace App\Console\Commands\MigrationHelper;

use App\World;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class ExportVillageHistoryToDisk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:saveVillageHistoryToDisk {--world=*} {--max-worlds=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export village history tables of a world into tar.gz archives';

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
        $maxWorlds = $this->option("max-worlds");

        if(count($worlds) == 1 && $worlds[0] == "*") {
            echo "Exporting village history of worlds\n";
            $world = (new World())
                    ->whereNull('active')
                    ->whereNotNull('world_finalized_at')
                    ->where('village_hisory_on_disk', false)
                    ->get();

            $worldsDone = 0;
            foreach ($world as $wInner) {
                $this->exportVillageHistory($wInner);
                $wInner->save();

                $worldsDone++;
                if($maxWorlds > 0 && $worldsDone > $maxWorlds) {
                    return 0;
                }
            }
            return 0;
        }

        foreach($worlds as $w) {
            $world = World::getWorld(substr($w, 0, 2), substr($w, 2));
            if($world != null) {
                if($world->active === null) {
                    $this->exportVillageHistory($world);
                } else {
                    echo "This tool can only be used on worlds were the updates have been disabled\n";
                }
            } else {
                echo "World $w not found\n";
            }
        }

        return 0;
    }

    private function exportVillageHistory(World $worldModel) {
        $this->info("Doing {$worldModel->serName()}");
        
        if($worldModel->world_finalized_at === null) {
            $this->warn("Cannot run on {$worldModel->serName()} this world has not been finalized yet");
            return;
        }
        if($worldModel->village_hisory_on_disk) {
            $this->warn("Cannot run on {$worldModel->serName()} this world is already stored on the disk");
            return;
        }

        $worldModel->maintananceMode = true;
        $worldModel->save();

        $this->exportVillageHash($worldModel);

        $worldModel->maintananceMode = false;
        $worldModel->village_hisory_on_disk = true;
        $worldModel->save();
    }

    private function exportVillageHash(World $worldModel) {
        $worldName = $worldModel->serName();
        $hashCount = $worldModel->hash_village; // number of village_* tables
        $basePath  = storage_path("app/village_history/{$worldName}");

        if (!is_dir($basePath)) {
            mkdir($basePath, 0755, true);
        }

        for ($num = 1; $num < $hashCount; $num++) {
            if (BasicFunctions::hasWorldDataTable($worldModel, "village_{$num}") === false) {
                continue;
            }
            $table = BasicFunctions::getWorldDataTable($worldModel, 'village_' . $num);

            // setup gz archive
            $gzpath = "{$basePath}/{$num}.csv.gz";
            if (file_exists($gzpath)) {
                unlink($gzpath);
            }

            $gzfile = gzopen($gzpath, "w");

            // export all entries of that table at once by villageID
            foreach (DB::table($table)->orderBy('villageID', 'asc')->orderBy('created_at', 'asc')->cursor() as $row) {
                $timestamp = strtotime($row->created_at);
                gzwrite($gzfile, "{$row->villageID};{$row->name};{$row->x};{$row->y};{$row->points};{$row->owner};{$row->bonus_id};{$timestamp}\n");
            }

            gzclose($gzfile);
            $this->info("Exported {$table} -> {$gzpath}");
        }

        // delete tables
        for($i = 0; $i < $hashCount; $i++) {
            if (BasicFunctions::hasWorldDataTable($worldModel, "village_$i") === false) {
                continue;
            }
            $tblTmp = BasicFunctions::getWorldDataTable($worldModel, "village_$i");
            Schema::dropIfExists($tblTmp);
        }

        $this->info("Export finished for world {$worldName}");
    }
}
