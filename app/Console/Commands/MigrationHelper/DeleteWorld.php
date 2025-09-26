<?php

namespace App\Console\Commands\MigrationHelper;

use App\DsConnection;
use App\PlayerOtherServers;
use App\Signature;
use App\SpeedWorld;
use App\World;
use App\WorldStatistic;
use App\Tool\AnimHistMap\AnimHistMapJob;
use App\Tool\AnimHistMap\AnimHistMapMap;
use App\Tool\AttackPlanner\AttackList;
use App\Tool\AttackPlanner\AttackListItem;
use App\Tool\Map\Map;
use App\Util\BasicFunctions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteWorld extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:deleteAllWorldData {world}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a specific world and all associated data';

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
        $worldStr = $this->argument('world');
        $worldModel = World::getAndCheckWorld(substr($worldStr, 0, 2), substr($worldStr, 2));
        if($worldModel->deleted_at != null) {
            return 1;
        }
        static::removeOtherworldReferences([$worldModel], true);
        static::deleteWorld($worldModel);
        return 0;
    }

    public static function deleteWorld(World $worldModel) {
        echo "Doing {$worldModel->serName()}\n";
        
        $worldModel->maintananceMode = true;
        $worldModel->save();
        static::deleteWorldHistory($worldModel);

        $tablesToMove = static::generateTablesToDelete($worldModel);

        foreach($tablesToMove as $tbl) {
            if(!BasicFunctions::hasWorldDataTable($worldModel, $tbl)) {
                continue;
            }
            $tblDel = BasicFunctions::getWorldDataTable($worldModel, $tbl);
            DB::statement("DROP TABLE $tblDel");
        }

        static::removeUserData($worldModel);
        static::removeSignatures($worldModel);
        static::removeSpeedWorldRef($worldModel);
        static::removeWorldStatistics($worldModel);

        if($worldModel->village_hisory_on_disk) {
            Storage::deleteDirectory('village_history/' . $worldModel->serName());
        }

        $worldModel->delete();
    }
    
    public static function removeOtherworldReferences($worlds, $progress=true){
        $servers = [];
        foreach($worlds as $model) {
            if(! isset($servers[$model->server->code])) {
                $servers[$model->server->code] = [
                    "m" => $model->server,
                    "w" => [],
                ];
            }

            $servers[$model->server->code]['w'][] = $model->id;
        }

        foreach($servers as $entry) {
            static::runRemoveOtherworldReferecnesInternally($entry['m'], $entry['w'], $progress);
        }
    }

    private static function runRemoveOtherworldReferecnesInternally($server, $worlds, $progress=true){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2000M');

        $i = 0;
        $toUpdate = [];
        foreach(PlayerOtherServers::prepareModel($server)->get() as $model) {
            $model->massRemoveWorlds($worlds);

            if($model->isDirty()) {
                $toUpdate[$model->playerID] = $model;
            }

            $i++;
            if($progress && $i % 100 == 0) {
                echo "\rReading player: $i             ";
            }
        }

        $i = 0;
        $cnt = count($toUpdate);
        foreach($toUpdate as $model) {
            $i++;
            if($progress && $i % 100 == 0) {
                echo "\rWriting player: $i / $cnt      ";
            }
            if($model->worlds == null) {
                $model->delete();
            } else {
                $model->save();
            }
        }

        if($progress) {
            echo "\n";
        }
    }

    private static function removeUserData(World $worldModel) {
        // -> AnimHistMapMap + rendered map (if rendered)
        foreach((new AnimHistMapJob())->where("world_id", $worldModel->id)->get() as $model) {
            if($model->finished_at != null) {
                $baseFName = storage_path(config('tools.animHistMap.renderDir') . "{$model->id}");
                $fName = $baseFName . "/render.mp4";
                if(is_file($fName)) {
                    unlink($fName);
                }
                $fName = $baseFName . "/src.zip";
                if(is_file($fName)) {
                    unlink($fName);
                }
                $fName = $baseFName . "/animated.gif";
                if(is_file($fName)) {
                    unlink($fName);
                }
                if(is_dir($baseFName)) {
                    rmdir($baseFName);
                }
            }
            $model->forceDelete();
        }
        // -> AnimHistMapJob
        (new AnimHistMapMap())->where("world_id", $worldModel->id)->forceDelete();
        // -> AttackLists -> AttackListItems
        foreach((new AttackList())->where("world_id", $worldModel->id)->get() as $m) {
            (new AttackListItem())->where('attack_list_id', $m->id)->forceDelete();
            $m->forceDelete();
        }
        // -> ds_connections
        (new DsConnection())->where("world_id", $worldModel->id)->forceDelete();
        // -> map
        foreach((new Map())->where("world_id", $worldModel->id)->get() as $model) {
            $fName = storage_path(config('tools.map.cacheDir') . $model->id);
            if(is_file($fName)) {
                unlink($fName);
            }
            $model->forceDelete();
        }
    }

    private static function removeSignatures(World $worldModel) {
        // -> signature
        foreach((new Signature())->where("world_id", $worldModel->id)->get() as $model) {
            $fName = $model->getCacheFile();
            if(is_file($fName)) {
                unlink($fName);
            }
            $model->forceDelete();
        }
    }

    private static function removeSpeedWorldRef(World $worldModel) {
        // -> speed_worlds
        (new SpeedWorld())->where("world_id", $worldModel->id)->forceDelete();
    }

    private static function removeWorldStatistics(World $worldModel) {
        // -> world_statistics
        (new WorldStatistic())->where("world_id", $worldModel->id)->delete();
    }

    private static function deleteWorldHistory(World $worldModel) {
        $fromBase = storage_path(config('dsUltimate.history_directory') . $worldModel->serName());
        if(!is_dir($fromBase)) {
            return;
        }
        $files = array_diff(scandir($fromBase), array('.','..'));
        foreach($files as $f) {
            unlink($fromBase . "/" . $f);
        }
        rmdir($fromBase);
    }
    
    private static function generateTablesToDelete(World $worldModel) {
        $result = [];
        for($i = 0; $i < $worldModel->hash_ally; $i++) {
            $result[] = "ally_$i";
        }
        $result[] = "ally_changes";
        $result[] = "ally_latest";
        $result[] = "ally_top";
        $result[] = "conquer";
        $result[] = "index";
        for($i = 0; $i < $worldModel->hash_player; $i++) {
            $result[] = "player_$i";
        }
        $result[] = "player_latest";
        $result[] = "player_top";

        if(! $worldModel->village_hisory_on_disk) {
            for($i = 0; $i < $worldModel->hash_village; $i++) {
                $result[] = "village_$i";
            }
        }
        $result[] = "village_latest";
        return $result;
    }
}
