<?php

namespace App\Console\Commands;

use App\World;
use App\Util\BasicFunctions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class DeleteTable extends Command
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
        foreach ($worlds as $world){
        }
    }
}
