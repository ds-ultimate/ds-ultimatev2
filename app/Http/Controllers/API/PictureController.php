<?php

namespace App\Http\Controllers\API;

use App\Ally;
use App\Player;
use App\Server;
use App\SpeedWorld;
use App\Village;
use App\World;
use App\Http\Controllers\Controller;
use App\Util\CacheLogger;
use App\Util\Chart;
use App\Util\ImageChart;
use App\Util\BasicFunctions;

class PictureController extends Controller
{
    private $debug = false;
    
    public function __construct()
    {
        $this->debug = config('app.debug');
    }
    
    public function getAllySizedPic($server, $world, $allyID, $type, $width, $height, $ext)
    {
        BasicFunctions::local();
        static::fixWorldName($server, $world);
        World::existWorld($server, $world);
        if (!Chart::validType($type)) {
            abort(404, "Invalid type");
        }
        
        $dim = $this->decodeDimensions($width, $height);
        $dir = storage_path(config('tools.chart.cacheDir') . "$server$world");
        $fName = "ally-$allyID-$type-{$dim["width"]}-{$dim["height"]}";
        $ret = $this->checkCache($dir, $fName, $ext);
        if($ret !== null) return $ret;
        
        $allyData = Ally::ally($server, $world, $allyID);
        if ($allyData == null) {
            abort(404, "Ally not Found");
        }
        
        $rawStatData = Ally::allyDataChart($server, $world, $allyID);
        $statData = array();
        foreach ($rawStatData as $rawData){
            $statData[$rawData->get('timestamp')] = $rawData->get($type);
        }
        
        $name = \App\Util\BasicFunctions::decodeName($allyData->name);
        $tag = \App\Util\BasicFunctions::decodeName($allyData->tag);
        $allyString = __('chart.who.ally') . ": $name [$tag]";
        
        return $this->generateChart($dim, $statData, $allyString, $type, $dir, $fName, $ext);
    }

    public function getPlayerSizedPic($server, $world, $playerID, $type, $width, $height, $ext)
    {
        BasicFunctions::local();
        static::fixWorldName($server, $world);
        World::existWorld($server, $world);
        if (!Chart::validType($type)) {
            abort(404, "Invalid type");
        }
        
        $dim = $this->decodeDimensions($width, $height);
        $dir = storage_path(config('tools.chart.cacheDir') . "$server$world");
        $fName = "player-$playerID-$type-{$dim["width"]}-{$dim["height"]}";
        $ret = $this->checkCache($dir, $fName, $ext);
        if($ret !== null) return $ret;
        
        $playerData = Player::player($server, $world, $playerID);
        if ($playerData == null) {
            abort(404, "Player not Found");
        }
        
        $rawStatData = Player::playerDataChart($server, $world, $playerID);
        $statData = array();
        foreach ($rawStatData as $rawData){
            $statData[$rawData->get('timestamp')] = $rawData->get($type);
        }
        
        $name = \App\Util\BasicFunctions::decodeName($playerData->name);
        $playerString = __('chart.who.player') . ": $name";
        
        return $this->generateChart($dim, $statData, $playerString, $type, $dir, $fName, $ext);
    }

    public function getVillageSizedPic($server, $world, $villageID, $type, $width, $height, $ext)
    {
        BasicFunctions::local();
        static::fixWorldName($server, $world);
        World::existWorld($server, $world);
        if (!Chart::validType($type)) {
            abort(404, "Invalid type");
        }
        
        $dim = $this->decodeDimensions($width, $height);
        $dir = storage_path(config('tools.chart.cacheDir') . "$server$world");
        $fName = "village-$villageID-$type-{$dim["width"]}-{$dim["height"]}";
        $ret = $this->checkCache($dir, $fName, $ext);
        if($ret !== null) return $ret;
        
        $villageData = Village::village($server, $world, $villageID);
        if ($villageData == null) {
            abort(404, "Village not Found");
        }
        
        $rawStatData = Village::villageDataChart($server, $world, $villageID);
        $statData = array();
        foreach ($rawStatData as $rawData){
            $statData[$rawData->get('timestamp')] = $rawData->get($type);
        }
        
        $name = \App\Util\BasicFunctions::decodeName($villageData->name);
        $x = $villageData->x;
        $y = $villageData->y;
        $villageString = __('chart.who.village') . ": $name ($x|$y)";
        
        return $this->generateChart($dim, $statData, $villageString, $type, $dir, $fName, $ext);
    }
    
    public function getAllyPic($server, $world, $allyID, $type, $ext)
    {
        return $this->getAllySizedPic($server, $world, $allyID, $type, null, null, $ext);
    }

    public function getPlayerPic($server, $world, $playerID, $type, $ext)
    {
        return $this->getPlayerSizedPic($server, $world, $playerID, $type, null, null, $ext);
    }

    public function getVillagePic($server, $world, $villageID, $type, $ext)
    {
        return $this->getVillageSizedPic($server, $world, $villageID, $type, null, null, $ext);
    }
    
    private function generateChart($dim, $statData, $nameString, $type, $dir, $fName, $ext) {
        $chart = new ImageChart("fonts/NotoMono-Regular.ttf", $dim, $this->debug);
        $chart->render($statData, $nameString, Chart::chartTitel($type), Chart::displayInvers($type));
        
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $chart->saveTo("$dir/$fName", $ext);
        return response()->file("$dir/$fName.$ext");
    }
    
    private function checkCache($dir, $fName, $ext) {
        $tmp = "$dir/$fName.$ext";
        if(!file_exists($tmp) || (time() - filemtime($tmp)) > config("tools.chart.cacheDuration")) {
            CacheLogger::logMiss(CacheLogger::$PICTURE_TYPE, $fName.$ext);
            return null;
        }
        CacheLogger::logHit(CacheLogger::$PICTURE_TYPE, $fName.$ext);
        return response()->file($tmp);
    }

    private function decodeDimensions($width, $height)
    {
        if($width == 'w') {
            $retArr = [
                'width' => intval($height),
                'height' => intval($height / ImageChart::$STD_ASPECT),
            ];
        } else if($width == 'h') {
            $retArr = [
                'width' => intval($height * ImageChart::$STD_ASPECT),
                'height' => intval($height),
            ];
        } else if($width !== null && $height !== null) {
            $retArr = [
                'width' => intval($width),
                'height' => intval($height),
            ];
        } else {
            $retArr = [
                'width' => intval(ImageChart::$STD_HEIGHT * ImageChart::$STD_ASPECT),
                'height' => intval(ImageChart::$STD_HEIGHT),
            ];
        }
        
        if(isset($retArr['width']) && $retArr['width'] <= 0) {
            abort(404, "Width too small");
        }
        
        if(isset($retArr['height']) && $retArr['height'] <= 0) {
            abort(404, "Height too small");
        }
        return $retArr;
    }
    
    public static function fixWorldName(&$server, &$world) {
        if(strlen($server) == 3 && strlen($world) == 2) {
            $serWorld = $server . $world;
            $server = substr($serWorld, 0, 2);
            $world = substr($serWorld, 2, 3);
        }
        
        if(World::isSpecialServerName($world)) {
            //find first speed world with correct update url and use that?
            $serverData = Server::getServerByCode($server);
            $model = new SpeedWorld();
            $first = $model
                ->where("server_id", $serverData->id)
                ->where("instance", $world)
                ->where("started", 1)
                ->first();
            
            if($first == null) {
                abort(404, "Aktuell läuft auf der Welt '$server$world' keine Runde.");
            }
            $world = $first->world->name;
        }
    }
}
