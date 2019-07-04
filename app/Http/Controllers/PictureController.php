<?php

namespace App\Http\Controllers;

use App\Ally;
use App\Player;
use App\Village;
use App\Util\Chart;
use App\Util\ImageChart;
use App\Util\BasicFunctions;

class PictureController extends Controller
{
    private $debug = false;
    
    public function __construct()
    {
        $this->debug = env('APP_DEBUG', 'false');
    }
    
    public function getAllySizedPic($server, $world, $allyID, $type, $width, $height, $ext)
    {
        BasicFunctions::local();
        if (!Chart::validType($type)) {
            return "Invalid type";
        }
        
        $rawStatData = Ally::allyDataChart($server, $world, $allyID);
        
        $statData = array();
        
        foreach ($rawStatData as $rawData){
            $statData[$rawData->get('timestamp')] = $rawData->get($type);
        }
        
        $allyData = Ally::ally($server, $world, $allyID);
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
        if (!Chart::validType($type)) {
            return "Invalid type";
        }
        
        $rawStatData = Player::playerDataChart($server, $world, $playerID);
        
        $statData = array();
        
        foreach ($rawStatData as $rawData){
            $statData[$rawData->get('timestamp')] = $rawData->get($type);
        }
        
        $playerData = Player::player($server, $world, $playerID);
        $name = \App\Util\BasicFunctions::decodeName($playerData->name);
        $playerString = __('chart.who.player') . ": $name";
        
        $chart = new ImageChart("fonts/NotoMono-Regular.ttf", $this->decodeDimensions($width, $height), $this->debug);
        $chart -> render($statData, $playerString, Chart::chartTitel($type), Chart::displayInvers($type));
        return $chart -> output($ext);
    }

    public function getVillageSizedPic($server, $world, $villageID, $type, $width, $height, $ext)
    {
        BasicFunctions::local();
        if (!Chart::validType($type)) {
            return "Invalid type";
        }
        
        $rawStatData = Village::villageDataChart($server, $world, $villageID);
        
        $statData = array();
        
        foreach ($rawStatData as $rawData){
            $statData[$rawData->get('timestamp')] = $rawData->get($type);
        }
        
        $villageData = Village::village($server, $world, $villageID);
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
            return array(
                'width' => $height,
            );
        } else if($width == 'h') {
            return array(
                'height' => $height,
            );
        } else {
            return array(
                'width' => $width,
                'height' => $height,
            );
        }
    }
}
