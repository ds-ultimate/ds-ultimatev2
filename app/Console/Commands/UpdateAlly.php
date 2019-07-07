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
        \App\Util\BasicFunctions::ignoreErrs();
        $db = new \App\Http\Controllers\DBController();
        
        if ($this->argument('world') != "null") {
            $db->latestAlly($this->argument('server'), $this->argument('world'));
        } else {
            $worlds = BasicFunctions::getWorld();
            
            $bar = $this->output->createProgressBar(count($worlds));
            $bar->start();
            
            foreach ($worlds as $world){
                $db->latestAlly($world->server->code, $world->name);
                $bar->advance();
            }
            $bar->finish();
        }
    }
}
