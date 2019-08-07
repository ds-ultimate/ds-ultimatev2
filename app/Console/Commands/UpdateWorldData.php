<?php

namespace App\Console\Commands;

use App\Http\Controllers\DBController;
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
                foreach(\App\Util\BasicFunctions::getWorld() as $dbWorld) {
                    $server = $dbWorld->server->code;
                    $world = $dbWorld->name;
                    foreach(explode(",", $this->argument('part')) as $part) {
                        UpdateWorldData::updateWorldData($server, $world, $part);
                    }
                }
            } else {
                foreach(explode(",", $this->argument('part')) as $part) {
                    UpdateWorldData::updateWorldData($server, $world, $part);
                }
            }
        }
    }
    
    public static function updateWorldData($server, $world, $part) {
        $db = new DBController();
        switch ($part) {
            case "village":
            case "v":
                $db->latestVillages($server, $world);
                break;

            case "player":
            case "p":
                $db->latestPlayer($server, $world);
                break;

            case "ally":
            case "a":
                $db->latestAlly($server, $world);
                break;

            case "conquer":
            case "c":
                $db->conquer($server, $world);
                break;
        }
    }
}
