<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('map', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            
            $table->unsignedInteger('world_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('edit_key');
            $table->string('show_key');
            $table->string('markers')->nullable();
            $table->integer('opaque')->nullable();
            $table->string('skin')->nullable();
            $table->string('layers')->nullable();
            $table->string('dimensions')->nullable();
            $table->string('defaultColours')->nullable();
            
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
        Schema::dropIfExists('map');
    }
}
