<?php

namespace App\Console\Commands;

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
    protected $signature = 'delete:table {tableName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Entfernt eine Tabelle aus allen Welten datenbanken';

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
        $worlds = BasicFunctions::getWorldQuery()->get();

        $bar = $this->output->createProgressBar(count($worlds));
        $bar->start();

        foreach ($worlds as $world){
            Schema::dropIfExists(BasicFunctions::getWorldDataTable($world, $this->argument('tableName')));
            $bar->advance();
        }
        $bar->finish();
        return 0;
    }
}
