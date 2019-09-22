<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUnitAndTitleToAttackListItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attack_list_items', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->integer('spear')->default(0);
            $table->integer('sword')->default(0);
            $table->integer('axe')->default(0);
            $table->integer('archer')->default(0);
            $table->integer('spy')->default(0);
            $table->integer('light')->default(0);
            $table->integer('marcher')->default(0);
            $table->integer('heavy')->default(0);
            $table->integer('ram')->default(0);
            $table->integer('catapult')->default(0);
            $table->integer('knight')->default(0);
            $table->integer('snob')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
