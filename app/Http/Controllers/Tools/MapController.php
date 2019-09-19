<?php

namespace App\Http\Controllers\Tools;

use App\Tool\Map\Map;
use App\Util\BasicFunctions;
use App\Util\MapGenerator;
use App\World;
use App\Player;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class MapController extends BaseController
{
    private $debug = false;
    
    public function __construct()
    {
        $this->debug = config('app.debug');
    }
    
    public function new($server, $world){
        BasicFunctions::local();
        World::existWorld($server, $world);
        $worldData = World::getWorld($server, $world);
        $mapModel = new Map();
        $mapModel->world_id = $worldData->id;
        if (\Auth::user()){
            $mapModel->user_id = \Auth::user()->id;
        }
        $mapModel->edit_key = Str::random(40);
        $mapModel->show_key = Str::random(40);
        $mapModel->save();
        return redirect()->route('tools.mapToolMode', [$mapModel->id, 'edit', $mapModel->edit_key]);
    }
    
    public function mode(Map $wantedMap, $action, $key){
        BasicFunctions::local();
        
        switch ($action) {
            case 'edit':
                abort_unless($key == $wantedMap->edit_key, 403);
                return $this->edit($wantedMap);
            case 'show':
                abort_unless($key == $wantedMap->show_key, 403);
                return $this->show($wantedMap);
            default:
                abort(404);
        }
    }
    
    public function edit(Map $wantedMap){
        $this->save($wantedMap);
        
        $worldData = $wantedMap->world;
        $defaults = [
            "ally" => $wantedMap->getMarkersAsDefaults($worldData, 'a'),
            "player" => $wantedMap->getMarkersAsDefaults($worldData, 'p'),
            "village" => $wantedMap->getMarkersAsDefaults($worldData, 'v'),
        ];
        $mode = 'edit';
        $server = $worldData->server->code;
        $mapDimensions = $this->getMapDimension($wantedMap);
        
        return view('tools.map', compact('server', 'worldData', 'wantedMap', 'mode', 'defaults', 'mapDimensions'));
    }
    
    public function show(Map $wantedMap){
        $worldData = $wantedMap->world;
        $mode = 'show';
        $server = $worldData->server->code;
        return view('tools.map', compact('server', 'worldData', 'wantedMap', 'mode'));
    }
    
    public function save(Map $wantedMap) {
        $getArray = \Illuminate\Support\Facades\Input::get();
        
        if(isset($getArray['mark'])) {
            $wantedMap->setMarkers($getArray['mark']);
        }
        if(isset($getArray['default'])) {
            $wantedMap->setDefaultColours(
                    (isset($getArray['default']['background']))?($getArray['default']['background']):(null),
                    (isset($getArray['default']['player']))?($getArray['default']['player']):(null),
                    (isset($getArray['default']['barbarian']))?($getArray['default']['barbarian']):(null),
            );
        }
        //do this after setting Default Colours as it modifies the same Property
        if(isset($getArray['showBarbarianHere'])) {
            if(!isset($getArray['showBarbarian'])) {
                $wantedMap->disableBarbarian();
            }
        }
        if(isset($getArray['showPlayerHere'])) {
            if(!isset($getArray['showPlayer'])) {
                $wantedMap->disablePlayer();
            }
        }
        
        if(isset($getArray['zoomValue']) &&
                isset($getArray['centerX']) &&
                isset($getArray['centerY'])) {
            $zoom = (int) $getArray['zoomValue'];
            $cX = (int) $getArray['centerX'];
            $cY = (int) $getArray['centerY'];
            
            $xs = ceil($cX - $zoom / 2);
            $xe = ceil($cX + $zoom / 2);
            $ys = ceil($cY - $zoom / 2);
            $ye = ceil($cY + $zoom / 2);
            $wantedMap->setDimensions($xs, $xe, $ys, $ye);
        }
        $wantedMap->save();
    }
    
    private function getMapDimension(Map $mapModel) {
        $dimensions = $mapModel->getDimensions();
        $dimension['w'] = $dimensions['xe'] - $dimensions['xs'];
        $dimension['h'] = $dimensions['ye'] - $dimensions['ys'];
        $dimension['cx'] = intval(($dimensions['xs'] + $dimensions['xe']) / 2);
        $dimension['cy'] = intval(($dimensions['ys'] + $dimensions['ye']) / 2);
        return $dimension;
    }

    public function getSizedMapByID(Map $wantedMap, $token, $width, $height, $ext){
        BasicFunctions::local();
        abort_unless($token == $wantedMap->show_key, 403);
        
        $map = new MapGenerator($wantedMap->world, $this->decodeDimensions($width, $height), $this->debug);
        $wantedMap->prepareRendering($map);
        $map->render();
        return $map->output($ext);
    }
    
    public function getMapByID(Map $wantedMap, $token, $ext) {
        return $this->getSizedMapByID($wantedMap, $token, null, null, $ext);
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
        $map->setLayerOrder([MapGenerator::$LAYER_MARK, MapGenerator::$LAYER_GRID]);
        $map->setMapDimensions([
            'xs' => 0,
            'ys' => 0,
            'xe' => 1000,
            'ye' => 1000,
        ]);
        $map->setOpaque(100);
        $map->setHighlight(true);
        $map->setAutoResize(true);
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
    
    public function mapTop10P($server, $world){
        BasicFunctions::local();
        $world = World::getWorld($server, $world);
        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server,$world->name).'.player_latest');
        $players = $playerModel->orderBy('rank')->limit(10)->get();

        $map = new MapGenerator($world, $this->decodeDimensions(800, 800), $this->debug);

        $color = [[138,43,226],[72,61,139],[69,139,116],[188,143,143],[139,105,105],[244,164,96],[139,35,35],[139,115,85],[139,69,19],[0,100,0]];
        $i = 0;
        foreach ($players as $player){
            $map->markPlayer($player->playerID, $color[$i]);
            $i++;
        }
        $map->setLayerOrder([MapGenerator::$LAYER_MARK, MapGenerator::$LAYER_GRID]);
        $map->setMapDimensions([
            'xs' => 0,
            'ys' => 0,
            'xe' => 1000,
            'ye' => 1000,
        ]);
        $map->setOpaque(100);
        $map->setHighlight(true);
        $map->setAutoResize(true);
        $map->render();
        return $map->output('png');
    }

    public function mapTop10($server, $world){
        World::existWorld($server, $world);
        $worldData = World::getWorld($server, $world);

        $world = World::getWorld($server, $world);
        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server,$world->name).'.player_latest');
        $players = $playerModel->orderBy('rank')->limit(10)->get();

        $map = new MapGenerator($world, $this->decodeDimensions(800, 800), $this->debug);

        $color = [[138,43,226],[72,61,139],[69,139,116],[188,143,143],[139,105,105],[244,164,96],[139,35,35],[139,115,85],[139,69,19],[0,100,0]];

        foreach ($players as $key => $player){
            $ps[$key] = ['name' => $player->name, 'color' => $color[$key]];
        }

        return view('content.mapTop10', compact('worldData', 'server', 'ps'));
    }
}
