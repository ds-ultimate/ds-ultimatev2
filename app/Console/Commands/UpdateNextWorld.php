<?php

namespace App\Console\Commands;

use App\Http\Controllers\DBController;
use Illuminate\Console\Command;

class UpdateNextWorld extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:nextWorld';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aktualisiert die Welten in der Datenbank';

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
        foreach (BasicFunctions::getWorld() as $world) {
        }
        \App\Util\BasicFunctions::ignoreErrs();
        $world = new DBController();
        $world->getWorld();
    }
}
