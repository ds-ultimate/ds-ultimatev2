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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\User::class, 'user_id');
            $table->string('discord_id')->nullable();
            $table->string('discord_private_channel_id')->nullable();
            $table->string('github_id')->nullable();
            $table->string('google_id')->nullable();
            $table->timestamp('last_seen_changelog')->nullable();
            
            $table->string('map_dimensions')->nullable();
            $table->string('map_defaultColours')->nullable();
            $table->float('map_markerFactor', 10, 8)->default(0.2);
            
            $table->string('conquerHightlight_World')->default("s:i:b:d");
            $table->string('conquerHightlight_Ally')->default("s:i:b:d:w:l");
            $table->string('conquerHightlight_Player')->default("s:i:b:d:w:l");
            $table->string('conquerHightlight_Village')->default("s:i:b:d");
            
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
        Schema::dropIfExists('profiles');
    }
};
