<?php

namespace App\Console\Commands;

use App\Console\DatabaseUpdate\DoWorld;
use App\Util\BasicFunctions;
use Illuminate\Console\Command;

class UpdateWorld extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:world';

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
        BasicFunctions::ignoreErrs();
        DoWorld::run();
        return 0;
    }
}
