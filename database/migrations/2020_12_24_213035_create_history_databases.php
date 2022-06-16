<?php

use App\World;
use App\Console\DatabaseUpdate\TableGenerator;
use App\Util\BasicFunctions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateHistoryDatabases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach((new World())->get() as $dbWorld) {
            $dbName = BasicFunctions::getWorldDataDatabase($dbWorld);
            if (DB::statement('CREATE DATABASE ' . $dbName . '_history') !== true) {
                echo("DB '$dbName\_history' konnte nicht erstellt werden.");
            }
            TableGenerator::historyIndexTable($dbWorld);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach((new World())->get() as $dbWorld) {
            $dbName = BasicFunctions::getWorldDataDatabase($dbWorld);
            if (DB::statement('DROP DATABASE ' . $dbName . '_history') !== true) {
                echo("DB '$dbName\_history' konnte nicht geloescht werden.");
            }
        }
    }
}
