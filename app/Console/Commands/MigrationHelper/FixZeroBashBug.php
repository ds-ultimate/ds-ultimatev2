<?php

namespace App\Console\Commands\MigrationHelper;

use App\Player;
use App\Util\BasicFunctions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixZeroBashBug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:zeroBashBug {server=null} {world=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes the bug with gesBash=0 after the april update';

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
        $server = $this->argument('server');
        $world = $this->argument('world');
        
        if ($server != null && $world != null && $server != "null" && $world != "null") {
            if($server == "*" && $world == "*") {
                foreach(BasicFunctions::getWorldQuery()->get() as $dbWorld) {
                    static::runFix($dbWorld);
                }
            } else {
                static::runFix(\App\World::getWorld($server, $world));
            }
        }
        return 0;
    }
    
    private static function runFix($world) {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '600M');
        
        static::fix(BasicFunctions::getWorldDataTable($world, 'player_latest'));
        for ($i = 0; $i < config('dsUltimate.hash_player'); $i++){
            if (BasicFunctions::hasWorldDataTable($world, "player_$i")) {
                static::fix(BasicFunctions::getWorldDataTable($world, "player_$i"));
            }
        }
    }
    
    private static function fix($tblName) {
        echo "Fixing $tblName\n";
        
        $playerModel = new Player();
        $playerModel->setTable($tblName);
        foreach($playerModel->get() as $play) {
            $realGes = $play->offBash + $play->defBash + $play->supBash;
            if($play->gesBash == 0 && $realGes > 0) {
                DB::update("UPDATE ".$play->getTable()." SET gesBash=? WHERE playerID=? AND created_at=?;", [
                    $realGes, $play->playerID, $play->created_at
                ]);
            }
        }
    }
}
