<?php

namespace App\Console\DatabaseUpdate;

use App\Player;
use App\AllyChanges;
use App\Util\BasicFunctions;
use App\World;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DoPlayer
{
    public static function run(World $world){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1800M');
        $minTime = Carbon::now()->subHour()->subMinutes(5);
        
        $liveTbl = BasicFunctions::getWorldDataTable($world, "player_latest");
        $tmpTbl = BasicFunctions::getWorldDataTable($world, "player_latest_temp");

        Schema::dropIfExists($tmpTbl);
        if (BasicFunctions::hasWorldDataTable($world, 'player_latest_temp') === false){
            TableGenerator::playerLatestTable($world, 'latest_temp');
        }

        if (BasicFunctions::hasWorldDataTable($world, 'ally_changes') === false){
            TableGenerator::allyChangeTable($world);
        }

        if (BasicFunctions::hasWorldDataTable($world, 'player_latest') === false){
            TableGenerator::playerLatestTable($world, 'latest');
        }
        
        $lines = DoWorldData::loadGzippedFile($world, "player.txt.gz", $minTime);
        if($lines === false) return false;

        $players = [];
        $playerOffs = [];
        $playerDefs = [];
        $playerSups = [];
        $playerTots = [];

        foreach($lines as $line) {
            $line = trim($line);
            if($line == "") continue;
            list($id, $name, $ally, $villages, $points, $rank) = explode(',', $line);
            $player = [
                'id' => (int)$id,
                'name' => $name,
                'ally' => (int)$ally,
                'villages' => (int)$villages,
                'points' => (int)$points,
                'rank' => (int)$rank,
            ];
            $players[$player['id']] = $player;
        }
        
        $offs = DoWorldData::loadGzippedFile($world, "kill_att.txt.gz", $minTime);
        if($offs === false) return false;
        foreach($offs as $off) {
            $off = trim($off);
            if($off == "") continue;
            list($rank, $id, $kills) = explode(',', $off);
            $playerOffs[$id] = [
                'offRank' => (int) $rank,
                'off' => (int) $kills,
            ];
        }

        $defs = DoWorldData::loadGzippedFile($world, "kill_def.txt.gz", $minTime);
        if($defs === false) return false;
        foreach($defs as $def) {
            $def = trim($def);
            if($def == "") continue;
            list($rank, $id, $kills) = explode(',', $def);
            $playerDefs[$id] = [
                'defRank' => (int) $rank,
                'def' => (int) $kills,
            ];
        }

        $sups = DoWorldData::loadGzippedFile($world, "kill_sup.txt.gz", $minTime);
        if($sups === false) return false;
        foreach($sups as $sup) {
            $sup = trim($sup);
            if($sup == "") continue;
            list($rank, $id, $kills) = explode(',', $sup);
            $playerSups[$id] = [
                'supRank' => (int) $rank,
                'sup' => (int) $kills,
            ];
        }

        $tots = DoWorldData::loadGzippedFile($world, "kill_all.txt.gz", $minTime);
        if($tots === false) return false;
        foreach($tots as $tot) {
            $tot = trim($tot);
            if($tot == "") continue;
            list($rank, $id, $kills) = explode(',', $tot);
            $playerTots[$id] = [
                'totRank' => (int) $rank,
                'tot' => (int) $kills,
            ];
        }
        
        
        $playerChange = new Player($world);
        $databasePlayer = [];
        foreach ($playerChange->get() as $player) {
            $databasePlayer[$player->playerID] = $player->ally_id;
        }

        $insert = new Player($world, 'player_latest_temp');
        $arrayAllyChange = [];
        $insertTime = Carbon::now();
        $arrayPlayer = [];
        
        foreach ($players as $player) {
            $id = $player['id'];
            $dataPlayer = [
                'playerID' => $player['id'],
                'name' => $player['name'],
                'ally_id' => $player['ally'],
                'village_count' => $player['villages'],
                'points' => $player['points'],
                'rank' => $player['rank'],
                'offBash' => (isset($playerOffs[$id]))? $playerOffs[$id]['off'] : 0,
                'offBashRank' => (isset($playerOffs[$id]))? $playerOffs[$id]['offRank'] : null,
                'defBash' => (isset($playerDefs[$id]))? $playerDefs[$id]['def'] : 0,
                'defBashRank' => (isset($playerDefs[$id]))? $playerDefs[$id]['defRank'] : null,
                'supBash' => (isset($playerSups[$id]))? $playerSups[$id]['sup'] : 0,
                'supBashRank' => (isset($playerSups[$id]))? $playerSups[$id]['supRank'] : null,
                'gesBash' => (isset($playerTots[$id]))? $playerTots[$id]['tot'] : 0,
                'gesBashRank' => (isset($playerTots[$id]))? $playerTots[$id]['totRank'] : null,
                'created_at' => $insertTime,
                'updated_at' => $insertTime,
            ];
            $arrayPlayer []= $dataPlayer;

            if((isset($databasePlayer[$player['id']]) && $databasePlayer[$player['id']] != $player['ally']) ||
                    (!isset($databasePlayer[$player['id']]) && $player['ally'] != 0)) {
                $arrayAllyChange[] = [
                    'player_id' => $player['id'],
                    'old_ally_id' => $databasePlayer[$player['id']] ?? 0,
                    'new_ally_id' => $player['ally'],
                    'points' => $player['points'],
                    'created_at' => $insertTime,
                    'updated_at' => $insertTime,
                ];
            }
        }

        foreach (array_chunk($arrayPlayer,3000) as $t){
            $insert->insert($t);
        }

        $allyChangeModel = new AllyChanges($world);
        foreach (array_chunk($arrayAllyChange,3000) as $t){
            $allyChangeModel->insert($t);
        }

        DoWorldData::moveTableData($tmpTbl, $liveTbl);

        $hashPlayer = UpdateUtil::hashTable($arrayPlayer, $world->hash_player, 'playerID');

        for ($i = 0; $i < $world->hash_player; $i++){
            if (array_key_exists($i ,$hashPlayer)) {
                if (BasicFunctions::hasWorldDataTable($world, 'player_' . $i) === false) {
                    TableGenerator::playerTable($world, $i);
                }
                $insert->setTable(BasicFunctions::getWorldDataTable($world, 'player_' . $i));
                foreach (array_chunk($hashPlayer[$i], 3000) as $t) {
                    $insert->insert($t);
                }
            }
        }

        $count = count($arrayPlayer);
        $world->player_count = $count;
        return true;
    }
}
