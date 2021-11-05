<?php

use App\World;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomDisplayName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('speed_worlds', function (Blueprint $table) {
            $table->string('display_name')->nullable();
        });
        
        Schema::table('worlds', function (Blueprint $table) {
            $table->string('display_name');
        });
        
        foreach((new World())->get() as $model) {
            $model->display_name = $model->generateDisplayName();
            $model->save();
        }
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
