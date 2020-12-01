<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccMgrTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accMgrDB_Template', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->text("show_key");
            $table->unsignedBigInteger('user_id');
            $table->boolean('public')->default(false);
            
            $table->text("name");
            $table->text("buildings", 2000);
            $table->boolean('remove_additional')->default(false);
            $table->text("ignore_remove", 200)->nullable()->default(null);
            
            $table->float('rating')->default(0);
            $table->integer('totalVotes')->default(0);
            $table->boolean('contains_watchtower');
            $table->boolean('contains_church');
            $table->boolean('contains_statue');
            
            $table->foreign('user_id')->references('id')->on('users');
        });
        
        Schema::create('accMgrDB_Ratings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedInteger('template_id');
            $table->integer('rating');
            $table->unsignedBigInteger('user_id');
            $table->foreign('template_id')->references('id')->on('accMgrDB_Template');
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
        Schema::dropIfExists('accMgrDB_Ratings');
        Schema::dropIfExists('accMgrDB_Template');
    }
}
