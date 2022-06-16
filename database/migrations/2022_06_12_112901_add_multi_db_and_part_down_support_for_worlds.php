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
        Schema::create('world_databases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
        });
        
        Schema::table('worlds', function (Blueprint $table) {
            $table->unsignedBigInteger('database_id')->nullable()->default(null);
            $table->foreign('database_id')->references('id')->on('world_databases');
            
            $table->boolean('maintananceMode')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('worlds', function (Blueprint $table) {
            $table->dropColumn('database_id');
            $table->dropColumn('maintananceMode');
        });
        
        Schema::dropIfExists('world_databases');
    }
};
