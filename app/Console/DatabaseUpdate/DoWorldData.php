<?php

namespace App\Console\DatabaseUpdate;

use App\World;
use App\WorldStatistic;
use App\Notifications\DiscordNotificationQueueElement;
use App\Util\HTTPRequests;
use App\Console\DatabaseUpdate\DoAlly;
use App\Console\DatabaseUpdate\DoConquer;
use App\Console\DatabaseUpdate\DoPlayer;
use App\Console\DatabaseUpdate\DoVillage;
use App\Console\DatabaseUpdate\WorldHistory;
use Carbon\Carbon;

/**
 * Runs a given set of World Updates
 */
class DoWorldData
{
    public static function run(World $world, $parts){
        $allGood = true;
        foreach(explode(",", $parts) as $part) {
            if(!static::updateWorldData($world, $part)) {
                $allGood = false;
            }
        }
        
        $statistic = WorldStatistic::todayWorldStatistic($world);

        if($statistic) {
            $statistic->increaseDailyUpdates();
        } else {
            $statistic = new WorldStatistic();
            $statistic->world_id = $world->id;
            $statistic->daily_updates = 1;
            $statistic->save();
        }
        
        if($allGood && $world->isSpeed()) {
            //save history data every time
            WorldHistory::run($world->server->code, $world->name, true);
        }
        
        $world->worldUpdated_at = Carbon::now();
        $world->save();
    }
    
    public static function updateWorldData(World $world, $part) {
        switch ($part) {
            case "village":
            case "v":
                return DoVillage::run($world);

            case "player":
            case "p":
                return DoPlayer::run($world);

            case "ally":
            case "a":
                return DoAlly::run($world);

            case "conquer":
            case "c":
                return DoConquer::run($world);
        }
    }
    
    public static function loadGzippedFile(World $world, $name, $minTime) {
        $url = "$world->url/map/$name";
        $file = (new HTTPRequests($url))->send()->gunzipData();
        if($file->responseCode() != 200) {
            BasicFunctions::createLog("ERROR_update[{$world->server->code}{$world->name}]", "$name konnte nicht ge&ouml;ffnet werden");
            DiscordNotificationQueueElement::worldUpdate($world, $name . ' - ' . $file->responseCode(), $url);
            return false;
        }
        if($file->modificationTime()->lessThan($minTime)) {
            //This is just error spam. Maybe just send broken servers once?
            //DiscordNotificationQueueElement::worldUpdate($world, $name . ' too old - ' . $file->modificationTime(), $url);
            return false;
        }
        return explode("\n", $file->responseData());
    }
    
    public static function loadUncompressedFile(World $world, $urlEnd, $name) {
        $url = "$world->url/$urlEnd";
        $file = (new HTTPRequests($url))->send();
        if($file->responseCode() != 200) {
            BasicFunctions::createLog("ERROR_update[{$world->server->code}{$world->name}]", "$name konnte nicht ge&ouml;ffnet werden");
            DiscordNotificationQueueElement::worldUpdate($world, $name . ' - ' . $file->responseCode(), $url);
            return false;
        }
        return explode("\n", $file->responseData());
    }
}
