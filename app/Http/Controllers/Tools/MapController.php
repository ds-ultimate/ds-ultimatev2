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
                break;
            case '1':
                $map = new MapGenerator($world, "fonts/NotoMono-Regular.ttf", $this->decodeDimensions($width, $height), $this->debug);
                $map->setMapDimensions(400, 400, 500, 500);
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
