<?php

namespace App\Console\Commands\MigrationHelper;

use App\World;
use App\Console\DatabaseUpdate\DoGenerateOtherWorlds;
use Illuminate\Console\Command;

class GenerateOtherWorlds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:generateOtherWorlds {server=null} {world=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Befüllt die andere Welten tabelle';

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
        static::generateOtherWorlds($this->argument('server'), $this->argument('world'), $this->output);
        return 0;
    }
    
    public static function generateOtherWorlds($server, $world, $output) {
        if ($server != null && $world != null && $server != "null" && $world != "null") {
            $worldMod = World::getWorld($server, $world);
            DoGenerateOtherWorlds::run([$worldMod], true);
        } else {
            $worlds = (new World())->get();
            DoGenerateOtherWorlds::run($worlds, true);
        }
    }
}
