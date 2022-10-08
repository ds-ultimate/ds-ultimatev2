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
        Schema::create('attackplanner_custom_sound', function (Blueprint $table) {
            $table->id();
            
            $table->foreignIdFor(\App\User::class, 'user_id')->nullable();
            $table->string('name');
            $table->string('internal_id');
            $table->timestamps();
            
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
        Schema::dropIfExists('attackplanner_custom_sound');
    }
};
