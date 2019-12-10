<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDrawingsToMap extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('map', function (Blueprint $table) {
            $table->mediumText('drawing_obj')->nullable();
            $table->string('drawing_dim')->nullable();
        });
        DB::statement("ALTER TABLE map ADD drawing_png MEDIUMBLOB");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
