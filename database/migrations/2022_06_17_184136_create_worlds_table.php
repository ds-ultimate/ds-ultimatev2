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
        Schema::create('worlds', function (Blueprint $table) {
            $table->id();
            
            $table->foreignIdFor(\App\Server::class, 'server_id');
            $table->text('name');
            $table->string('display_name');
            $table->integer('ally_count')->nullable();
            $table->integer('player_count')->nullable();
            $table->integer('village_count')->nullable();
            $table->text('url');
            $table->text('config');
            $table->text('units');
            $table->text('buildings')->nullable();
            $table->integer('win_condition')->nullable()->default(null);
            $table->integer('hash_ally');
            $table->integer('hash_player');
            $table->integer('hash_village');
            
            $table->foreignIdFor(\App\WorldDatabase::class, 'database_id')->nullable();
            $table->boolean('active')->default(1)->nullable();
            $table->boolean('maintananceMode')->default(false);
            
            $table->timestamps();
            $table->timestamp('worldCheck_at')->useCurrent();
            $table->timestamp('worldUpdated_at')->useCurrent();
            $table->timestamp('worldCleaned_at')->useCurrent();
            $table->date('worldTop_at')->nullable();
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
        Schema::dropIfExists('worlds');
    }
};
