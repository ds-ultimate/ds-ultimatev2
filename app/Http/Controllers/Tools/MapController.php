<?php

namespace App\Http\Controllers\Tools;

use App\Util\BasicFunctions;
use App\Util\MapGenerator;
use App\World;

use Illuminate\Routing\Controller as BaseController;

class MapController extends BaseController
{
    private $debug = false;
    
    public function __construct()
    {
        $this->debug = config('app.debug');
    }
    
    public function getSizedMapByID($id, $token, $width, $height, $ext){
        BasicFunctions::local();
        $world = World::getWorld("de", "p11");
        
        switch($id) {
            case '0':
                $map = new MapGenerator($world, "fonts/NotoMono-Regular.ttf", $this->decodeDimensions($width, $height), $this->debug);
                $map->setLayerOrder(array(MapGenerator::$LAYER_MARK));
                $map->markVillage(10, array(255, 0, 0));
                $map->markVillage(20, array(255, 0, 0));
                break;
            case '1':
                $map = new MapGenerator($world, "fonts/NotoMono-Regular.ttf", $this->decodeDimensions($width, $height), $this->debug);
                $map->setMapDimensions(400, 400, 440, 440);
                $map->setSkin("default");
                $map->setLayerOrder(array(MapGenerator::$LAYER_PICTURE, MapGenerator::$LAYER_MARK));
                $map->setOpaque(40);
                break;
            case '2':
                $map = new MapGenerator($world, "fonts/NotoMono-Regular.ttf", $this->decodeDimensions($width, $height), $this->debug);
                $map->setLayerOrder(array(MapGenerator::$LAYER_MARK));
                $map->markPlayer(2908937, array(255, 0, 0));
                $map->markPlayer(186056, array(0, 255, 0));
                $map->markPlayer(344194, array(0, 0, 255));
                break;
            case '3':
                $map = new MapGenerator($world, "fonts/NotoMono-Regular.ttf", $this->decodeDimensions($width, $height), $this->debug);
                $map->setLayerOrder(array(MapGenerator::$LAYER_MARK));
                $map->markAlly(4, array(255, 0, 0));
                $map->markAlly(27, array(0, 255, 0));
                $map->markAlly(852, array(0, 0, 255));
                break;
            case '4':
                $map = new MapGenerator($world, "fonts/NotoMono-Regular.ttf", $this->decodeDimensions($width, $height), $this->debug);
                $map->setLayerOrder(array(MapGenerator::$LAYER_MARK));
                break;
        }
        $map -> render();
        return $map -> output($ext);
    }
    
    public function getMapByID($id, $token, $ext) {
        return $this->getSizedMapByID($id, $token, null, null, $ext);
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
