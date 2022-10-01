<?php

use App\World;
use App\Util\BasicFunctions;
use App\Console\DatabaseUpdate\TableGenerator;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $world = (new World())->get();
                $world = (new World())->where('active', 1)->orWhere("name", "test")->get();
        foreach ($world as $wInner) {
            TableGenerator::allySupportTable($wInner);
            for($num = 0; $num < $wInner->hash_ally; $num++) {
                if(BasicFunctions::hasWorldDataTable($wInner, 'ally_'.$num)) {
                    Schema::table(BasicFunctions::getWorldDataTable($wInner, 'ally_'.$num), function (Blueprint $table) {
                        $table->bigInteger('supBash')->after('defBashRank');
                        $table->integer('supBashRank')->after('supBash');
                    });
                }
            }
            if(BasicFunctions::hasWorldDataTable($wInner, 'ally_latest')) {
                Schema::table(BasicFunctions::getWorldDataTable($wInner, 'ally_latest'), function (Blueprint $table) {
                    $table->bigInteger('supBash')->after('defBashRank');
                    $table->integer('supBashRank')->after('supBash');
                });
            }
            if(BasicFunctions::hasWorldDataTable($wInner, 'ally_changes')) {
                Schema::table(BasicFunctions::getWorldDataTable($wInner, 'ally_changes'), function (Blueprint $table) {
                    $table->bigInteger('offBash')->after('new_ally_id');
                    $table->bigInteger('deffBash')->after('offBash');
                    $table->bigInteger('supBash')->after('deffBash');
                });
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
        $world = (new World())->get();
                $world = (new World())->where('active', 1)->orWhere("name", "test")->get();
        foreach ($world as $wInner) {
            Schema::dropIfExists(BasicFunctions::getWorldDataTable($wInner, 'ally_support'));
            for($num = 0; $num < $wInner->hash_ally; $num++) {
                if(BasicFunctions::hasWorldDataTable($wInner, 'ally_'.$num)) {
                    Schema::table(BasicFunctions::getWorldDataTable($wInner, 'ally_'.$num), function (Blueprint $table) {
                        $table->dropColumn('supBash');
                        $table->dropColumn('supBashRank');
                    });
                }
            }
            if(BasicFunctions::hasWorldDataTable($wInner, 'ally_latest')) {
                Schema::table(BasicFunctions::getWorldDataTable($wInner, 'ally_latest'), function (Blueprint $table) {
                    $table->dropColumn('supBash');
                    $table->dropColumn('supBashRank');
                });
            }
            if(BasicFunctions::hasWorldDataTable($wInner, 'ally_changes')) {
                Schema::table(BasicFunctions::getWorldDataTable($wInner, 'ally_changes'), function (Blueprint $table) {
                    $table->dropColumn('offBash');
                    $table->dropColumn('deffBash');
                    $table->dropColumn('supBash');
                });
            }
        }
    }
};
