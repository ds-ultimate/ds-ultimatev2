<?php

namespace App\Console\DatabaseUpdate;

use App\Log;
use App\Notifications\DiscordNotification;
use App\Util\BasicFunctions;
use App\Village;
use App\World;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;

class DoVillage
{
    public static function run($server, $world){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1800M');
        $dbName = BasicFunctions::getDatabaseName($server, $world);
        $worldUpdate = World::getWorld($server, $world);

        Schema::dropIfExists("$dbName.village_latest_temp");
        if (BasicFunctions::existTable($dbName, 'village_latest_temp') === false) {
            TableGenerator::villageLatestTable($dbName, 'latest_temp');
        }

        $lines = gzfile("$worldUpdate->url/map/village.txt.gz");
        if (!is_array($lines)) {
            BasicFunctions::createLog("ERROR_update[$server$world]", "village.txt.gz konnte nicht ge&ouml;ffnet werden");
            $input =[
                'world' => $worldUpdate,
                'file' => 'village.txt',
                'url' => $worldUpdate->url.'/map/village.txt'
            ];
            Notification::send(new Log(), new DiscordNotification('worldUpdate', null, $input));
            return;
        }
        $villages = collect();
        foreach ($lines as $line) {
            list($id, $name, $x, $y, $owner, $points, $bonus_id) = explode(',', $line);
            $village = collect();
            $village->put('id', (int)$id);
            $village->put('name', $name);
            $village->put('x', (int)$x);
            $village->put('y', (int)$y);
            $village->put('points', (int)$points);
            $village->put('owner', (int)$owner);
            $village->put('bonus_id', (int)$bonus_id);
            $villages->put($village->get('id'), $village);
        }

        $insert = new Village();
        $insert->setTable($dbName . '.village_latest_temp');
        $array = array();
        $insertTime = Carbon::now();
        
        foreach ($villages as $village) {
            $data = [
                'villageID' => $village->get('id'),
                'name' => $village->get('name'),
                'x' => $village->get('x'),
                'y' => $village->get('y'),
                'points' => $village->get('points'),
                'owner' => $village->get('owner'),
                'bonus_id' => $village->get('bonus_id'),
                'created_at' => $insertTime,
                'updated_at' => $insertTime,
            ];
            $array [] = $data;
        }
        foreach (array_chunk($array, 3000) as $t) {
            $insert->insert($t);
        }

        $villageDB = static::prepareVillageChangeCheck($dbName);
        Schema::dropIfExists("$dbName.village_latest");
        DB::statement("ALTER TABLE $dbName.village_latest_temp RENAME TO $dbName.village_latest");

        $hashVillage = UpdateUtil::hashTable($array, 'v', 'villageID', array(static::class, 'villageSameSinceLast'), $villageDB);
        for ($i = 0; $i < config('dsUltimate.hash_village'); $i++) {
            if (array_key_exists($i, $hashVillage)) {
                if (BasicFunctions::existTable($dbName, 'village_' . $i) === false) {
                    TableGenerator::villageTable($dbName, $i);
                }
                $insert->setTable($dbName . '.village_' . $i);
                foreach (array_chunk($hashVillage[$i], 3000) as $t) {
                    $insert->insert($t);
                }
            }
        }

        $count = count($array);

        $worldUpdate->village_count = $count;
        $worldUpdate->worldUpdated_at = $insertTime;
        $worldUpdate->save();
    }
    
    private static function prepareVillageChangeCheck($dbName) {
        if(!BasicFunctions::existTable($dbName, 'village_latest')) {
            return array();
        }
        $villageModel = new Village();
        $villageModel->setTable($dbName . '.village_latest');

        $arrVil = array();
        foreach ($villageModel->get() as $village) {
            $arrVil[$village->villageID] = array($village->name, $village->points, $village->owner);
        }
        return $arrVil;
    }

    /**
     *
     * @param type $arrVil
     * @param type $data
     * @return boolean true if same is already inside Database
     */
    public static function villageSameSinceLast($arrVil, $data) {
        if(!isset($arrVil[$data['villageID']])) return false;

        $possible_dup = $arrVil[$data['villageID']];
        if($possible_dup[0] == $data['name'] &&
                $possible_dup[1] == $data['points'] &&
                $possible_dup[2] == $data['owner']) {
            return true;
        }

        return false;
    }

}
