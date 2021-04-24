<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnimatedMapTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('animHistMapMap', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->unsignedInteger('world_id');
            $table->foreign('world_id')->references('id')->on('worlds');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('edit_key');
            $table->string('show_key');
            $table->text('markers')->nullable();
            $table->integer('opaque')->nullable();
            $table->string('skin')->nullable();
            $table->string('layers')->nullable();
            $table->boolean('autoDimensions')->default(true);
            $table->string('dimensions')->nullable();
            $table->string('defaultColours')->nullable();
            $table->string('title')->nullable();
            $table->float('markerFactor', 10, 8)->default(0.2);
            $table->boolean('continentNumbers')->default(True);
            $table->boolean('showLegend')->default(True);
            $table->integer('legendSize')->default(10);
            $table->string('legendPosition')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('animHistMapJob', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->unsignedInteger('world_id');
            $table->foreign('world_id')->references('id')->on('worlds');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('edit_key');
            $table->string('show_key');
            $table->text('markers')->nullable();
            $table->integer('opaque')->nullable();
            $table->string('skin')->nullable();
            $table->string('layers')->nullable();
            $table->boolean('autoDimensions')->default(true);
            $table->string('dimensions')->nullable();
            $table->string('defaultColours')->nullable();
            $table->string('title')->nullable();
            $table->float('markerFactor', 10, 8)->default(0.2);
            $table->boolean('continentNumbers')->default(True);
            $table->boolean('showLegend')->default(True);
            $table->integer('legendSize')->default(10);
            $table->string('legendPosition')->nullable();
            
            $table->time("finished_at")->nullable();
            $table->string("state")->nullable();
            $table->unsignedBigInteger("animHistMapMap_id");
            $table->foreign('animHistMapMap_id')->references('id')->on('animHistMapMap');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('animHistMapMap');
        Schema::dropIfExists('animHistMapJob');
    }
}
