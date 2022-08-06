<?php

namespace App\Console\Commands\MigrationHelper;

use App\World;
use App\WorldDatabase;
use App\Server;
use App\Util\BasicFunctions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SpeedToSharedDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:speedToSharedDB {--dryRun}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatisiert speed Welten anpassen';

    private static $WORLDS_PER_SHARED_SPEED = 50;
    private static $SHARED_DB_NAME = "sharedS";
    
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
        $dryRun = $this->option("dryRun");
        $speedWorlds = [];
        foreach((new World())->where('active', NULL)->orWhere('active', 0)->get() as $w) {
            if(!$w->isSpeed()) {
                continue;
            }
            if($w->database_id != null) {
                continue;
            }
            
            if(!isset($speedWorlds[$w->server->code])) {
                $speedWorlds[$w->server->code] = [];
            }
            $speedWorlds[$w->server->code][] = $w;
        }
        
        foreach($speedWorlds as $server => $worlds) {
            foreach($worlds as $w) {
                $sharedDB = static::findNextFreeSharedDB($w->server);
                echo "Moving " . $w->serName() . " to " . $sharedDB->name . " ";
                if(!$dryRun) {
                    ToSharedDB::moveToSharedDB($w, $sharedDB);
                }
            }
        }
        
        return 0;
    }
    
    private static function findNextFreeSharedDB(Server $s) {
        $i = 0;
        while($i < 100) {
            $dbName = $s->code . static::$SHARED_DB_NAME . $i;
            $sharedDB = (new WorldDatabase())->where("name", $dbName)->first();
            if($sharedDB == null) {
                $sharedDB = new WorldDatabase();
                $sharedDB->name = $dbName;
                $sharedDB->save();
                return $sharedDB;
            }
            
            $worldCnt = (new World())->where("database_id", $sharedDB->id)->count();
            if($worldCnt < static::$WORLDS_PER_SHARED_SPEED) {
                return $sharedDB;
            }
            $i++;
        }
        echo "More than 100 Shared Databases are full. That's a lot of speed servers";
        return null;
    }
}
