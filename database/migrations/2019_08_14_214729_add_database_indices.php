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
            $this->changeAllyTables($world);
            $this->changePlayerTables($world);
            $this->changeVillageTables($world);
        }
    }
    
    private function changeAllyTables(World $world) {
        $tablePrefix = 'ally';
        $envHashIndex = 'hash_ally';
        for($i = 0; $i < config('dsUltimate.'.$envHashIndex); $i++) {
            if(!BasicFunctions::hasWorldDataTable($world, $tablePrefix.'_'.$i)) continue;
            Schema::table(BasicFunctions::getWorldDataTable($world, $tablePrefix.'_'.$i), function (Blueprint $table) {
                $table->index('allyID');
            });
        }
        if(!BasicFunctions::hasWorldDataTable($world, $tablePrefix.'_latest')) return;
        Schema::table(BasicFunctions::getWorldDataTable($world, $tablePrefix.'_latest'), function (Blueprint $table) {
            $table->primary('allyID');
        });
    }
    
    private function changePlayerTables(World $world) {
        $tablePrefix = 'player';
        $envHashIndex = 'hash_player';
        for($i = 0; $i < config('dsUltimate.'.$envHashIndex); $i++) {
            if(!BasicFunctions::hasWorldDataTable($world, $tablePrefix.'_'.$i)) continue;
            Schema::table(BasicFunctions::getWorldDataTable($world, $tablePrefix.'_'.$i), function (Blueprint $table) {
                $table->index('playerID');
            });
        }
        if(!BasicFunctions::hasWorldDataTable($world, $tablePrefix.'_latest')) return;
        Schema::table(BasicFunctions::getWorldDataTable($world, $tablePrefix.'_latest'), function (Blueprint $table) {
            $table->primary('playerID');
        });
    }
    
    private function changeVillageTables(World $world) {
        $tablePrefix = 'village';
        $envHashIndex = 'hash_village';
        for($i = 0; $i < config('dsUltimate.'.$envHashIndex); $i++) {
            if(!BasicFunctions::hasWorldDataTable($world, $tablePrefix.'_'.$i)) continue;
            Schema::table(BasicFunctions::getWorldDataTable($world, $tablePrefix.'_'.$i), function (Blueprint $table) {
                $table->index('villageID');
            });
        }
        if(!BasicFunctions::hasWorldDataTable($world, $tablePrefix.'_latest')) return;
        Schema::table(BasicFunctions::getWorldDataTable($world, $tablePrefix.'_latest'), function (Blueprint $table) {
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
