<?php

use App\World;
use App\Util\BasicFunctions;
use App\Http\Controllers\DBController;
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
            $server = $dbWorld->server->code;
            $world = $dbWorld->name;
            $dbName = BasicFunctions::getDatabaseName($server, $world);
            if (DB::statement('CREATE DATABASE ' . $dbName . '_history') !== true) {
                echo("DB '$dbName\_history' konnte nicht erstellt werden.");
            }
            DBController::historyIndexTable($dbName);
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
            $server = $dbWorld->server->code;
            $world = $dbWorld->name;
            $dbName = BasicFunctions::getDatabaseName($server, $world);
            if (DB::statement('DROP DATABASE ' . $dbName . '_history') !== true) {
                echo("DB '$dbName\_history' konnte nicht geloescht werden.");
            }
        }
    }
}
