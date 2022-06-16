<?php

namespace App\Console\Commands;

use App\World;
use App\Console\DatabaseUpdate\DoStatistic;
use App\Util\BasicFunctions;
use Illuminate\Console\Command;

class UpdateStatistic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:statistic {server=null} {world=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aktualisiert die Welt Statistik';

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
     * @return int
     */
    public function handle()
    {
        $server = $this->argument('server');
        $world = $this->argument('world');

        if ($server != null && $world != null && $server != "null" && $world != "null") {
            $world = World::getWorld($this->argument('server'), $this->argument('world'));
            DoStatistic::run($world);
        }else{
            $worlds = BasicFunctions::getWorldQuery()->get();
            foreach ($worlds as $world){
                DoStatistic::run($world);
            }
        }
        return 0;
    }
}
