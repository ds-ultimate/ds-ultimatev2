<?php

namespace App\Console\Commands;

use App\Util\BasicFunctions;
use App\Village;
use App\World;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateAlly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:ally';

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
        $world = new World();
        $world->setTable(env('DB_DATABASE_MAIN').'.worlds');
        $worlds = $world->get();

        $bar = $this->output->createProgressBar(count($worlds));
        $bar->start();

        $db = new \App\Http\Controllers\DBController();
        foreach ($worlds as $world){
            $db->latestAlly($world->name);
            $bar->advance();
        }
        $bar->finish();
        $this->error('Something went wrong!');
    }
}
