<?php

namespace App\Http\Controllers\API;

use App\Ally;
use App\Player;
use App\Server;
use App\Village;
use App\World;
use App\Http\Controllers\Controller;
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
        $world = $this->fixWorldNameSpeed($server, $world);
        if (!Chart::validType($type)) {
            abort(400, "Invalid type");
        }
        
        $allyData = Ally::ally($server, $world, $allyID);
        if ($allyData == null) {
            abort(400, "Ally not Found");
        }
        
        $rawStatData = Ally::allyDataChart($server, $world, $allyID);
        $statData = array();
        foreach ($rawStatData as $rawData){
            $statData[$rawData->get('timestamp')] = $rawData->get($type);
        }
        
        $name = \App\Util\BasicFunctions::decodeName($allyData->name);
        $tag = \App\Util\BasicFunctions::decodeName($allyData->tag);
        $allyString = __('chart.who.ally') . ": $name [$tag]";
        
        $chart = new ImageChart("fonts/NotoMono-Regular.ttf", $this->decodeDimensions($width, $height), $this->debug);
        $chart -> render($statData, $allyString, Chart::chartTitel($type), Chart::displayInvers($type));
        return $chart -> output($ext);
    }

    public function getPlayerSizedPic($server, $world, $playerID, $type, $width, $height, $ext)
    {
        BasicFunctions::local();
        $world = $this->fixWorldNameSpeed($server, $world);
        if (!Chart::validType($type)) {
            abort(400, "Invalid type");
        }
        
        $playerData = Player::player($server, $world, $playerID);
        if ($playerData == null) {
            abort(400, "Ally not Found");
        }
        
        $rawStatData = Player::playerDataChart($server, $world, $playerID);
        $statData = array();
        foreach ($rawStatData as $rawData){
            $statData[$rawData->get('timestamp')] = $rawData->get($type);
        }
        
        $name = \App\Util\BasicFunctions::decodeName($playerData->name);
        $playerString = __('chart.who.player') . ": $name";
        
        $chart = new ImageChart("fonts/NotoMono-Regular.ttf", $this->decodeDimensions($width, $height), $this->debug);
        $chart -> render($statData, $playerString, Chart::chartTitel($type), Chart::displayInvers($type));
        return $chart -> output($ext);
    }

    public function getVillageSizedPic($server, $world, $villageID, $type, $width, $height, $ext)
    {
        BasicFunctions::local();
        $world = $this->fixWorldNameSpeed($server, $world);
        if (!Chart::validType($type)) {
            abort(400, "Invalid type");
        }
        
        $villageData = Village::village($server, $world, $villageID);
        if ($villageData == null) {
            abort(400, "Ally not Found");
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
        
        $chart = new ImageChart("fonts/NotoMono-Regular.ttf", $this->decodeDimensions($width, $height), $this->debug);
        $chart -> render($statData, $villageString, Chart::chartTitel($type), Chart::displayInvers($type));
        return $chart -> output($ext);
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
    
    private function decodeDimensions($width, $height)
    {
        if($width == 'w') {
            $retArr = [
                'width' => $height,
            ];
        } else if($width == 'h') {
            $retArr = [
                'height' => $height,
            ];
        } else {
            $retArr = [
                'width' => $width,
                'height' => $height,
            ];
        }
        
        if(isset($retArr['width']) && $retArr['width'] <= 0) {
            abort(400, "Width too small");
        }
        
        if(isset($retArr['height']) && $retArr['height'] <= 0) {
            abort(400, "Height too small");
        }
    }
    
    private function fixWorldNameSpeed($server, $world) {
        if(World::isSpeedName($world)) {
            //find first speed world with correct update url and use that?
            $serverData = Server::getServerByCode($server);
            $model = new World();
            $first = $model
                ->where("server_id", $serverData->id)
                ->where("url", "LIKE", "%" . BasicFunctions::likeSaveEscape($world) . "%")
                ->where("active", 1)
                ->first();
            return $first->name;
        }
        return $world;
    }
}
