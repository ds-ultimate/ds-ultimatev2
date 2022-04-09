<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AttackplaneritemaddutBoost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attack_list_items', function (Blueprint $table) {
            $table->float('support_boost')->default(0.00);
            $table->float('tribe_skill')->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attack_list_items', function (Blueprint $table) {
            $table->dropColumn('support_boost');
            $table->dropColumn('tribe_skill');
        });
    }
}
