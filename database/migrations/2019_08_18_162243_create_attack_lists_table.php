<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttackListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attack_lists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('world_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('edit_key');
            $table->string('show_key');
            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('attack_lists');
    }
}
