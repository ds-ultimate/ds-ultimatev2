<?php

use App\World;
use App\Util\BasicFunctions;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDatabaseIndices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach((new World())->get() as $world) {
            $dbName = BasicFunctions::getDatabaseName($world->server->code, $world->name);
            $this->changeAllyTables($dbName);
            $this->changePlayerTables($dbName);
            $this->changeVillageTables($dbName);
        }
    }
    
    private function changeAllyTables($dbName) {
        $tablePrefix = 'ally';
        $envHashIndex = 'hash_ally';
        for($i = 0; $i < config('dsUltimate.'.$envHashIndex); $i++) {
            if(!BasicFunctions::existTable($dbName, $tablePrefix.'_'.$i)) continue;
            Schema::table($dbName.'.'.$tablePrefix.'_'.$i, function (Blueprint $table) {
                $table->index('allyID');
            });
        }
        if(!BasicFunctions::existTable($dbName, $tablePrefix.'_latest')) return;
        Schema::table($dbName.'.'.$tablePrefix.'_latest', function (Blueprint $table) {
            $table->primary('allyID');
        });
    }
    
    private function changePlayerTables($dbName) {
        $tablePrefix = 'player';
        $envHashIndex = 'hash_player';
        for($i = 0; $i < config('dsUltimate.'.$envHashIndex); $i++) {
            if(!BasicFunctions::existTable($dbName, $tablePrefix.'_'.$i)) continue;
            Schema::table($dbName.'.'.$tablePrefix.'_'.$i, function (Blueprint $table) {
                $table->index('playerID');
            });
        }
        if(!BasicFunctions::existTable($dbName, $tablePrefix.'_latest')) return;
        Schema::table($dbName.'.'.$tablePrefix.'_latest', function (Blueprint $table) {
            $table->primary('playerID');
        });
    }
    
    private function changeVillageTables($dbName) {
        $tablePrefix = 'village';
        $envHashIndex = 'hash_village';
        for($i = 0; $i < config('dsUltimate.'.$envHashIndex); $i++) {
            if(!BasicFunctions::existTable($dbName, $tablePrefix.'_'.$i)) continue;
            Schema::table($dbName.'.'.$tablePrefix.'_'.$i, function (Blueprint $table) {
                $table->index('villageID');
            });
        }
        if(!BasicFunctions::existTable($dbName, $tablePrefix.'_latest')) return;
        Schema::table($dbName.'.'.$tablePrefix.'_latest', function (Blueprint $table) {
            $table->primary('villageID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
