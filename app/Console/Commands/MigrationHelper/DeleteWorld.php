<?php

namespace App\Console\Commands\MigrationHelper;

use App\World;
use App\Util\BasicFunctions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        $worldModel->delete();
    }
    
    private static function deleteWorldHistory(World $worldModel) {
        $tblName = BasicFunctions::getWorldDataTable($worldModel, "index");
        $data = DB::select("SELECT * FROM $tblName");
        $fromBase = storage_path(config('dsUltimate.history_directory') . $worldModel->serName());
        foreach($data as $d) {
            unlink($fromBase . "/village_{$d->date}.gz");
            unlink($fromBase . "/player_{$d->date}.gz");
            unlink($fromBase . "/ally_{$d->date}.gz");
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
        for($i = 0; $i < $worldModel->hash_village; $i++) {
            $result[] = "village_$i";
        }
        $result[] = "village_latest";
        return $result;
    }
}
