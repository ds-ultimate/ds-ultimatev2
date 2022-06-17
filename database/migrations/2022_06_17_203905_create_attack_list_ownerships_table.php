<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attack_list_ownerships', function (Blueprint $table) {
            $table->id();
            
            $table->foreignIdFor(\App\World::class, 'world_id');
            $table->foreignIdFor(\App\User::class, 'user_id');
            $table->unsignedBigInteger('list_id');
            
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
        Schema::dropIfExists('attack_list_ownerships');
    }
};
