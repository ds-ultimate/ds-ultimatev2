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
        Schema::create("attack_lists", function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->foreignIdFor(\App\World::class, 'world_id');
            $table->foreignIdFor(\App\User::class, 'user_id')->nullable();
            $table->string('edit_key');
            $table->string('show_key');
            $table->string('title')->nullable();
            $table->boolean('uvMode')->default(false);
            $table->boolean('api')->default(false);
            $table->integer('apiKey')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create("attack_list_items", function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->foreignIdFor(\App\Tool\AttackPlanner\AttackList::class, 'attack_list_id');
            $table->tinyInteger('type');
            $table->integer('start_village_id');
            $table->integer('target_village_id');
            $table->integer('slowest_unit');
            $table->text('note')->nullable();
            $table->timestamp('send_time')->useCurrent();
            $table->timestamp('arrival_time')->useCurrent();
            $table->smallInteger('ms')->default(0);
            $table->boolean('send')->default(0);
            
            $table->float('support_boost')->default(0.00);
            $table->float('tribe_skill')->default(0.00);
            
            $table->integer('spear')->default(0);
            $table->integer('sword')->default(0);
            $table->integer('axe')->default(0);
            $table->integer('archer')->default(0);
            $table->integer('spy')->default(0);
            $table->integer('light')->default(0);
            $table->integer('marcher')->default(0);
            $table->integer('heavy')->default(0);
            $table->integer('ram')->default(0);
            $table->integer('catapult')->default(0);
            $table->integer('knight')->default(0);
            $table->integer('snob')->default(0);
            
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
        Schema::dropIfExists('attack_lists');
        Schema::dropIfExists('attack_list_items');
    }
};
