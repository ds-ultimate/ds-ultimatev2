<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttackListItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attack_list_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('attack_list_id');
            $table->tinyInteger('type');
            $table->integer('start_village_id');
            $table->integer('target_village_id');
            $table->integer('slowest_unit');
            $table->text('note')->nullable();
            $table->timestamp('send_time')->useCurrent();
            $table->timestamp('arrival_time')->useCurrent();
            $table->timestamps();

            $table->foreign('attack_list_id')->references('id')->on('attack_lists');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attack_list_items');
    }
}
