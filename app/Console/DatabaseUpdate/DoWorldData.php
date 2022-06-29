<?php

namespace App\Console\DatabaseUpdate;

use App\World;
use App\WorldStatistic;
use App\Notifications\DiscordNotificationQueueElement;
use App\Util\BasicFunctions;
use App\Console\DatabaseUpdate\DoAlly;
use App\Console\DatabaseUpdate\DoConquer;
use App\Console\DatabaseUpdate\DoPlayer;
use App\Console\DatabaseUpdate\DoVillage;
use App\Console\DatabaseUpdate\WorldHistory;
use Illuminate\Support\Facades\Http;
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
            WorldHistory::run($world);
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
        $response = Http::retry(3, 1000, throw: false)->withoutRedirecting()->get($url);
        if(!$response->ok()) {
            BasicFunctions::createLog("ERROR_update[" . $world->serName() . "]", "$name konnte nicht ge&ouml;ffnet werden");
            DiscordNotificationQueueElement::worldUpdate($world, $name . ' - ' . $response->status(), $url);
            return false;
        }
        if(Carbon::parse($response->header("Last-Modified"))->lessThan($minTime)) {
            //This is just error spam. Maybe just send broken servers once?
            //DiscordNotificationQueueElement::worldUpdate($world, $name . ' too old - ' . $response->header("Last-Modified"), $url);
            return false;
        }
        return explode("\n", gzdecode($response->body()));
    }
    
    public static function loadUncompressedFile(World $world, $urlEnd, $name) {
        $url = "$world->url/$urlEnd";
        $response = Http::retry(3, 1000, throw: false)->withoutRedirecting()->get($url);
        if(!$response->ok()) {
            BasicFunctions::createLog("ERROR_update[" . $world->serName() . "]", "$name konnte nicht ge&ouml;ffnet werden");
            DiscordNotificationQueueElement::worldUpdate($world, $name . ' - ' . $response->status(), $url);
            return false;
        }
        return explode("\n", $response->body());
    }
}
