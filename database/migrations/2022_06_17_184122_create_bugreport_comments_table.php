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
        Schema::create('bugreport_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Bugreport::class, 'bugreport_id');
            $table->foreignIdFor(\App\User::class, 'user_id');
            $table->text('content');
            $table->timestamps();
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
        Schema::dropIfExists('bugreport_comments');
    }
};
