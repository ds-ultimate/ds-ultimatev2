<?php

namespace App\Console\Commands;

use App\Console\DatabaseUpdate\DoConquer;
use App\Util\BasicFunctions;
use Illuminate\Console\Command;

class UpdateConquer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:conquer {server=null} {world=null}';

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
        static::updateConquer($this->argument('server'), $this->argument('world'), $this->output);
        return 0;
    }
    
    public static function updateConquer($server, $world, $output) {
        BasicFunctions::ignoreErrs();
        $progress = true;
        if($server == "no-progress") {
            $progress = false;
            $server = null;
        }
        
        if ($server != null && $world != null && $server != "null" && $world != "null") {
            DoConquer::run($server, $world);
        } else {
            $worlds = BasicFunctions::getWorldQuery()->get();
            
            if($progress) {
                $bar = $output->createProgressBar(count($worlds));
                $bar->start();
            }
            
            foreach ($worlds as $world){
                DoConquer::run($world->server->code, $world->name);
                if($progress) {
                    $bar->advance();
                }
            }
            if($progress) {
                $bar->finish();
            }
        }
    }
}
