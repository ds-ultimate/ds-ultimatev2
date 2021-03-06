<?php

namespace App\Console\Commands;

use App\Console\DatabaseUpdate\DoAlly;
use App\Console\DatabaseUpdate\DoConquer;
use App\Console\DatabaseUpdate\DoPlayer;
use App\Console\DatabaseUpdate\DoVillage;
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
        \App\Util\BasicFunctions::ignoreErrs();
        $server = $this->argument('server');
        $world = $this->argument('world');
        
        if ($server != null && $world != null && $server != "null" && $world != "null") {
            if($server == "*" && $world == "*") {
                foreach(\App\Util\BasicFunctions::getWorldQuery()->get() as $dbWorld) {
                    $server = $dbWorld->server->code;
                    $world = $dbWorld->name;
                    foreach(explode(",", $this->argument('part')) as $part) {
                        static::updateWorldData($server, $world, $part);
                    }
                }
            } else {
                foreach(explode(",", $this->argument('part')) as $part) {
                    static::updateWorldData($server, $world, $part);
                }
            }
        }
        return 0;
    }
    
    public static function updateWorldData($server, $world, $part) {
        switch ($part) {
            case "village":
            case "v":
                DoVillage::run($server, $world);
                break;

            case "player":
            case "p":
                DoPlayer::run($server, $world);
                break;

            case "ally":
            case "a":
                DoAlly::run($server, $world);
                break;

            case "conquer":
            case "c":
                DoConquer::run($server, $world);
                break;
        }
    }
}
