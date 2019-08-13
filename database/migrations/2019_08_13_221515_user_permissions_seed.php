<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserPermissionsSeed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('permissions')->insert([
            ['id'=>1, 'title'=>'user_create'],
            ['id'=>2, 'title'=>'user_edit'],
            ['id'=>3, 'title'=>'user_show'],
            ['id'=>4, 'title'=>'user_delete'],
            ['id'=>5, 'title'=>'user_access'],
            ['id'=>7, 'title'=>'user_management_access'],
            ['id'=>8, 'title'=>'permission_access'],
            ['id'=>9, 'title'=>'permission_create'],
            ['id'=>10, 'title'=>'permission_edit'],
            ['id'=>11, 'title'=>'permission_show'],
            ['id'=>12, 'title'=>'permission_delete'],
            ['id'=>13, 'title'=>'role_access'],
            ['id'=>14, 'title'=>'role_edit'],
            ['id'=>15, 'title'=>'role_create'],
            ['id'=>16, 'title'=>'role_edit'],
            ['id'=>17, 'title'=>'role_show'],
            ['id'=>18, 'title'=>'role_delete'],
            ['id'=>19, 'title'=>'server_management_access'],
            ['id'=>20, 'title'=>'server_access'],
            ['id'=>21, 'title'=>'world_access'],
            ['id'=>22, 'title'=>'server_edit'],
            ['id'=>23, 'title'=>'server_create'],
            ['id'=>24, 'title'=>'server_show'],
            ['id'=>25, 'title'=>'server_delete'],
            ['id'=>26, 'title'=>'world_access'],
            ['id'=>27, 'title'=>'world_edit'],
            ['id'=>28, 'title'=>'world_create'],
            ['id'=>29, 'title'=>'world_show'],
            ['id'=>30, 'title'=>'world_delete'],
            ['id'=>31, 'title'=>'Hhhh hhh hhh; hhh'],
            ['id'=>32, 'title'=>'dashboard_access'],
            ['id'=>33, 'title'=>'translation_access'],
            ['id'=>34, 'title'=>'bugreport_management_access'],
            ['id'=>35, 'title'=>'bugreport_access'],
            ['id'=>36, 'title'=>'bugreport_edit'],
            ['id'=>37, 'title'=>'bugreport_create'],
            ['id'=>38, 'title'=>'bugreport_show'],
            ['id'=>39, 'title'=>'bugreport_delete'],
            ['id'=>40, 'title'=>'news_access'],
            ['id'=>41, 'title'=>'news_edit'],
            ['id'=>42, 'title'=>'news_create'],
            ['id'=>43, 'title'=>'news_show'],
            ['id'=>44, 'title'=>'news_delete'],
            ['id'=>45, 'title'=>'bugreport_notification'],
            ['id'=>46, 'title'=>'bugreportComment_access'],
            ['id'=>47, 'title'=>'bugreportComment_edit'],
            ['id'=>48, 'title'=>'bugreportComment_create'],
            ['id'=>49, 'title'=>'bugreportComment_show'],
            ['id'=>50, 'title'=>'bugreportComment_delete'],
            ['id'=>51, 'title'=>'changelog_access'],
            ['id'=>52, 'title'=>'changelog_edit'],
            ['id'=>53, 'title'=>'changelog_create'],
            ['id'=>54, 'title'=>'changelog_show'],
            ['id'=>55, 'title'=>'changelog_delete']
        ]);
        
        DB::table('roles')->insert([
            ['id'=>1, 'title'=>'Admin'],
            ['id'=>2, 'title'=>'Benutzer'],
            ['id'=>3, 'title'=>'Supporter'],
            ['id'=>4, 'title'=>'Übersetzer'],
            ['id'=>5, 'title'=>'Bugreport']
        ]);
        
        DB::table('permission_role')->insert([
            ['role_id'=>1, 'permission_id'=>1],
            ['role_id'=>1, 'permission_id'=>2],
            ['role_id'=>1, 'permission_id'=>3],
            ['role_id'=>1, 'permission_id'=>4],
            ['role_id'=>1, 'permission_id'=>5],
            ['role_id'=>1, 'permission_id'=>7],
            ['role_id'=>1, 'permission_id'=>8],
            ['role_id'=>1, 'permission_id'=>9],
            ['role_id'=>1, 'permission_id'=>13],
            ['role_id'=>1, 'permission_id'=>14],
            ['role_id'=>1, 'permission_id'=>10],
            ['role_id'=>1, 'permission_id'=>11],
            ['role_id'=>1, 'permission_id'=>12],
            ['role_id'=>1, 'permission_id'=>15],
            ['role_id'=>1, 'permission_id'=>17],
            ['role_id'=>1, 'permission_id'=>18],
            ['role_id'=>1, 'permission_id'=>19],
            ['role_id'=>1, 'permission_id'=>20],
            ['role_id'=>1, 'permission_id'=>22],
            ['role_id'=>1, 'permission_id'=>23],
            ['role_id'=>1, 'permission_id'=>24],
            ['role_id'=>1, 'permission_id'=>25],
            ['role_id'=>1, 'permission_id'=>26],
            ['role_id'=>1, 'permission_id'=>27],
            ['role_id'=>1, 'permission_id'=>28],
            ['role_id'=>1, 'permission_id'=>29],
            ['role_id'=>1, 'permission_id'=>30],
            ['role_id'=>3, 'permission_id'=>3],
            ['role_id'=>3, 'permission_id'=>5],
            ['role_id'=>3, 'permission_id'=>7],
            ['role_id'=>3, 'permission_id'=>13],
            ['role_id'=>3, 'permission_id'=>17],
            ['role_id'=>3, 'permission_id'=>19],
            ['role_id'=>3, 'permission_id'=>20],
            ['role_id'=>3, 'permission_id'=>24],
            ['role_id'=>3, 'permission_id'=>26],
            ['role_id'=>3, 'permission_id'=>29],
            ['role_id'=>1, 'permission_id'=>32],
            ['role_id'=>1, 'permission_id'=>33],
            ['role_id'=>3, 'permission_id'=>32],
            ['role_id'=>4, 'permission_id'=>33],
            ['role_id'=>1, 'permission_id'=>34],
            ['role_id'=>1, 'permission_id'=>35],
            ['role_id'=>1, 'permission_id'=>16],
            ['role_id'=>1, 'permission_id'=>21],
            ['role_id'=>1, 'permission_id'=>38],
            ['role_id'=>1, 'permission_id'=>36],
            ['role_id'=>1, 'permission_id'=>37],
            ['role_id'=>1, 'permission_id'=>39],
            ['role_id'=>1, 'permission_id'=>40],
            ['role_id'=>1, 'permission_id'=>41],
            ['role_id'=>1, 'permission_id'=>42],
            ['role_id'=>1, 'permission_id'=>43],
            ['role_id'=>1, 'permission_id'=>44],
            ['role_id'=>5, 'permission_id'=>45],
            ['role_id'=>1, 'permission_id'=>46],
            ['role_id'=>1, 'permission_id'=>47],
            ['role_id'=>1, 'permission_id'=>48],
            ['role_id'=>1, 'permission_id'=>49],
            ['role_id'=>1, 'permission_id'=>50],
            ['role_id'=>3, 'permission_id'=>35],
            ['role_id'=>3, 'permission_id'=>37],
            ['role_id'=>3, 'permission_id'=>38],
            ['role_id'=>3, 'permission_id'=>46],
            ['role_id'=>3, 'permission_id'=>47],
            ['role_id'=>3, 'permission_id'=>48],
            ['role_id'=>3, 'permission_id'=>49],
            ['role_id'=>3, 'permission_id'=>50],
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
