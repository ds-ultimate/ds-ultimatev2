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
            $playerLatest = BasicFunctions::getWorldDataTable($world, "player_latest");
            echo "$playerLatest\n";
            Schema::table($playerLatest, function (Blueprint $table) {
                $table->string('name', 288)->change();
            });
            for ($num = 0; $num < config('dsUltimate.hash_player'); $num++){
                if(!BasicFunctions::hasWorldDataTable($world, "player_$num")) {
                    continue;
                }
                
                $playerHist = BasicFunctions::getWorldDataTable($world, "player_$num");
                echo "$playerHist\n";
                Schema::table($playerHist, function (Blueprint $table) {
                    $table->string('name', 288)->change();
                });
            }
            
            $allyLatest = BasicFunctions::getWorldDataTable($world, "ally_latest");
            echo "$allyLatest\n";
            Schema::table($allyLatest, function (Blueprint $table) {
                $table->string('name', 384)->change();
                $table->string('tag', 72)->change();
            });
            for ($num = 0; $num < config('dsUltimate.hash_ally'); $num++){
                if(!BasicFunctions::hasWorldDataTable($world, "ally_$num")) {
                    continue;
                }
                
                $allyHist = BasicFunctions::getWorldDataTable($world, "ally_$num");
                echo "$allyHist\n";
                Schema::table($allyHist, function (Blueprint $table) {
                    $table->string('name', 384)->change();
                    $table->string('tag', 72)->change();
                });
            }
            
            $villageLatest = BasicFunctions::getWorldDataTable($world, "village_latest");
            echo "$villageLatest\n";
            Schema::table($villageLatest, function (Blueprint $table) {
                $table->string('name', 384)->change();
            });
            for ($num = 0; $num < config('dsUltimate.hash_village'); $num++){
                if(!BasicFunctions::hasWorldDataTable($world, "village_$num")) {
                    continue;
                }
                
                $villageHist = BasicFunctions::getWorldDataTable($world, "village_$num");
                echo "$villageHist\n";
                Schema::table($villageHist, function (Blueprint $table) {
                    $table->string('name', 384)->change();
                });
            }
            
            $conquer = BasicFunctions::getWorldDataTable($world, "conquer");
            echo "$conquer\n";
            Schema::table($conquer, function (Blueprint $table) {
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
