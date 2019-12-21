<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuildtimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buildtimesraw', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            
            $table->unsignedInteger('world_id');
            $table->unsignedBigInteger('user_id');
            
            $table->text("rawdata");
            $table->float("booster")->default(0);
            
            $table->foreign('world_id')->references('id')->on('worlds');
            $table->foreign('user_id')->references('id')->on('users');
        });
        
        Schema::create('buildtimes', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            
            $table->unsignedInteger('world_id');
            $table->unsignedBigInteger('user_id');
            
            $table->string("building");
            $table->string("level");
            $table->string("wood");
            $table->string("clay");
            $table->string("iron");
            $table->string("buildtime");
            $table->string("pop");
            $table->string("mainLevel");
            $table->unsignedInteger("rawdata_id");
            
            $table->foreign('rawdata_id')->references('id')->on('buildtimesraw');
            $table->foreign('world_id')->references('id')->on('worlds');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('buildtimes');
        Schema::dropIfExists('buildtimesraw');
    }
}
