<?php

namespace App\Console\Commands;

use App\World;
use App\Console\DatabaseUpdate\DoClean;
use Illuminate\Console\Command;

class UpdateCleanWorld extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:cleanWorld {server} {world}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Entfernt alte einträge von der gegebenen Welt';

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
        
        $worldMod = World::getWorld($server, $world);
        if($worldMod == null) {
            return -1;
        }

        //DoClean::run($world, 'v');
        DoClean::run($worldMod, 'p');
        DoClean::run($worldMod, 'a');
        return 0;
    }
}
