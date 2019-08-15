<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBugreportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bugreports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email');
            $table->text('title');
            $table->boolean('priority');
            $table->text('description');
            $table->text('url')->nullable();
            $table->boolean('status')->default(0);
            $table->integer('firstSeenUser_id')->nullable();
            $table->timestamp('firstSeen')->nullable();
            $table->timestamp('delivery')->nullable();
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
        Schema::dropIfExists('bugreports');
    }
}
