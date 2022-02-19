<?php

namespace App\Console\Commands\MigrationHelper;

use App\Server;
use App\World;
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
            $wData.= "|" . $world->active;
            $wData.= "|" . $world->worldCheck_at;
            $wData.= "|" . $world->worldCleaned_at;
            $wData.= "|" . $world->worldTop_at;
            $wData.= "|" . $world->display_name . "\n";
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
            $found[$world->server->code][] = $world->name;
        }
        
        foreach(file($fName) as $line) {
            $w = explode("|", trim($line));
            
            if(! isset($found[$w[0]]) ||
                    !in_array($w[1], $found[$w[0]])) {
                $this->insertWorld($w);
            }
        }
    }
    
    private function insertWorld($data) {
        $s = Server::getServerByCode($data[0]);
        if($s == null) {
            echo "Unable to find server {$data[0]}\n";
            return;
        }
        echo "Inserting {$data[0]} / {$data[1]} / {$s->id} \n";
        $world = new World();
        $world->server_id = $s->id;
        $world->name = $data[1];
        $world->ally_count = $data[2];
        $world->player_count = $data[3];
        $world->village_count = $data[4];
        $world->url = $data[5];
        $world->config = base64_decode($data[6]);
        $world->units = base64_decode($data[7]);
        $world->buildings = base64_decode($data[8]);
        if($data[9] != "") {
            $world->active = $data[9];
        }
        $world->worldCheck_at = $data[10];
        $world->worldCleaned_at = $data[11];
        if($data[12] != "") {
            $world->worldTop_at = $data[12];
        }
        $world->display_name = $data[13];
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
            $name = BasicFunctions::getDatabaseName($world->server->code, $world->name);
            if(!in_array($name, $all)) {
                echo "Deleting {$world->server->code} {$world->name}\n";
                $world->delete();
            }
        }
    }
}
