<?php

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
        Schema::create('world_statistics', function (Blueprint $table) {
            $table->id();
            
            $table->foreignIdFor(\App\World::class, 'world_id');
            $table->integer('total_player')->default(0);
            $table->integer('total_ally')->default(0);
            $table->integer('total_villages')->default(0);
            $table->integer('total_barbarian_village')->default(0);
            $table->integer('total_conquer')->default(0);
            $table->integer('daily_conquer')->default(0);
            $table->integer('daily_ally_changes')->default(0);
            $table->integer('daily_updates')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('world_statistics');
    }
};
