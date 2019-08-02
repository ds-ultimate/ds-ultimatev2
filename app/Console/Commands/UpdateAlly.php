<?php

namespace App\Console\Commands;

use App\Util\BasicFunctions;
use Illuminate\Console\Command;

class UpdateAlly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:ally {server=null} {world=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aktualisiert die Stämme in der Datenbank';

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
        UpdateAlly::updateAlly($this->argument('server'), $this->argument('world'), $this->output);
    }
    
    public static function updateAlly($server, $world, $output) {
        \App\Util\BasicFunctions::ignoreErrs();
        $db = new \App\Http\Controllers\DBController();
        
        if ($server != null && $world != null && $server != "null" && $world != "null") {
            $db->latestAlly($server, $world);
        } else {
            $worlds = BasicFunctions::getWorld();
            
            $bar = $output->createProgressBar(count($worlds));
            $bar->start();
            
            foreach ($worlds as $world){
                $db->latestAlly($world->server->code, $world->name);
                $bar->advance();
            }
            $bar->finish();
        }
    }
}
