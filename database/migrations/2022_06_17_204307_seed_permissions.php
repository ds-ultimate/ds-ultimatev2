<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
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
            ['id'=>6, 'title'=>'user_management_access'],
            ['id'=>7, 'title'=>'permission_access'],
            ['id'=>8, 'title'=>'role_access'],
            ['id'=>9, 'title'=>'role_show'],
            ['id'=>10, 'title'=>'server_management_access'],
            ['id'=>11, 'title'=>'server_access'],
            ['id'=>12, 'title'=>'server_edit'],
            ['id'=>13, 'title'=>'server_create'],
            ['id'=>14, 'title'=>'server_show'],
            ['id'=>15, 'title'=>'server_delete'],
            ['id'=>16, 'title'=>'world_access'],
            ['id'=>17, 'title'=>'world_edit'],
            ['id'=>18, 'title'=>'world_create'],
            ['id'=>19, 'title'=>'world_show'],
            ['id'=>20, 'title'=>'world_delete'],
            ['id'=>21, 'title'=>'dashboard_access'],
            ['id'=>22, 'title'=>'translation_access'],
            ['id'=>23, 'title'=>'bugreport_management_access'],
            ['id'=>24, 'title'=>'bugreport_access'],
            ['id'=>25, 'title'=>'bugreport_edit'],
            ['id'=>26, 'title'=>'bugreport_create'],
            ['id'=>27, 'title'=>'bugreport_show'],
            ['id'=>28, 'title'=>'bugreport_delete'],
            ['id'=>29, 'title'=>'news_access'],
            ['id'=>30, 'title'=>'news_edit'],
            ['id'=>31, 'title'=>'news_create'],
            ['id'=>32, 'title'=>'news_show'],
            ['id'=>33, 'title'=>'news_delete'],
            ['id'=>34, 'title'=>'bugreport_notification'],
            ['id'=>35, 'title'=>'bugreportComment_access'],
            ['id'=>36, 'title'=>'bugreportComment_edit'],
            ['id'=>37, 'title'=>'bugreportComment_create'],
            ['id'=>38, 'title'=>'bugreportComment_show'],
            ['id'=>39, 'title'=>'bugreportComment_delete'],
            ['id'=>40, 'title'=>'changelog_access'],
            ['id'=>41, 'title'=>'changelog_edit'],
            ['id'=>42, 'title'=>'changelog_create'],
            ['id'=>43, 'title'=>'changelog_show'],
            ['id'=>44, 'title'=>'changelog_delete'],
            ['id'=>45, 'title'=>'applog_access'],
            ['id'=>46, 'title' => 'discord_bot_beta'],
            ['id'=>47, 'title' => 'cacheStat_access'],
            ['id'=>48, 'title' => 'speed_world_access'],
            ['id'=>49, 'title' => 'speed_world_create'],
            ['id'=>50, 'title' => 'speed_world_edit'],
            ['id'=>51, 'title' => 'speed_world_show'],
            ['id'=>52, 'title' => 'speed_world_delete'],
        ]);
        
        DB::table('roles')->insert([
            ['id'=>1, 'title'=>'Admin'],
            ['id'=>2, 'title'=>'Benutzer'],
            ['id'=>3, 'title'=>'Supporter'],
            ['id'=>4, 'title'=>'Übersetzer'],
            ['id'=>5, 'title'=>'Bugreport'],
            ['id'=>6, 'title' =>'beta_user'],
        ]);
        
        DB::table('permission_role')->insert([
            ['role_id'=>1, 'permission_id'=>1],
            ['role_id'=>1, 'permission_id'=>2],
            ['role_id'=>1, 'permission_id'=>3],
            ['role_id'=>1, 'permission_id'=>4],
            ['role_id'=>1, 'permission_id'=>5],
            ['role_id'=>1, 'permission_id'=>6],
            ['role_id'=>1, 'permission_id'=>7],
            ['role_id'=>1, 'permission_id'=>8],
            ['role_id'=>1, 'permission_id'=>9],
            ['role_id'=>1, 'permission_id'=>10],
            ['role_id'=>1, 'permission_id'=>11],
            ['role_id'=>1, 'permission_id'=>12],
            ['role_id'=>1, 'permission_id'=>13],
            ['role_id'=>1, 'permission_id'=>14],
            ['role_id'=>1, 'permission_id'=>15],
            ['role_id'=>1, 'permission_id'=>16],
            ['role_id'=>1, 'permission_id'=>17],
            ['role_id'=>1, 'permission_id'=>18],
            ['role_id'=>1, 'permission_id'=>19],
            ['role_id'=>1, 'permission_id'=>20],
            ['role_id'=>1, 'permission_id'=>21],
            ['role_id'=>1, 'permission_id'=>22],
            ['role_id'=>1, 'permission_id'=>23],
            ['role_id'=>1, 'permission_id'=>24],
            ['role_id'=>1, 'permission_id'=>25],
            ['role_id'=>1, 'permission_id'=>26],
            ['role_id'=>1, 'permission_id'=>27],
            ['role_id'=>1, 'permission_id'=>28],
            ['role_id'=>1, 'permission_id'=>29],
            ['role_id'=>1, 'permission_id'=>30],
            ['role_id'=>1, 'permission_id'=>31],
            ['role_id'=>1, 'permission_id'=>32],
            ['role_id'=>1, 'permission_id'=>33],
            ['role_id'=>1, 'permission_id'=>35],
            ['role_id'=>1, 'permission_id'=>36],
            ['role_id'=>1, 'permission_id'=>37],
            ['role_id'=>1, 'permission_id'=>38],
            ['role_id'=>1, 'permission_id'=>39],
            ['role_id'=>1, 'permission_id'=>40],
            ['role_id'=>1, 'permission_id'=>41],
            ['role_id'=>1, 'permission_id'=>42],
            ['role_id'=>1, 'permission_id'=>43],
            ['role_id'=>1, 'permission_id'=>44],
            ['role_id'=>1, 'permission_id'=>45],
            ['role_id'=>1, 'permission_id'=>47],
            ['role_id'=>1, 'permission_id'=>48],
            ['role_id'=>1, 'permission_id'=>49],
            ['role_id'=>1, 'permission_id'=>50],
            ['role_id'=>1, 'permission_id'=>51],
            ['role_id'=>1, 'permission_id'=>52],
            
            ['role_id'=>3, 'permission_id'=>3],
            ['role_id'=>3, 'permission_id'=>5],
            ['role_id'=>3, 'permission_id'=>6],
            ['role_id'=>3, 'permission_id'=>8],
            ['role_id'=>3, 'permission_id'=>9],
            ['role_id'=>3, 'permission_id'=>10],
            ['role_id'=>3, 'permission_id'=>11],
            ['role_id'=>3, 'permission_id'=>14],
            ['role_id'=>3, 'permission_id'=>16],
            ['role_id'=>3, 'permission_id'=>19],
            ['role_id'=>3, 'permission_id'=>21],
            ['role_id'=>3, 'permission_id'=>24],
            ['role_id'=>3, 'permission_id'=>26],
            ['role_id'=>3, 'permission_id'=>27],
            ['role_id'=>3, 'permission_id'=>35],
            ['role_id'=>3, 'permission_id'=>36],
            ['role_id'=>3, 'permission_id'=>37],
            ['role_id'=>3, 'permission_id'=>38],
            ['role_id'=>3, 'permission_id'=>39],
            ['role_id'=>3, 'permission_id'=>47],
            ['role_id'=>3, 'permission_id'=>48],
            ['role_id'=>3, 'permission_id'=>60],
            ['role_id'=>3, 'permission_id'=>50],
            ['role_id'=>3, 'permission_id'=>51],
            ['role_id'=>3, 'permission_id'=>52],
            
            ['role_id'=>4, 'permission_id'=>22],
            ['role_id'=>5, 'permission_id'=>34],
            ['role_id'=>6, 'permission_id'=>46],
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
};
