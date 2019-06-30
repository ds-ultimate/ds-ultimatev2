<?php

namespace App\Console\Commands;

use App\Util\BasicFunctions;
use App\Village;
use App\World;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateVillage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:village';

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
        $worlds = BasicFunctions::getWorld();

        $bar = $this->output->createProgressBar(count($worlds));
        $bar->start();

        $db = new \App\Http\Controllers\DBController();
        foreach ($worlds as $world){
            $db->latestVillages($world->name);
            $bar->advance();
        }
        $bar->finish();
        $this->error('Something went wrong!');
    }
}
