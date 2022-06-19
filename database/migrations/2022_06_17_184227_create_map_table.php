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
        Schema::create('map', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\World::class, 'world_id');
            $table->foreignIdFor(\App\User::class, 'user_id')->nullable();
            
            $table->string('title')->nullable();
            $table->string('edit_key');
            $table->string('show_key');
            $table->text('markers')->nullable();
            $table->integer('opaque')->nullable();
            $table->string('skin')->nullable();
            $table->string('layers')->nullable();
            $table->string('dimensions')->nullable();
            $table->string('defaultColours')->nullable();
            $table->float('markerFactor', 10, 8)->default(0.2);
            $table->boolean('continentNumbers')->default(true);
            $table->boolean("autoDimensions")->default(true);
            
            $table->mediumText('drawing_obj')->nullable();
            $table->string('drawing_dim')->nullable();
            
            $table->boolean('shouldUpdate')->default(false);
            $table->dateTime('cached_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('world_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('map');
    }
};
