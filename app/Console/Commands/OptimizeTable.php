<?php

namespace App\Console\Commands;

use App\HistoryIndex;
use App\World;
use App\Util\BasicFunctions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class OptimizeTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimize:table {targets=history,ally,player,village}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimiert die angegeben targets oder alles wenn ohne Argumente';

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
        
        echo "Reading worlds\n";
        $bar = $this->output->createProgressBar(count($worlds));
        $bar->start();
        $targets = explode(",", $this->argument('targets'));

        $tables = [];
        foreach ($worlds as $world){
            foreach($targets as $target) {
                $tables = array_merge($tables, static::getTablesForTarget($world, $target));
            }
            $bar->advance();
        }
        $bar->finish();
        
        echo "\nOptimizing\n";
        $bar = $this->output->createProgressBar(count($tables));
        $bar->start();
        
        foreach($tables as $table) {
        DB::statement("OPTIMIZE TABLE $table");
            $bar->advance();
        }
        $bar->finish();
        echo "\n";
        return 0;
    }
    
    static private function getTablesForTarget(World $world, $target) {
        $tables = [];
        
        switch($target) {
            case "history":
                $tables[] = BasicFunctions::getWorldDataTable($world, "index");
                break;
            
            case "ally":
                for($num = 0; $num < config('dsUltimate.hash_ally'); $num++) {
                    $tables[] = BasicFunctions::getWorldDataTable($world, "ally_{$num}");
                }
                break;
            
            case "player":
                for($num = 0; $num < config('dsUltimate.hash_player'); $num++) {
                    $tables[] = BasicFunctions::getWorldDataTable($world, "player_{$num}");
                }
                break;
            
            case "village":
                for ($num = 0; $num < config('dsUltimate.hash_village'); $num++){
                    $tables[] = BasicFunctions::getWorldDataTable($world, "village_{$num}");
                }
                break;
            
            default:
                echo "Unknown Target $target";
        }
        
        return $tables;
    }
}
