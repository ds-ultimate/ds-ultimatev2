<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserPermissionsChangelogSeed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('permission_role')->insert([
            ['role_id'=>1, 'permission_id'=>51],
            ['role_id'=>1, 'permission_id'=>52],
            ['role_id'=>1, 'permission_id'=>53],
            ['role_id'=>1, 'permission_id'=>54],
            ['role_id'=>1, 'permission_id'=>55],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
