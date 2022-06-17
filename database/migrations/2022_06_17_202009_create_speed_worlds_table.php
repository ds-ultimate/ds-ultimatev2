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
        Schema::create('speed_worlds', function (Blueprint $table) {
            $table->id();
            
            $table->foreignIdFor(\App\Server::class, 'server_id');
            $table->text('name');
            $table->string('display_name')->nullable();
            $table->string('instance')->nullable();
            $table->bigInteger("planned_start");
            $table->bigInteger("planned_end");
            $table->boolean("started");
            $table->foreignIdFor(\App\World::class, 'world_id')->nullable();
            
            $table->timestamps();
            $table->timestamp('worldCheck_at')->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('speed_worlds');
    }
};
