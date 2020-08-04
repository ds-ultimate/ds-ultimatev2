<?php

use App\World;
use App\Util\BasicFunctions;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixNameLenght extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $worlds = (new World())->get();
        foreach ($worlds as $world){
            echo BasicFunctions::getDatabaseName($world->server->code, $world->name).".player_latest\n";
            Schema::table(BasicFunctions::getDatabaseName($world->server->code, $world->name).".player_latest", function (Blueprint $table) {
                $table->string('name', 288)->change();
            });
            for ($num = 0; $num < config('dsUltimate.hash_player'); $num++){
                if(!BasicFunctions::existTable(BasicFunctions::getDatabaseName($world->server->code, $world->name), "player_$num")) {
                    continue;
                }
                
                echo BasicFunctions::getDatabaseName($world->server->code, $world->name).".player_$num\n";
                Schema::table(BasicFunctions::getDatabaseName($world->server->code, $world->name).".player_$num", function (Blueprint $table) {
                    $table->string('name', 288)->change();
                });
            }
            
            echo BasicFunctions::getDatabaseName($world->server->code, $world->name).".ally_latest\n";
            Schema::table(BasicFunctions::getDatabaseName($world->server->code, $world->name).".ally_latest", function (Blueprint $table) {
                $table->string('name', 384)->change();
                $table->string('tag', 72)->change();
            });
            for ($num = 0; $num < config('dsUltimate.hash_ally'); $num++){
                if(!BasicFunctions::existTable(BasicFunctions::getDatabaseName($world->server->code, $world->name), "ally_$num")) {
                    continue;
                }
                
                echo BasicFunctions::getDatabaseName($world->server->code, $world->name).".ally_$num\n";
                Schema::table(BasicFunctions::getDatabaseName($world->server->code, $world->name).".ally_$num", function (Blueprint $table) {
                    $table->string('name', 384)->change();
                    $table->string('tag', 72)->change();
                });
            }
            
            echo BasicFunctions::getDatabaseName($world->server->code, $world->name).".village_latest\n";
            Schema::table(BasicFunctions::getDatabaseName($world->server->code, $world->name).".village_latest", function (Blueprint $table) {
                $table->string('name', 384)->change();
            });
            for ($num = 0; $num < config('dsUltimate.hash_village'); $num++){
                if(!BasicFunctions::existTable(BasicFunctions::getDatabaseName($world->server->code, $world->name), "village_$num")) {
                    continue;
                }
                
                echo BasicFunctions::getDatabaseName($world->server->code, $world->name).".village_$num\n";
                Schema::table(BasicFunctions::getDatabaseName($world->server->code, $world->name).".village_$num", function (Blueprint $table) {
                    $table->string('name', 384)->change();
                });
            }
            
            echo BasicFunctions::getDatabaseName($world->server->code, $world->name).".conquer\n";
            Schema::table(BasicFunctions::getDatabaseName($world->server->code, $world->name).".conquer", function (Blueprint $table) {
                $table->string('old_owner_name', 288)->nullable()->default(null)->change();
                $table->string('new_owner_name', 288)->nullable()->default(null)->change();
                $table->string('old_ally_name', 384)->nullable()->default(null)->change();
                $table->string('new_ally_name', 384)->nullable()->default(null)->change();
                $table->string('old_ally_tag', 72)->nullable()->default(null)->change();
                $table->string('new_ally_tag', 72)->nullable()->default(null)->change();
            });
        }
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
