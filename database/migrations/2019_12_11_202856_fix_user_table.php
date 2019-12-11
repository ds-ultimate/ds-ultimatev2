<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
        });
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('discord_id')->nullable()->change();
            $table->string('github_id')->nullable()->change();
            $table->string('facebook_id')->nullable()->change();
            $table->string('google_id')->nullable()->change();
            $table->string('twitter_id')->nullable()->change();
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
}
