<?php

use App\Util\BasicFunctions;
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
        foreach((new World())->get() as $model) {
            Schema::table(BasicFunctions::getWorldDataTable($model, "conquer"), function (Blueprint $table) {
                $table->integer('points')->default(-1);
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
        foreach((new World())->get() as $model) {
            Schema::table(BasicFunctions::getWorldDataTable($model, "conquer"), function (Blueprint $table) {
                $table->dropColumn('points');
            });
        }
    }
};
