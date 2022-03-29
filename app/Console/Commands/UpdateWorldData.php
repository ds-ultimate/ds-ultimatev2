<?php

namespace App\Console\Commands;

use App\Util\BasicFunctions;
use App\Console\DatabaseUpdate\DoWorldData;
use Illuminate\Console\Command;

class UpdateWorldData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:worldData {server=null} {world=null} {part=village,player,ally}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aktualisiert die Daten (ohne adelungen) einer Welt in der Datenbank.';

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
                    DoWorldData::run($dbWorld, $this->argument('part'));
                }
            } else {
                DoWorldData::run(\App\World::getWorld($server, $world), $this->argument('part'));
            }
        }
        return 0;
    }
}
