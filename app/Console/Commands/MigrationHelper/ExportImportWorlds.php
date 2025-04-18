<?php

namespace App\Console\Commands\MigrationHelper;

use App\Server;
use App\World;
use App\WorldDatabase;
use App\Util\BasicFunctions;
use Illuminate\Console\Command;

class ExportImportWorlds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:worldExp {type} {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exports or imports worlds (only the descriptions)';

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
        switch ($this->argument('type')) {
            case 'e':
                $this->export($this->argument('file'));
                break;
            case 'i':
                $this->import($this->argument('file'));
                break;
            case 'r':
                $this->autoremove();
        }
        return 0;
    }
    
    private function export($fName) {
        $file = fopen($fName, "w");
        $worlds = (new World())->get();
        foreach ($worlds as $world){
            if($world->deleted_at != null) {
                continue;
            }
            $wData = $world->server->code;
            $wData.= "|" . $world->name;
            $wData.= "|" . $world->ally_count;
            $wData.= "|" . $world->player_count;
            $wData.= "|" . $world->village_count;
            $wData.= "|" . $world->url;
            $wData.= "|" . base64_encode($world->config);
            $wData.= "|" . base64_encode($world->units);
            $wData.= "|" . base64_encode($world->buildings);
            $wData.= "|" . $world->worldCheck_at;
            $wData.= "|" . $world->worldCleaned_at;
            $wData.= "|" . $world->worldTop_at;
            $wData.= "|" . base64_encode($world->display_name);
            $wData.= "|" . $world->hash_ally;
            $wData.= "|" . $world->hash_player;
            $wData.= "|" . $world->hash_village;
            $wData.= "|" . ($world->active ?? "null");
            if($world->database_id != null) {
                $wData.= "|" . $world->database->name;
            } else {
                $wData.= "|" . "-";
            }
            $wData.= "\n";
            fwrite($file, $wData);
        }
        fclose($file);
    }
    
    private function import($fName) {
        $worlds = (new World())->get();
        $found = [];
        foreach ($worlds as $world){
            if($world->deleted_at != null) {
                continue;
            }
            if(! isset($found[$world->server->code])) {
                $found[$world->server->code] = [];
            }
            $found[$world->server->code][$world->name] = $world;
        }
        
        foreach(file($fName) as $line) {
            $w = explode("|", trim($line));
            
            if(isset($found[$w[0]]) && isset($found[$w[0]][$w[1]])) {
                $model = $found[$w[0]][$w[1]];
            } else {
                $model = new World();
            }
            
            $this->insertWorld($w, $model);
        }
    }
    
    private function insertWorld($data, World $world) {
        $s = Server::getServerByCode($data[0]);
        if($s == null) {
            echo "Unable to find server {$data[0]}\n";
            return;
        }
        echo "Inserting {$data[0]} / {$data[1]} / {$s->id} \n";
        $world->server_id = $s->id;
        $world->name = $data[1];
        $world->ally_count = ($data[2] !== "")?$data[2]:null;
        $world->player_count = ($data[3] !== "")?$data[3]:null;
        $world->village_count = ($data[4] !== "")?$data[4]:null;
        $world->url = $data[5];
        $world->config = base64_decode($data[6]);
        $world->units = base64_decode($data[7]);
        $world->buildings = base64_decode($data[8]);
        $world->active = ($data[16] == "null")?null:0;
        $world->worldCheck_at = $data[9];
        $world->worldCleaned_at = $data[10];
        if($data[11] != "") {
//            $world->worldTop_at = $data[11];
        }
        $world->display_name = base64_decode($data[12]);
        $world->hash_ally = $data[13];
        $world->hash_player = $data[14];
        $world->hash_village = $data[15];
        
        if($world->display_name == "") {
            $world->display_name = null;
        }
        
        if($data[17] == "-") {
            $world->database_id = null;
        } else {
            $sharedDB = (new WorldDatabase())->where("name", $data[17])->first();
            if($sharedDB == null) {
                $sharedDB = new WorldDatabase();
                $sharedDB->name = $data[17];
                $sharedDB->save();
            }
            $world->database_id = $sharedDB->id;
        }
        
        $world->save();
    }
    
    private function autoremove() {
        $data = \DB::select('SHOW DATABASES');
        $all = [];
        foreach($data as $d) {
            $all[] = $d->Database;
        }
        
        $worlds = (new World())->get();
        foreach ($worlds as $world){
            $name = BasicFunctions::getWorldDataTable($world, "");
            $name = substr($name, 0, strlen($name) - 1);
            if(!in_array($name, $all)) {
                echo "Deleting " . $world->serName() . "\n";
                $world->delete();
            }
        }
    }
}
