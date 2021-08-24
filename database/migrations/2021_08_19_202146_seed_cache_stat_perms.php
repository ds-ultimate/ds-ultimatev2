<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedCacheStatPerms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('permissions')->insert([
            ['id' => 59, 'title' => 'cacheStat_access'],
        ]);
        
        DB::table('permission_role')->insert([
            ['role_id' => 1, 'permission_id' => 59],
            ['role_id' => 3, 'permission_id' => 59],
        ]);
        
        Schema::create('cache_stats', function (Blueprint $table) {
            $table->id();
            $table->integer('type');
            $table->integer('hits');
            $table->integer('misses');
            $table->string('date');
            
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
        Schema::dropIfExists('cache_stats');
        //
    }
}
