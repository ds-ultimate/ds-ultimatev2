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
        
        $map = new MapGenerator($world, $this->decodeDimensions($width, $height), $this->debug);
        switch($id) {
            case '0':
                $map->setLayerOrder(array(MapGenerator::$LAYER_MARK));
                $map->markVillage(10, array(255, 0, 0));
                $map->markVillage(20, array(255, 0, 0));
                break;
            case '1':
                $map->setMapDimensions(400, 400, 440, 440);
                $map->setSkin("default");
                $map->setLayerOrder(array(MapGenerator::$LAYER_PICTURE, MapGenerator::$LAYER_MARK));
                $map->setOpaque(40);
                $map->setPlayerColour(51, 23, 4);
                $map->setBarbarianColour(51, 23, 4);
                break;
            case '2':
                $map->setLayerOrder(array(MapGenerator::$LAYER_MARK));
                $map->markPlayer(2908937, array(255, 0, 0));
                $map->markPlayer(186056, array(0, 255, 0));
                $map->markPlayer(344194, array(0, 0, 255));
                break;
            case '3':
                $map->setLayerOrder(array(MapGenerator::$LAYER_MARK));
                $map->markAlly(4, array(255, 0, 0));
                $map->markAlly(27, array(0, 255, 0));
                $map->markAlly(852, array(0, 0, 255));
                break;
            case '4':
                $map->setLayerOrder(array(MapGenerator::$LAYER_MARK));
                break;
        }
        $map->render();
        return $map->output($ext);
    }
    
    public function getMapByID($id, $token, $ext) {
        return $this->getSizedMapByID($id, $token, null, null, $ext);
    }
    
    public function getSizedOverviewMap($server, $world, $type, $id, $width, $height, $ext){
        BasicFunctions::local();
        $world = World::getWorld($server, $world);
        
        $map = new MapGenerator($world, $this->decodeDimensions($width, $height), $this->debug);
        switch($type) {
            case 'a':
                $map->markAlly($id, [255, 255, 255]);
                break;
            case 'p':
                $map->markPlayer($id, [255, 255, 255]);
                break;
            case 'v':
                $map->markVillage($id, [255, 255, 255]);
                break;
            default:
                //FIXME create error view
                return "Wrong type " . htmlentities($type);
        }
        $map->setLayerOrder([MapGenerator::$LAYER_MARK]);
        $map->setMapDimensions(0, 0, 1000, 1000);
        $map->setOpaque(100);
        $map->render();
        return $map->output($ext);
    }
    
    public function getOverviewMap($server, $world, $type, $id, $ext){
        return $this->getSizedOverviewMap($server, $world, $type, $id, null, null, $ext);
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
