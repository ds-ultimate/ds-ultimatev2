<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateWorldData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:worldData {server} {world}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aktualisiert alle Daten (ohne adelungen) einer Welt in der Datenbank';

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
        UpdateWorldData::updateWorldData($this->argument('server'), $this->argument('world'), $this->output);
    }
    
    public function updateWorldData($server, $world, $output) {
        \App\Util\BasicFunctions::ignoreErrs();
        
        if ($server != null && $world != null && $server != "null" && $world != "null") {
            UpdateVillage::updateVillage($server, $world, $output);
            UpdatePlayer::updatePlayer($server, $world, $output);
            UpdateAlly::updateAlly($server, $world, $output);
        }
    }
}
