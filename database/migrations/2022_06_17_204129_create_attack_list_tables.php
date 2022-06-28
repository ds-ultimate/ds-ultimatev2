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
            
            $table->tinyText('spear')->nullable();
            $table->tinyText('sword')->nullable();
            $table->tinyText('axe')->nullable();
            $table->tinyText('archer')->nullable();
            $table->tinyText('spy')->nullable();
            $table->tinyText('light')->nullable();
            $table->tinyText('marcher')->nullable();
            $table->tinyText('heavy')->nullable();
            $table->tinyText('ram')->nullable();
            $table->tinyText('catapult')->nullable();
            $table->tinyText('knight')->nullable();
            $table->tinyText('snob')->nullable();
            
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
