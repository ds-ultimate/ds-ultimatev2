<?php

use App\World;
use App\Console\DatabaseUpdate\TableGenerator;
use App\Util\BasicFunctions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach((new World())->get() as $dbWorld) {
            TableGenerator::allyTopTable($dbWorld);
            TableGenerator::playerTopTable($dbWorld);
        }
        
        Schema::table('worlds', function (Blueprint $table) {
            $table->date('worldTop_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach((new World())->get() as $dbWorld) {
            Schema::dropIfExists(BasicFunctions::getWorldDataTable($dbWorld, 'ally_top'));
            Schema::dropIfExists(BasicFunctions::getWorldDataTable($dbWorld, 'player_top'));
        }
    }
}
