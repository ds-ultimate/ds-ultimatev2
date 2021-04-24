<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class SeedBetaTesterPerms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('permissions')->insert([
            ['id' => 57, 'title' => 'discord_bot_beta'],
            ['id' => 58, 'title' => 'anim_hist_map_beta'],
        ]);
        
        DB::table('roles')->insert([
            ['id' => 6, 'title' => 'beta_user']
        ]);
        
        DB::table('permission_role')->insert([
            ['role_id' => 6, 'permission_id' => 57],
            ['role_id' => 6, 'permission_id' => 58],
        ]);
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
