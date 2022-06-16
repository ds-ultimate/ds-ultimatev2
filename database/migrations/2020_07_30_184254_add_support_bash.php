<?php

use App\World;
use App\Util\BasicFunctions;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddSupportBash extends Migration
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
            echo $world->serName() . " player_latest\n";
            $blueprint = function (Blueprint $table) {
                $table->bigInteger('supBash')->nullable()->default(null);
                $table->integer('supBashRank')->nullable()->default(null);
            };
            Schema::table(BasicFunctions::getWorldDataTable($world, "player_latest"), $blueprint);

            for ($num = 0; $num < config('dsUltimate.hash_player'); $num++){
                if(!BasicFunctions::existTable(BasicFunctions::getWorldDataTable($world, "player_$num"))) {
                    continue;
                }
                
                echo $world->serName() . ".player_$num\n";
                Schema::table(BasicFunctions::getWorldDataTable($world, "player_$num"), $blueprint);
            }
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
    
    private function noColumn($db, $table, $column) {
        $cols = DB::select("SELECT column_name as `column_name` from information_schema.columns where table_schema = ? and table_name = ?", [$db, $table]);
        foreach($cols as $col) {
            if($col->column_name == $column) {
                return false;
            }
        }
        return true;
    }
}
