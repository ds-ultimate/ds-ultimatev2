<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBugreportCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bugreport_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('bugreport_id');
            $table->unsignedBigInteger('user_id');
            $table->text('content');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('bugreport_id')->references('id')->on('bugreports');
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
        Schema::dropIfExists('bugreport_comments');
    }
}
