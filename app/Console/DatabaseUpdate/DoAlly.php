<?php

namespace App\Console\DatabaseUpdate;

use App\Ally;
use App\Util\BasicFunctions;
use App\World;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class DoAlly
{
    public static function run(World $world){
        ini_set('max_execution_time', 1800);
        ini_set('memory_limit', '1800M');
        $minTime = Carbon::now()->subHour()->subMinutes(5);
        
        $liveTbl = BasicFunctions::getWorldDataTable($world, "ally_latest");
        $tmpTbl = BasicFunctions::getWorldDataTable($world, "ally_latest_temp");

        Schema::dropIfExists($tmpTbl);
        if (BasicFunctions::hasWorldDataTable($world, 'ally_latest_temp') === false){
            TableGenerator::allyLatestTable($world, 'latest_temp');
        }

        $lines = DoWorldData::loadGzippedFile($world, "ally.txt.gz", $minTime);
        if($lines === false) return false;

        $allys = [];
        $allyOffs = [];
        $allyDefs = [];
        $allyTots = [];

        foreach ($lines as $line){
            $line = trim($line);
            if($line == "") continue;
            list($id, $name, $tag, $members, $villages, $points, $points_all, $rank) = explode(',', $line);
            $ally = [
                'id' => (int)$id,
                'name' => $name,
                'tag' => $tag,
                'member_count' => (int)$members,
                'points' => min((int)$points_all, 2147483647),
                'village_count' => (int)$villages,
                'rank' => (int)$rank,
            ];
            $allys[$ally['id']] = $ally;
        }

        $offs = DoWorldData::loadGzippedFile($world, "kill_att_tribe.txt.gz", $minTime);
        if($offs === false) return false;
        foreach ($offs as $off){
            $off = trim($off);
            if($off == "") continue;
            list($rank, $id, $kills) = explode(',', $off);
            $allyOffs[$id] = [
                'offRank' => (int) $rank,
                'off' => (int) $kills,
            ];
        }

        $defs = DoWorldData::loadGzippedFile($world, "kill_def_tribe.txt.gz", $minTime);
        if($defs === false) return false;
        foreach ($defs as $def){
            $def = trim($def);
            if($def == "") continue;
            list($rank, $id, $kills) = explode(',', $def);
            $allyDefs[$id] = [
                'defRank' => (int) $rank,
                'def' => (int) $kills,
            ];
        }

        $tots = DoWorldData::loadGzippedFile($world, "kill_all_tribe.txt.gz", $minTime);
        if($tots === false) return false;
        foreach ($tots as $tot){
            $tot = trim($tot);
            if($tot == "") continue;
            list($rank, $id, $kills) = explode(',', $tot);
            $allyTots[$id] = [
                'totRank' => (int) $rank,
                'tot' => (int) $kills,
            ];
        }

        $insert = new Ally($world, 'ally_latest_temp');
        $array = array();
        $insertTime = Carbon::now();
        
        foreach ($allys as $ally) {
            $id = $ally['id'];
            $data = [
                'allyID' => $ally['id'],
                'name' => $ally['name'],
                'tag' => $ally['tag'],
                'member_count' => $ally['member_count'],
                'points' => $ally['points'],
                'village_count' => $ally['village_count'],
                'rank' => $ally['rank'],
                'offBash' => (isset($allyOffs[$id]))? $allyOffs[$id]['off'] : 0,
                'offBashRank' => (isset($allyOffs[$id]))? $allyOffs[$id]['offRank'] : null,
                'defBash' => (isset($allyDefs[$id]))? $allyDefs[$id]['def'] : 0,
                'defBashRank' => (isset($allyDefs[$id]))? $allyDefs[$id]['defRank'] : null,
                'gesBash' => (isset($allyTots[$id]))? $allyTots[$id]['tot'] : 0,
                'gesBashRank' => (isset($allyTots[$id]))? $allyTots[$id]['totRank'] : null,
                'created_at' => $insertTime,
                'updated_at' => $insertTime,
            ];
            $array []= $data;
        }
        foreach (array_chunk($array,3000) as $t){
            $insert->insert($t);
        }
        DoWorldData::moveTableData($tmpTbl, $liveTbl);
        
        $hashAlly = UpdateUtil::hashTable($array, $world->hash_ally, 'allyID');

        for ($i = 0; $i < $world->hash_ally; $i++){
            if (array_key_exists($i ,$hashAlly)) {
                if (BasicFunctions::hasWorldDataTable($world, 'ally_' . $i) === false) {
                    TableGenerator::allyTable($world, $i);
                }
                $insert->setTable(BasicFunctions::getWorldDataTable($world, 'ally_' . $i));
                foreach (array_chunk($hashAlly[$i], 3000) as $t) {
                    $insert->insert($t);
                }
            }
        }

        $count = count($array);
        $world->ally_count = $count;
        return true;
    }
}
