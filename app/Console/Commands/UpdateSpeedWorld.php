<?php

namespace App\Console\Commands;

use App\Console\DatabaseUpdate\DoSpeedWorld;
use Illuminate\Console\Command;

class UpdateSpeedWorld extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:speedWorld';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aktualisiert die speed Welten in der Datenbank';

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
        DoSpeedWorld::run();
        return 0;
    }
}
