<?php

use App\World;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWinConditionToWorldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('worlds', function (Blueprint $table) {
            $table->integer('win_condition')->nullable()->default(null);
        });
        
        foreach((new World())->get() as $w) {
            $w->touch();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('worlds', function (Blueprint $table) {
            $table->dropColumn('win_condition');
        });
    }
}
