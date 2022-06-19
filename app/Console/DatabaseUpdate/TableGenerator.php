<?php

namespace App\Console\DatabaseUpdate;

use App\Server;
use App\World;
use App\Util\BasicFunctions;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableGenerator
{
    public static function playerTable(World $model, $num) {
        Schema::create(BasicFunctions::getWorldDataTable($model, 'player_'.$num), function (Blueprint $table) {
            $table->integer('playerID');
            $table->string('name', 288);
            $table->integer('ally_id');
            $table->integer('village_count');
            $table->integer('points');
            $table->integer('rank');
            $table->bigInteger('offBash')->nullable();
            $table->integer('offBashRank')->nullable();
            $table->bigInteger('defBash')->nullable();
            $table->integer('defBashRank')->nullable();
            $table->bigInteger('supBash')->nullable();
            $table->integer('supBashRank')->nullable();
            $table->bigInteger('gesBash')->nullable();
            $table->integer('gesBashRank')->nullable();
            $table->timestamps();
            $table->index('playerID', 'primary_playerID');
        });
    }

    public static function playerLatestTable(World $model, $latestSuffix) {
        Schema::create(BasicFunctions::getWorldDataTable($model, 'player_'.$latestSuffix), function (Blueprint $table) {
            $table->integer('playerID');
            $table->string('name', 288);
            $table->integer('ally_id');
            $table->integer('village_count');
            $table->integer('points');
            $table->integer('rank');
            $table->bigInteger('offBash')->nullable();
            $table->integer('offBashRank')->nullable();
            $table->bigInteger('defBash')->nullable();
            $table->integer('defBashRank')->nullable();
            $table->bigInteger('supBash')->nullable();
            $table->integer('supBashRank')->nullable();
            $table->bigInteger('gesBash')->nullable();
            $table->integer('gesBashRank')->nullable();
            $table->timestamps();
            $table->primary('playerID', 'primary_playerID');
        });
    }

    public static function allyTable(World $model, $num) {
        Schema::create(BasicFunctions::getWorldDataTable($model, 'ally_'.$num), function (Blueprint $table) {
            $table->integer('allyID');
            $table->string('name', 384);
            $table->string('tag', 72);
            $table->integer('member_count');
            $table->integer('points');
            $table->integer('village_count');
            $table->integer('rank');
            $table->bigInteger('offBash')->nullable();
            $table->integer('offBashRank')->nullable();
            $table->bigInteger('defBash')->nullable();
            $table->integer('defBashRank')->nullable();
            $table->bigInteger('gesBash')->nullable();
            $table->integer('gesBashRank')->nullable();
            $table->timestamps();
            $table->index('allyID', 'primary_allyID');
        });
    }

    public static function allyLatestTable(World $model, $latestSuffix) {
        Schema::create(BasicFunctions::getWorldDataTable($model, 'ally_'.$latestSuffix), function (Blueprint $table) {
            $table->integer('allyID');
            $table->string('name', 384);
            $table->string('tag', 72);
            $table->integer('member_count');
            $table->integer('points');
            $table->integer('village_count');
            $table->integer('rank');
            $table->bigInteger('offBash')->nullable();
            $table->integer('offBashRank')->nullable();
            $table->bigInteger('defBash')->nullable();
            $table->integer('defBashRank')->nullable();
            $table->bigInteger('gesBash')->nullable();
            $table->integer('gesBashRank')->nullable();
            $table->timestamps();
            $table->primary('allyID', 'primary_allyID');
        });
    }

    public static function villageTable(World $model, $num) {
        Schema::create(BasicFunctions::getWorldDataTable($model, 'village_'.$num), function (Blueprint $table) {
            $table->integer('villageID');
            $table->string('name', 384);
            $table->integer('x');
            $table->integer('y');
            $table->integer('points');
            $table->integer('owner');
            $table->integer('bonus_id');
            $table->timestamps();
            $table->index('villageID', 'primary_villageID');
        });
    }

    public static function villageLatestTable(World $model, $latestSuffix) {
        Schema::create(BasicFunctions::getWorldDataTable($model, 'village_'.$latestSuffix), function (Blueprint $table) {
            $table->integer('villageID');
            $table->string('name', 384);
            $table->integer('x');
            $table->integer('y');
            $table->integer('points');
            $table->integer('owner');
            $table->integer('bonus_id');
            $table->timestamps();
            $table->primary('villageID', 'primary_villageID');
        });
    }

    public static function allyChangeTable(World $model) {
        Schema::create(BasicFunctions::getWorldDataTable($model, 'ally_changes'), function (Blueprint $table) {
            $table->integer('player_id');
            $table->integer('old_ally_id');
            $table->integer('new_ally_id');
            $table->integer('points');
            $table->timestamps();
        });
    }

    public static function conquerTable(World $model) {
        Schema::create(BasicFunctions::getWorldDataTable($model, 'conquer'), function (Blueprint $table) {
            $table->integer('village_id');
            $table->bigInteger('timestamp');
            $table->integer('new_owner');
            $table->integer('old_owner');
            $table->increments('id');
            $table->string('old_owner_name', 288)->nullable()->default(null);
            $table->string('new_owner_name', 288)->nullable()->default(null);
            $table->integer('old_ally')->nullable()->default(null);
            $table->integer('new_ally')->nullable()->default(null);
            $table->string('old_ally_name', 384)->nullable()->default(null);
            $table->string('new_ally_name', 384)->nullable()->default(null);
            $table->string('old_ally_tag', 72)->nullable()->default(null);
            $table->string('new_ally_tag', 72)->nullable()->default(null);
            $table->integer('points')->default(-1);
            $table->timestamps();
        });
    }
    
    public static function historyIndexTable(World $model) {
        Schema::create(BasicFunctions::getWorldDataTable($model, 'index'), function (Blueprint $table) {
            $table->increments('id');
            $table->text('date');
            $table->timestamps();
        });
    }
    
    public static function playerTopTable(World $model) {
        Schema::create(BasicFunctions::getWorldDataTable($model, 'player_top'), function (Blueprint $table) {
            $table->integer('playerID');
            $table->string('name', 288);
            $table->integer('village_count_top');
            $table->date('village_count_date');
            $table->integer('points_top');
            $table->date('points_date');
            $table->integer('rank_top');
            $table->date('rank_date');
            $table->bigInteger('offBash_top')->nullable();
            $table->date('offBash_date')->nullable();
            $table->integer('offBashRank_top')->nullable();
            $table->date('offBashRank_date')->nullable();
            $table->bigInteger('defBash_top')->nullable();
            $table->date('defBash_date')->nullable();
            $table->integer('defBashRank_top')->nullable();
            $table->date('defBashRank_date')->nullable();
            $table->bigInteger('supBash_top')->nullable();
            $table->date('supBash_date')->nullable();
            $table->integer('supBashRank_top')->nullable();
            $table->date('supBashRank_date')->nullable();
            $table->bigInteger('gesBash_top')->nullable();
            $table->date('gesBash_date')->nullable();
            $table->integer('gesBashRank_top')->nullable();
            $table->date('gesBashRank_date')->nullable();
            $table->timestamps();
            $table->primary('playerID', 'primary_playerID');
        });
    }
    
    public static function allyTopTable(World $model) {
        Schema::create(BasicFunctions::getWorldDataTable($model, 'ally_top'), function (Blueprint $table) {
            $table->integer('allyID');
            $table->string('name', 384);
            $table->string('tag', 72);
            $table->integer('member_count_top');
            $table->date('member_count_date');
            $table->integer('village_count_top');
            $table->date('village_count_date');
            $table->integer('points_top');
            $table->date('points_date');
            $table->integer('rank_top');
            $table->date('rank_date');
            $table->bigInteger('offBash_top')->nullable();
            $table->date('offBash_date')->nullable();
            $table->integer('offBashRank_top')->nullable();
            $table->date('offBashRank_date')->nullable();
            $table->bigInteger('defBash_top')->nullable();
            $table->date('defBash_date')->nullable();
            $table->integer('defBashRank_top')->nullable();
            $table->date('defBashRank_date')->nullable();
            $table->bigInteger('gesBash_top')->nullable();
            $table->date('gesBash_date')->nullable();
            $table->integer('gesBashRank_top')->nullable();
            $table->date('gesBashRank_date')->nullable();
            $table->timestamps();
            $table->primary('allyID', 'primary_allyID');
        });
    }
    
    public static function otherServersTable(Server $model) {
        Schema::create("other_servers_" . $model->code, function (Blueprint $table) {
            $table->integer('playerID');
            $table->string('name', 288);
            $table->text('worlds');
            $table->timestamps();
            $table->primary('playerID');
        });
    }
}
