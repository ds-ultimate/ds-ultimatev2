<?php

namespace App\Console\Commands;

use App\World;
use App\Console\DatabaseUpdate\WorldHistory;
use Illuminate\Console\Command;

class UpdateWorldHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:worldHistory {server=null} {world=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schreibt die aktuellen Infos einer Welt in die Welten History';

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
        ini_set('max_execution_time', 1800);
        ini_set('memory_limit', '2000M');
        
        $server = $this->argument('server');
        $world = $this->argument('world');
        
        if($server == null || $world == null || $server == "null" || $world == "null" || ($server == "*" && $world == "*")) {
            foreach((new World())->where("active", 1)->get() as $dbWorld) {
                if($dbWorld->isSpeed()) {
                    continue;
                }
                $server = $dbWorld->server->code;
                $world = $dbWorld->name;
                WorldHistory::run($server, $world, $dbWorld->isSpeed());
            }
        } else {
            WorldHistory::run($server, $world, World::isSpeedName($world));
        }
        return 0;
    }
}
