<?php

use App\World;
use App\Util\BasicFunctions;
use App\Tool\AttackPlanner\AttackList;
use App\Tool\AttackPlanner\AttackListItem;
use App\Tool\AttackPlanner\AttackListLegacy;
use App\Tool\AttackPlanner\AttackListOwnership;
use App\Console\DatabaseUpdate\TableGenerator;
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
        Schema::create('attack_list_legacies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('world_id');
            $table->unsignedBigInteger('new_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('world_id')->references('id')->on('worlds');
        });
        
        Schema::create('attack_list_ownerships', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('world_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('list_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('world_id')->references('id')->on('worlds');
            $table->foreign('user_id')->references('id')->on('users');
        });
        
        $listCopy = ["user_id", "edit_key", "show_key", "title", "uvMode", "created_at", "updated_at", "deleted_at"];
        $itemCopy = [
            "type", "start_village_id", "target_village_id", "slowest_unit",
            "note", "send_time", "arrival_time", "ms", "send",
            "support_boost", "tribe_skill", "created_at", "updated_at",
            
            "spear", "sword", "axe", "archer", "spy", "light", "marcher",
            "heavy", "ram", "catapult", "knight", "snob",
        ];
        
        foreach((new AttackList())->get() as $model) {
            $world = $model->world;
            if(!BasicFunctions::hasUserWorldDataTable($world, "attack_lists")) {
                TableGenerator::attackPlannerTables($world);
            }
        
            $newList = new AttackList($world);
            static::copyModelContents($model, $newList, $listCopy);
            $newList->api = 0;
            $newList->apiKey = null;
            $newList->save();
            
            foreach($model->items as $it) {
                $newItem = new AttackListItem($world);
                $newItem->attack_list_id = $newList->id;
                static::copyModelContents($it, $newItem, $itemCopy);
                $newItem->save();
            }
            
            $legacyLink = new AttackListLegacy();
            $legacyLink->id = $model->id;
            $legacyLink->world_id = $model->world_id;
            $legacyLink->new_id = $newList->id;
            $legacyLink->save();
            
            if($model->user_id != null) {
                $ownership = new AttackListOwnership();
                $ownership->world_id = $model->world_id;
                $ownership->user_id = $model->user_id;
                $ownership->list_id = $newList->id;
                $ownership->save();
            }
        }
        
        Schema::dropIfExists("attack_list_items");
        Schema::dropIfExists("attack_lists");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // I won't write a reversal for that
    }
    
    private static function copyModelContents($old, $new, $parts) {
        foreach($parts as $p) {
            $new->{$p} = $old->{$p};
        }
    }
};
