<?php

use App\World;
use App\Tool\AttackPlanner\AttackList;
use App\Tool\AttackPlanner\AttackListItem;
use App\Tool\Map\Map;
use App\Util\BasicFunctions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSpeedToServerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('server', function (Blueprint $table) {
            $table->boolean('speed_active')->default(false);
        });
        
        foreach((new World())->get() as $world) {
            if(! $world->isSpeed()) {
                continue;
            }
            
            $dbName = BasicFunctions::getDatabaseName($world->server->code, $world->name);
            $worldID = $world->id;
            $world->delete();
            
            //Pretend all speed worlds never existed (needed in order to sort them by number)
            DB::statement("DROP DATABASE $dbName");
            
            foreach((new AttackList())->where('world_id', $worldID)->get() as $attList) {
                foreach((new AttackListItem())->where('attack_list_id', $attList->id) as $attListItem) {
                    $attListItem->delete();
                }
                $attList->delete();
            }
            
            foreach((new Map())->where('world_id', $worldID)->get() as $map) {
                $map->delete();
            }
        }
        
        Schema::create('speed_worlds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('server_id');
            $table->text('name');
            $table->bigInteger("planned_start");
            $table->bigInteger("planned_end");
            $table->boolean("started");
            $table->integer("world_id")->unsigned()->nullable();
            $table->timestamps();
            $table->timestamp('worldCheck_at')->useCurrent();
            $table->softDeletes();
            
            $table->foreign('world_id')->references('id')->on('worlds');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
