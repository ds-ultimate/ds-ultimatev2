<?php

namespace App\Console\Commands;

use App\Util\BasicFunctions;
use Illuminate\Console\Command;

class UpdateVillage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:village {server=null} {world=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aktualisiert die Dörfer in der Datenbank';

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
        UpdateVillage::updateVillage($this->argument('server'), $this->argument('world'), $this->output);
    }
    
    public static function updateVillage($server, $world, $output) {
        \App\Util\BasicFunctions::ignoreErrs();
        $db = new \App\Http\Controllers\DBController();
        
        if ($server != null && $world != null && $server != "null" && $world != "null") {
            $db->latestVillages($server, $world);
        } else {
            $worlds = BasicFunctions::getWorld();
            
            $bar = $output->createProgressBar(count($worlds));
            $bar->start();
            
            foreach ($worlds as $world){
                $db->latestVillages($world->server->code, $world->name);
                $bar->advance();
            }
            $bar->finish();
        }
    }
}
