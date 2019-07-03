<?php

namespace App\Console\Commands;

use App\Util\BasicFunctions;
use Illuminate\Console\Command;

class UpdateConquer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:conquer {world=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aktualisiert die Adelungen in der Datenbank';

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
        $db = new \App\Http\Controllers\DBController();
        
        if ($this->argument('world') != "null") {
            $db->conquer($this->argument('world'));
        } else {
            $worlds = BasicFunctions::getWorld();
            
            $bar = $this->output->createProgressBar(count($worlds));
            $bar->start();
            
            foreach ($worlds as $world){
                $db->conquer($world->name);
                $bar->advance();
            }
            $bar->finish();
        }
    }
}
