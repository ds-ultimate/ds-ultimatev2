<?php

namespace App\Console\DatabaseUpdate;

use App\Ally;
use App\Util\BasicFunctions;
use App\World;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DoAlly
{
    public static function run(World $world){
        ini_set('max_execution_time', 1800);
        ini_set('memory_limit', '1500M');
        $dbName = BasicFunctions::getDatabaseName($world->server->code, $world->name);
        $minTime = Carbon::now()->subHour()->subMinutes(5);

        Schema::dropIfExists("$dbName.ally_latest_temp");
        if (BasicFunctions::existTable($dbName, 'ally_latest_temp') === false){
            TableGenerator::allyLatestTable($dbName, 'latest_temp');
        }

        $lines = DoWorldData::loadGzippedFile($world, "ally.txt.gz", $minTime);
        if($lines === false) return false;

        $allys = collect();
        $allyOffs = collect();
        $allyDefs = collect();
        $allyTots = collect();

        foreach ($lines as $line){
            $line = trim($line);
            if($line == "") continue;
            list($id, $name, $tag, $members, $villages, $points, $points_all, $rank) = explode(',', $line);
            $ally = collect();
            $ally->put('id', (int)$id);
            $ally->put('name', $name);
            $ally->put('tag', $tag);
            $ally->put('member_count', (int)$members);
            $ally->put('points', (int)$points_all);
            $ally->put('village_count', (int)$villages);
            $ally->put('rank', (int)$rank);
            $allys->put($ally->get('id'),$ally);
        }

        $offs = DoWorldData::loadGzippedFile($world, "kill_att_tribe.txt.gz", $minTime);
        if($offs === false) return false;
        foreach ($offs as $off){
            $off = trim($off);
            if($off == "") continue;
            list($rank, $id, $kills) = explode(',', $off);
            $allyOff = collect();
            $allyOff->put('offRank', (int)$rank);
            $allyOff->put('off', (int)$kills);
            $allyOffs->put($id, $allyOff);

        }

        $defs = DoWorldData::loadGzippedFile($world, "kill_def_tribe.txt.gz", $minTime);
        if($defs === false) return false;
        foreach ($defs as $def){
            $def = trim($def);
            if($def == "") continue;
            list($rank, $id, $kills) = explode(',', $def);
            $allyDef = collect();
            $allyDef->put('defRank', (int)$rank);
            $allyDef->put('def', (int)$kills);
            $allyDefs->put($id, $allyDef);
        }

        $tots = DoWorldData::loadGzippedFile($world, "kill_def_tribe.txt.gz", $minTime);
        if($tots === false) return false;
        foreach ($tots as $tot){
            $tot = trim($tot);
            if($tot == "") continue;
            list($rank, $id, $kills) = explode(',', $tot);
            $allyTot = collect();
            $allyTot->put('totRank', (int)$rank);
            $allyTot->put('tot', (int)$kills);
            $allyTots->put($id, $allyTot);
        }

        $insert = new Ally();
        $insert->setTable($dbName.'.ally_latest_temp');
        $array = array();
        $insertTime = Carbon::now();
        
        foreach ($allys as $ally) {
            $id = $ally->get('id');
            $data = [
                'allyID' => $ally->get('id'),
                'name' => $ally->get('name'),
                'tag' => $ally->get('tag'),
                'member_count' => $ally->get('member_count'),
                'points' => $ally->get('points'),
                'village_count' => $ally->get('village_count'),
                'rank' => $ally->get('rank'),
                'offBash' => (is_null($allyOffs->get($id)))? 0 :$allyOffs->get($id)->get('off'),
                'offBashRank' => (is_null($allyOffs->get($id)))? null : $allyOffs->get($id)->get('offRank'),
                'defBash' => (is_null($allyDefs->get($id)))? 0 : $allyDefs->get($id)->get('def'),
                'defBashRank' => (is_null($allyDefs->get($id)))? null : $allyDefs->get($id)->get('defRank'),
                'gesBash' => (is_null($allyTots->get($id)))? 0 : $allyTots->get($id)->get('tot'),
                'gesBashRank' => (is_null($allyTots->get($id)))? null : $allyTots->get($id)->get('totRank'),
                'created_at' => $insertTime,
                'updated_at' => $insertTime,
            ];
            $array []= $data;
        }
        foreach (array_chunk($array,3000) as $t){
            $insert->insert($t);
        }


        Schema::dropIfExists("$dbName.ally_latest");
        DB::statement("ALTER TABLE $dbName.ally_latest_temp RENAME TO $dbName.ally_latest");

        $hashAlly = UpdateUtil::hashTable($array, 'a', 'allyID');

        for ($i = 0; $i < config('dsUltimate.hash_ally'); $i++){
            if (array_key_exists($i ,$hashAlly)) {
                if (BasicFunctions::existTable($dbName, 'ally_' . $i) === false) {
                    TableGenerator::allyTable($dbName, $i);
                }
                $insert->setTable($dbName . '.ally_' . $i);
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
