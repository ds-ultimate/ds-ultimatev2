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
        Schema::create('ds_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\User::class, 'user_id');
            $table->foreignIdFor(\App\World::class, 'world_id');
            $table->unsignedBigInteger('player_id');
            $table->string('key');
            $table->boolean('verified')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ds_connections');
    }
};
