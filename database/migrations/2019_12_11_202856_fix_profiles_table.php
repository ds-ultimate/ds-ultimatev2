<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('discord');
            $table->bigInteger('discord_id')->nullable()->after('show_skype');
            $table->bigInteger('github_id')->nullable()->after('show_discord');
            $table->bigInteger('facebook_id')->nullable()->after('github_id');
            $table->bigInteger('google_id')->nullable()->after('facebook_id');
            $table->bigInteger('twitter_id')->nullable()->after('google_id');
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
