<?php

use App\SpeedWorld;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedSpeedWorldPerms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('permissions')->insert([
            ['id' => 60, 'title' => 'speed_world_access'],
            ['id' => 61, 'title' => 'speed_world_create'],
            ['id' => 62, 'title' => 'speed_world_edit'],
            ['id' => 63, 'title' => 'speed_world_show'],
            ['id' => 64, 'title' => 'speed_world_delete'],
        ]);
        
        DB::table('permission_role')->insert([
            ['role_id' => 1, 'permission_id' => 60],
            ['role_id' => 3, 'permission_id' => 60],
            ['role_id' => 1, 'permission_id' => 61],
            ['role_id' => 3, 'permission_id' => 61],
            ['role_id' => 1, 'permission_id' => 62],
            ['role_id' => 3, 'permission_id' => 62],
            ['role_id' => 1, 'permission_id' => 63],
            ['role_id' => 3, 'permission_id' => 63],
            ['role_id' => 1, 'permission_id' => 64],
            ['role_id' => 3, 'permission_id' => 64],
        ]);
        
        Schema::table('server', function (Blueprint $table) {
            $table->boolean('classic_active')->default(false);
        });
        
        Schema::table('speed_worlds', function (Blueprint $table) {
            $table->unsignedInteger('server_id')->change();
            $table->foreign('server_id', 'server_id_foreign')->references('id')->on('server');
            $table->string('instance')->nullable();
        });
        
        foreach((new SpeedWorld())->get() as $model) {
            if($model->world !== null) {
                $url = str_replace(["https://"], "", $model->world->url);
                $world = substr($url, 2, strpos($url, ".") - 2);
                $model->instance = $world;
                $model->save();
            }
        }
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
