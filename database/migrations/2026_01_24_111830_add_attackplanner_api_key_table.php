<?php

use App\Permission;
use App\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    private static $PERMISSIONS = [
        'attackplanner_api_access',
        'attackplanner_api_create',
        'attackplanner_api_edit',
        'attackplanner_api_show',
        'attackplanner_api_delete',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attackplanner_api_keys', function (Blueprint $table) {
            $table->id();

            $table->string('discord_name');
            $table->string('discord_id');
            $table->string('key');
            $table->timestamps();

            $table->index('key');
        });

        $inserCache = [];
        $role = Role::where("title", "Admin")->first();

        foreach(static::$PERMISSIONS as $permTitle) {
            $perm = new Permission();
            $perm->title = $permTitle;
            $perm->save();

            $inserCache[] = ['role_id'=>$role->id, 'permission_id'=>$perm->id];
        }
        
        DB::table('permission_role')->insert($inserCache);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Get permission IDs
        $permissionIds = Permission::whereIn('title', static::$PERMISSIONS)->pluck('id');

        // Remove pivot table entries
        DB::table('permission_role')
            ->whereIn('permission_id', $permissionIds)
            ->delete();

        // Delete permissions
        Permission::whereIn('title', static::$PERMISSIONS)->delete();

        // Drop table (indexes are dropped automatically)
        Schema::dropIfExists('attackplanner_api_keys');
    }
};
