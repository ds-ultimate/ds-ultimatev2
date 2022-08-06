<?php

namespace App\Console\Commands\MigrationHelper;

use App\World;
use App\WorldDatabase;
use App\Util\BasicFunctions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ToSharedDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:toSharedDB {--database=} {--world=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Spaart datenbanken durch gemeinsam genutzte Datenbanken';
    
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
        $worlds = $this->option('world');
        $dbName = $this->option('database');
        
        $sharedDB = null;
        if($dbName != null) {
            $sharedDB = (new WorldDatabase())->where("name", $dbName)->first();
            if($sharedDB == null) {
                $sharedDB = new WorldDatabase();
                $sharedDB->name = $dbName;
                $sharedDB->save();
            }
        }
        
        foreach($worlds as $w) {
            $world = World::getWorld(substr($w, 0, 2), substr($w, 2));
            if($world != null) {
                if($world->active === null || $world->active == 0) {
                    static::moveToSharedDB($world, $sharedDB);
                } else {
                    echo "$w: This tool can only be used on worlds were the updates have been disabled\n";
                }
            } else {
                echo "World $w not found\n";
            }
        }
        
        return 0;
    }
    
    public static function moveToSharedDB(World $worldModel, ?WorldDatabase $sharedDB) {
        echo "Doing {$worldModel->serName()}\n";
        
        $worldModel->maintananceMode = true;
        $worldModel->save();
        
        $tablesToMove = static::generateTablesToMove($worldModel);
        $oldSharedId = $worldModel->database_id;
        $newSharedId = ($sharedDB == null)?(null):($sharedDB->id);
        
        if($oldSharedId != null && $newSharedId == null) {
            //create needed
            $worldModel->database_id = $newSharedId;
            $dbRaw = BasicFunctions::getWorldDataDatabase($worldModel);
            $worldModel->database_id = $oldSharedId;
            DB::statement('CREATE DATABASE ' . $dbRaw);
        }
        
        foreach($tablesToMove as $tbl) {
            if(!BasicFunctions::hasWorldDataTable($worldModel, $tbl)) {
                continue;
            }
            $tblOld = BasicFunctions::getWorldDataTable($worldModel, $tbl);
            
            //Fake worldModel DatabaseId to trick BasicFunctions
            $worldModel->database_id = $newSharedId;
            $tblNew = BasicFunctions::getWorldDataTable($worldModel, $tbl);
            $worldModel->database_id = $oldSharedId;
            DB::statement("ALTER TABLE $tblOld RENAME TO $tblNew");
        }
        
        if($oldSharedId == null && $newSharedId != null) {
            //delete needed
            $dbRaw = BasicFunctions::getWorldDataDatabase($worldModel);
            DB::statement('DROP DATABASE ' . $dbRaw);
        }
        
        $worldModel->database_id = $newSharedId;
        $worldModel->maintananceMode = false;
        $worldModel->save();
    }
    
    private static function generateTablesToMove(World $worldModel) {
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
