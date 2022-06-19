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
        Schema::create('accMgrDB_Template', function (Blueprint $table) {
            $table->id();
            
            $table->text("show_key");
            $table->foreignIdFor(\App\User::class, 'user_id');
            $table->boolean('public')->default(false);
            
            $table->text("name");
            $table->text("buildings", 2000);
            $table->boolean('remove_additional')->default(false);
            $table->text("ignore_remove", 200)->nullable();
            
            $table->float('rating')->default(0);
            $table->integer('totalVotes')->default(0);
            $table->boolean('contains_watchtower');
            $table->boolean('contains_church');
            $table->boolean('contains_statue');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('user_id');
        });
        
        Schema::create('accMgrDB_Ratings', function (Blueprint $table) {
            $table->id();
            
            $table->foreignIdFor(\App\Tool\AccMgrDB\AccountManagerTemplate::class, 'template_id');
            $table->integer('rating');
            $table->foreignIdFor(\App\User::class, 'user_id');
            
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
        Schema::dropIfExists('accMgrDB_Ratings');
        Schema::dropIfExists('accMgrDB_Template');
    }
};
