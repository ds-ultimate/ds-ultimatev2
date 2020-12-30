<?php

namespace App\Console\DatabaseUpdate;

use App\Log;
use App\Notifications\DiscordNotification;
use App\Player;
use App\AllyChanges;
use App\Util\BasicFunctions;
use App\World;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;

class DoPlayer
{
    public static function run($server, $world){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '600M');
        $dbName = BasicFunctions::getDatabaseName($server, $world);
        $worldUpdate = World::getWorld($server, $world);

        Schema::dropIfExists("$dbName.player_latest_temp");
        if (BasicFunctions::existTable($dbName, 'player_latest_temp') === false){
            TableGenerator::playerLatestTable($dbName, 'latest_temp');
        }

        if (BasicFunctions::existTable($dbName, 'ally_changes') === false){
            TableGenerator::allyChangeTable($dbName);
        }

        if (BasicFunctions::existTable($dbName, 'player_latest') === false){
            TableGenerator::playerLatestTable($dbName, 'latest');
        }

        $lines = gzfile("$worldUpdate->url/map/player.txt.gz");
        if(!is_array($lines)) {
            BasicFunctions::createLog("ERROR_update[$server$world]", "player.txt.gz konnte nicht ge&ouml;ffnet werden");
            $input =[
                'world' => $worldUpdate,
                'file' => 'player.txt',
                'url' => $worldUpdate->url.'/map/player.txt'
            ];
            Notification::send(new Log(), new DiscordNotification('worldUpdate', null, $input));
            return;
        }

        $players = collect();
        $playerOffs = collect();
        $playerDefs = collect();
        $playerSups = collect();
        $playerTots = collect();

        foreach ($lines as $line){
            list($id, $name, $ally, $villages, $points, $rank) = explode(',', $line);
            $player = collect();
            $player->put('id', (int)$id);
            $player->put('name', $name);
            $player->put('ally', (int)$ally);
            $player->put('villages', (int)$villages);
            $player->put('points', (int)$points);
            $player->put('rank', (int)$rank);
            $players->put($player->get('id'),$player);
        }

        $offs = gzfile("$worldUpdate->url/map/kill_att.txt.gz");
        if(!is_array($offs)) {
            BasicFunctions::createLog("ERROR_update[$server$world]", "kill_att.txt.gz konnte nicht ge&ouml;ffnet werden");
            $input =[
                'world' => $worldUpdate,
                'file' => 'kill_att.txt',
                'url' => $worldUpdate->url.'/map/kill_att.txt'
            ];
            Notification::send(new Log(), new DiscordNotification('worldUpdate', null, $input));
            return;
        }
        foreach ($offs as $off){
            list($rank, $id, $kills) = explode(',', $off);
            $playerOff = collect();
            $playerOff->put('offRank', (int)$rank);
            $playerOff->put('off', (int)$kills);
            $playerOffs->put($id, $playerOff);
        }

        $defs = gzfile("$worldUpdate->url/map/kill_def.txt.gz");
        if(!is_array($defs)) {
            BasicFunctions::createLog("ERROR_update[$server$world]", "kill_def.txt.gz konnte nicht ge&ouml;ffnet werden");
            $input =[
                'world' => $worldUpdate,
                'file' => 'kill_def.txt',
                'url' => $worldUpdate->url.'/map/kill_def.txt'
            ];
            Notification::send(new Log(), new DiscordNotification('worldUpdate', null, $input));
            return;
        }
        foreach ($defs as $def){
            list($rank, $id, $kills) = explode(',', $def);
            $playerDef = collect();
            $playerDef->put('defRank', (int)$rank);
            $playerDef->put('def', (int)$kills);
            $playerDefs->put($id, $playerDef);
        }

        $sups = gzfile("$worldUpdate->url/map/kill_sup.txt.gz");
        if(!is_array($defs)) {
            BasicFunctions::createLog("ERROR_update[$server$world]", "kill_sup.txt.gz konnte nicht ge&ouml;ffnet werden");
            $input =[
                'world' => $worldUpdate,
                'file' => 'kill_sup.txt',
                'url' => $worldUpdate->url.'/map/kill_sup.txt'
            ];
            Notification::send(new Log(), new DiscordNotification('worldUpdate', null, $input));
            return;
        }
        foreach ($sups as $sup){
            list($rank, $id, $kills) = explode(',', $sup);
            $playerSup = collect();
            $playerSup->put('supRank', (int)$rank);
            $playerSup->put('sup', (int)$kills);
            $playerSups->put($id, $playerSup);
        }

        $tots = gzfile("$worldUpdate->url/map/kill_all.txt.gz");
        if(!is_array($tots)) {
            BasicFunctions::createLog("ERROR_update[$server$world]", "kill_all.txt.gz konnte nicht ge&ouml;ffnet werden");
            $input =[
                'world' => $worldUpdate,
                'file' => 'kill_all.txt',
                'url' => $worldUpdate->url.'/map/kill_all.txt'
            ];
            Notification::send(new Log(), new DiscordNotification('worldUpdate', null, $input));
            return;
        }
        foreach ($tots as $tot){
            list($rank, $id, $kills) = explode(',', $tot);
            $playerTot = collect();
            $playerTot->put('totRank', (int)$rank);
            $playerTot->put('tot', (int)$kills);
            $playerTots->put($id, $playerTot);
        }


        $playerChange = new Player();
        $playerChange->setTable($dbName . '.player_latest');
        $databasePlayer = array();
        foreach ($playerChange->get() as $player) {
            $databasePlayer[$player->playerID] = $player->ally_id;
        }

        $insert = new Player();
        $insert->setTable($dbName.'.player_latest_temp');
        $arrayAllyChange = array();
        $insertTime = Carbon::createFromTimestamp(time());
        
        foreach ($players as $player) {
            $id = $player->get('id');
            $dataPlayer = [
                'playerID' => $player->get('id'),
                'name' => $player->get('name'),
                'ally_id' => $player->get('ally'),
                'village_count' => $player->get('villages'),
                'points' => $player->get('points'),
                'rank' => $player->get('rank'),
                'offBash' => (is_null($playerOffs->get($id)))? 0 :$playerOffs->get($id)->get('off'),
                'offBashRank' => (is_null($playerOffs->get($id)))? null : $playerOffs->get($id)->get('offRank'),
                'defBash' => (is_null($playerDefs->get($id)))? 0 : $playerDefs->get($id)->get('def'),
                'defBashRank' => (is_null($playerDefs->get($id)))? null : $playerDefs->get($id)->get('defRank'),
                'supBash' => (is_null($playerSups->get($id)))? 0 : $playerSups->get($id)->get('sup'),
                'supBashRank' => (is_null($playerSups->get($id)))? null : $playerSups->get($id)->get('supRank'),
                'gesBash' => (is_null($playerTots->get($id)))? 0 : $playerTots->get($id)->get('tot'),
                'gesBashRank' => (is_null($playerTots->get($id)))? null : $playerTots->get($id)->get('totRank'),
                'created_at' => $insertTime,
                'updated_at' => $insertTime,
            ];
            $arrayPlayer []= $dataPlayer;

            if((isset($databasePlayer[$player->get('id')]) && $databasePlayer[$player->get('id')] != $player->get('ally')) ||
                    (!isset($databasePlayer[$player->get('id')]) && $player->get('ally') != 0)) {
                $arrayAllyChange[] = [
                    'player_id' => $player->get('id'),
                    'old_ally_id' => $databasePlayer[$player->get('id')] ?? 0,
                    'new_ally_id' => $player->get('ally'),
                    'points' => $player->get('points'),
                    'created_at' => $insertTime,
                    'updated_at' => $insertTime,
                ];
            }
        }

        foreach (array_chunk($arrayPlayer,3000) as $t){
            $insert->insert($t);
        }

        $allyChangeModel = new AllyChanges();
        $allyChangeModel->setTable($dbName.'.ally_changes');
        foreach (array_chunk($arrayAllyChange,3000) as $t){
            $allyChangeModel->insert($t);
        }

        Schema::dropIfExists("$dbName.player_latest");
        DB::statement("ALTER TABLE $dbName.player_latest_temp RENAME TO $dbName.player_latest");

        $hashPlayer = UpdateUtil::hashTable($arrayPlayer, 'p', 'playerID');

        for ($i = 0; $i < config('dsUltimate.hash_player'); $i++){
            if (array_key_exists($i ,$hashPlayer)) {
                if (BasicFunctions::existTable($dbName, 'player_' . $i) === false) {
                    TableGenerator::playerTable($dbName, $i);
                }
                $insert->setTable($dbName . '.player_' . $i);
                foreach (array_chunk($hashPlayer[$i], 3000) as $t) {
                    $insert->insert($t);
                }
            }
        }

        $count = count($arrayPlayer);

        $worldUpdate->player_count = $count;
        $worldUpdate->worldUpdated_at = $insertTime;
        $worldUpdate->save();
    }
}
