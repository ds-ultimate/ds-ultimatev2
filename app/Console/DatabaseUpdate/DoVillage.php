<?php

namespace App\Console\DatabaseUpdate;

use App\Util\BasicFunctions;
use App\Village;
use App\World;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DoVillage
{
    public static function run(World $world){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1800M');
        $minTime = Carbon::now()->subHour()->subMinutes(5);
        
        $liveTbl = BasicFunctions::getWorldDataTable($world, "village_latest");
        $tmpTbl = BasicFunctions::getWorldDataTable($world, "village_latest_temp");

        Schema::dropIfExists($tmpTbl);
        if (BasicFunctions::hasWorldDataTable($world, 'village_latest_temp') === false) {
            TableGenerator::villageLatestTable($world, 'latest_temp');
        }

        $lines = DoWorldData::loadGzippedFile($world, "village.txt.gz", $minTime);
        if($lines === false) return false;
        
        $villages = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if($line == "") continue;
            list($id, $name, $x, $y, $owner, $points, $bonus_id) = explode(',', $line);
            $village = [
                'id' => (int)$id,
                'name' => $name,
                'x' => (int) $x,
                'y' => (int) $y,
                'points' => (int) $points,
                'owner' => (int) $owner,
                'bonus_id' => (int) $bonus_id,
            ];
            $villages[$village['id']] = $village;
        }

        $insert = new Village($world, 'village_latest_temp');
        $array = array();
        $insertTime = Carbon::now();
        
        foreach ($villages as $village) {
            $data = [
                'villageID' => $village['id'],
                'name' => $village['name'],
                'x' => $village['x'],
                'y' => $village['y'],
                'points' => $village['points'],
                'owner' => $village['owner'],
                'bonus_id' => $village['bonus_id'],
                'created_at' => $insertTime,
                'updated_at' => $insertTime,
            ];
            $array [] = $data;
        }
        foreach (array_chunk($array, 3000) as $t) {
            $insert->insert($t);
        }

        $villageDB = static::prepareVillageChangeCheck($world);
        Schema::dropIfExists($liveTbl);
        DB::statement("ALTER TABLE $tmpTbl RENAME TO $liveTbl");

        $hashVillage = UpdateUtil::hashTable($array, $world->hash_village, 'villageID', array(static::class, 'villageSameSinceLast'), $villageDB);
        for ($i = 0; $i < $world->hash_village; $i++) {
            if (array_key_exists($i, $hashVillage)) {
                if (BasicFunctions::hasWorldDataTable($world, 'village_' . $i) === false) {
                    TableGenerator::villageTable($world, $i);
                }
                $insert->setTable(BasicFunctions::getWorldDataTable($world, 'village_' . $i));
                foreach (array_chunk($hashVillage[$i], 3000) as $t) {
                    $insert->insert($t);
                }
            }
        }
        $count = count($array);

        $world->village_count = $count;
        return true;
    }
    
    private static function prepareVillageChangeCheck(World $world) {
        if(!BasicFunctions::hasWorldDataTable($world, 'village_latest')) {
            return array();
        }
        $villageModel = new Village($world);

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
