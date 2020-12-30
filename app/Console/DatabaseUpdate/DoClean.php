<?php

namespace App\Console\DatabaseUpdate;

use App\Ally;
use App\Player;
use App\Util\BasicFunctions;
use App\Village;
use Carbon\Carbon;

class DoClean
{
    public static function run(){
        $dbName = BasicFunctions::getDatabaseName($world->server->code, $world->name);
        switch($type) {
            case 'a':
                $envHashIndex = 'hash_ally';
                $tablePrefix = 'ally';
                $model = new Ally();
                break;

            case 'p':
                $envHashIndex = 'hash_player';
                $tablePrefix = 'player';
                $model = new Player();
                break;

            case 'v':
                $envHashIndex = 'hash_village';
                $tablePrefix = 'village';
                $model = new Village();
                break;
        }

        for ($i = 0; $i < config('dsUltimate.'.$envHashIndex); $i++){
            if (BasicFunctions::existTable($dbName, "{$tablePrefix}_{$i}") === true) {
                $model->setTable("$dbName.{$tablePrefix}_{$i}");
                $delete = $model->where('updated_at', '<', Carbon::createFromTimestamp(time() - (60 * 60 * 24) * config('dsUltimate.db_save_day')));
                $delete->delete();
            }
        }
        $world->worldCleaned_at = Carbon::createFromTimestamp(time());
        $world->save();
    }
}
