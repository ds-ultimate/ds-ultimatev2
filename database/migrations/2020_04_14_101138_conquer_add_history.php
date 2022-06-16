<?php

use App\Util\BasicFunctions;
use App\World;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ConquerAddHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $worlds = (new World())->get();
        foreach ($worlds as $world){
            Schema::table(BasicFunctions::getWorldDataTable($world, '.conquer'), function (Blueprint $table) {
                $table->increments('id');
                $table->string('old_owner_name')->nullable()->default(null);
                $table->string('new_owner_name')->nullable()->default(null);
                $table->integer('old_ally')->nullable()->default(null);
                $table->integer('new_ally')->nullable()->default(null);
                $table->string('old_ally_name')->nullable()->default(null);
                $table->string('new_ally_name')->nullable()->default(null);
                $table->string('old_ally_tag')->nullable()->default(null);
                $table->string('new_ally_tag')->nullable()->default(null);
            });
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
