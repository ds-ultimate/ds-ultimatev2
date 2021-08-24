<?php

namespace App\Console\Commands;

use App\World;
use App\Console\DatabaseUpdate\DoGenerateTops;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateGenerateTops extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:generateTops {server=null} {world=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aktualisiert die Top Spieler / Stämme einträge einer Welt';

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
        static::updateGenerateTops($this->argument('server'), $this->argument('world'), $this->output);
        return 0;
    }
    
    public static function updateGenerateTops($server, $world, $output) {
        $progress = true;
        if($server == "no-progress") {
            $progress = false;
            $server = null;
        }
        
        if ($server != null && $world != null && $server != "null" && $world != "null") {
            $now = Carbon::now()->subMinutes(5);
            $worldMod = World::getWorld($server, $world);
            DoGenerateTops::run($worldMod, 'p', $progress);
            DoGenerateTops::run($worldMod, 'a', $progress);
            $worldMod->worldTop_at = $now;
            $worldMod->save();
        } else {
            $worlds = (new World())->whereColumn("worldTop_at", "<", "worldUpdated_at")->orWhereNull('worldTop_at')->get();
            
            foreach ($worlds as $world){
                $now = Carbon::now()->subMinutes(5);
                DoGenerateTops::run($world, 'p', $progress);
                DoGenerateTops::run($world, 'a', $progress);
                $world->worldTop_at = $now;
                $world->save();
            }
        }
    }
}
