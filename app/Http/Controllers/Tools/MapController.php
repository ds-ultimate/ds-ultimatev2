<?php

namespace App\Http\Controllers\Tools;

use App\Player;
use App\World;
use App\Tool\Map\Map;
use App\Util\BasicFunctions;
use App\Util\CacheLogger;
use App\Util\ImageCached;
use App\Util\Map\AbstractMapGenerator;
use App\Util\Map\SQLMapGenerator;

use Carbon\Carbon;
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
        $worldData = World::getAndCheckWorld($server, $world);

        $mapModel = new Map();
        if(\Auth::check()) {
            //only allow one map without title per user per world
            $uniqueMap = $mapModel->where('world_id', $worldData->id)->where('user_id', \Auth::user()->id)->whereNull('title')->first();
            if($uniqueMap != null) {
                return redirect()->route('tools.map.mode', [$uniqueMap->id, 'edit', $uniqueMap->edit_key]);
            }
        }

        $mapModel->world_id = $worldData->id;
        if (\Auth::check()){
            $mapModel->user_id = \Auth::user()->id;
            $profile = \Auth::user()->profile;
            if($profile != null) {
                if(isset($profile->map_dimensions)) $mapModel->dimensions = $profile->map_dimensions;
                if(isset($profile->map_defaultColours)) $mapModel->defaultColours = $profile->map_defaultColours;
                if(isset($profile->map_markerFactor)) $mapModel->markerFactor = $profile->map_markerFactor;
            }
        }
        $mapModel->markerFactor = $mapModel->makerFactorDefault();
        $mapModel->autoDimensions = true;
        $mapModel->edit_key = Str::random(40);
        $mapModel->show_key = Str::random(40);
        $mapModel->save();
        $this->cacheMapImage($mapModel);
        $mapModel->save();
        return redirect()->route('tools.map.mode', [$mapModel->id, 'edit', $mapModel->edit_key]);
    }

    public function mode(Map $wantedMap, $action, $key){
        BasicFunctions::local();

        switch ($action) {
            case 'edit':
                abort_unless($key == $wantedMap->edit_key, 403);
                $wantedMap->touch();
                return $this->edit($wantedMap);
            case 'show':
                abort_unless($key == $wantedMap->show_key, 403);
                $wantedMap->touch();
                return $this->show($wantedMap);
            case 'getCanvas':
                abort_unless($key == $wantedMap->edit_key, 403);
                $wantedMap->touch();
                if($wantedMap->drawing_obj == null)
                    return "";

                /*$rawData = json_decode($wantedMap->drawing_obj);
                foreach($rawData->objects as $obj) {
                    if(isset($obj->eraserPaths)) {
                        $cnt = count($obj->eraserPaths);
                        for($i = 0; $i < $cnt; $i++) {
                            unset($obj->eraserPaths[$i]);
                        }
                    }
                }

                $newindex = 0;
                $oldCnt = count($rawData->objects);
                for($i = 0; $i < $oldCnt; $i++) {
                   if($rawData->objects[$i]->type == "ErasablePath" ||
                           $rawData->objects[$i]->type == "EraserPath") {
                       unset($rawData->objects[$i]);
                       continue;
                   }
                   if($i != $newindex) {
                       $rawData->objects[$newindex] = $rawData->objects[$i];
                       unset($rawData->objects[$i]);
                   }
                   $newindex++;
                }

                $modified = json_encode($rawData);

                return $modified;
                */

                return $wantedMap->drawing_obj;
            default:
                abort(404);
        }
    }

    public function modePost(Map $wantedMap, $action, $key){
        BasicFunctions::local();

        switch ($action) {
            case 'save':
                abort_unless($key == $wantedMap->edit_key, 403);
                $wantedMap->touch();
                return $this->save($wantedMap);
            case 'saveEdit':
                abort_unless($key == $wantedMap->edit_key, 403);
                $wantedMap->touch();
                $this->save($wantedMap);
                return $this->edit($wantedMap);
            case 'saveCanvas':
                abort_unless($key == $wantedMap->edit_key, 403);
                $wantedMap->touch();
                return $this->saveCanvas($wantedMap);
            case 'title':
                abort_unless($key == $wantedMap->edit_key, 403);
                $wantedMap->touch();
                return $this->saveTitle($wantedMap);
            default:
                abort(404);
        }
    }

    public function edit(Map $wantedMap){
        $worldData = $wantedMap->world;
        $defaults = [
            "ally" => $wantedMap->getMarkersAsDefaults($worldData, 'a'),
            "player" => $wantedMap->getMarkersAsDefaults($worldData, 'p'),
            "village" => $wantedMap->getMarkersAsDefaults($worldData, 'v'),
        ];
        $mode = 'edit';
        $server = $worldData->server;
        $mapDimensions = MapController::getMapDimension($wantedMap->getDimensions());
        $defMapDimensions = MapController::getMapDimension(AbstractMapGenerator::$DEFAULT_DIMENSIONS);

        $ownMaps = array();
        if(\Auth::check()) {
            $ownMaps = Map::where('user_id', \Auth::user()->id)->orderBy('world_id')->get();
        }

        return view('tools.mapMain', compact('server', 'worldData', 'wantedMap', 'mode', 'defaults', 'mapDimensions', 'ownMaps', 'defMapDimensions'));
    }

    public function show(Map $wantedMap){
        $worldData = $wantedMap->world;
        $defaults = [
            "ally" => $wantedMap->getMarkersAsDefaults($worldData, 'a'),
            "player" => $wantedMap->getMarkersAsDefaults($worldData, 'p'),
            "village" => $wantedMap->getMarkersAsDefaults($worldData, 'v'),
        ];
        $mode = 'show';
        $server = $worldData->server;
        $mapDimensions = MapController::getMapDimension($wantedMap->getDimensions());
        $defMapDimensions = MapController::getMapDimension(AbstractMapGenerator::$DEFAULT_DIMENSIONS);

        $ownMaps = array();
        if(\Auth::check()) {
            $ownMaps = Map::where('user_id', \Auth::user()->id)->orderBy('world_id')->get();
        }

        return view('tools.mapMain', compact('server', 'worldData', 'wantedMap', 'mode', 'defaults', 'mapDimensions', 'ownMaps', 'defMapDimensions'));
    }

    public function save(Map $wantedMap) {
        $getArray = $_POST;

        if(isset($getArray['mark'])) {
            $wantedMap->setMarkers($getArray['mark']);
        }
        if(isset($getArray['default'])) {
            $wantedMap->setDefaultColours(
                    (isset($getArray['default']['background']))?($getArray['default']['background']):(null),
                    (isset($getArray['default']['player']))?($getArray['default']['player']):(null),
                    (isset($getArray['default']['barbarian']))?($getArray['default']['barbarian']):(null)
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

            $wantedMap->setDimensions([
                'xs' => ceil($cX - $zoom / 2),
                'xe' => ceil($cX + $zoom / 2),
                'ys' => ceil($cY - $zoom / 2),
                'ye' => ceil($cY + $zoom / 2),
            ]);
        }

        if(isset($getArray['markerFactor'])) {
            $wantedMap->markerFactor = $getArray['markerFactor'];
        }

        if(isset($getArray['continentNumbersHere'])) {
            $wantedMap->continentNumbers = (isset($getArray['continentNumbers']))?(1):(0);
        }

        if(isset($getArray['autoUpdateHere'])) {
            $wantedMap->shouldUpdate = (isset($getArray['autoUpdate']))?(1):(0);
        }

        if(isset($getArray['zoomAutoHere'])) {
            $wantedMap->autoDimensions = (isset($getArray['zoomAuto']))?(1):(0);
        }

        $wantedMap->cached_at = null;
        $this->cacheMapImage($wantedMap);
        $wantedMap->save();

        return response()->json(MapController::getMapDimension($wantedMap->getDimensions()));
    }

    public function saveCanvas(Map $wantedMap) {
        $getArray = $_POST;

        abort_unless(isset($getArray['type']), 404);
        abort_unless(isset($getArray['data']), 404);
        switch($getArray['type']) {
            case "image":
                if(!isset($wantedMap->dimensions) || $wantedMap->dimensions == null)
                    $wantedMap->setDimensions(AbstractMapGenerator::$DEFAULT_DIMENSIONS);

                $wantedMap->drawing_dim = $wantedMap->dimensions;
                $wantedMap->drawing_png = \App\Util\PictureRender::base64ToPng($getArray['data']);
                $wantedMap->cached_at = null;
                $wantedMap->save();
                break;
            case "object":
                $wantedMap->drawing_obj = $getArray['data'];
                $wantedMap->save();
                break;
            default:
                abort(404);
        }

        return response()->json(MapController::getMapDimension($wantedMap->getDimensions()));
    }

    public static function saveTitle(Map $wantedMap){
        $getArray = $_POST;
        abort_unless(isset($getArray['title']), 404);
        
        $wantedMap->title = $getArray['title'];
        $wantedMap->save();
    }

    public static function getMapDimension($dimensions) {
        $dimensions['w'] = $dimensions['xe'] - $dimensions['xs'];
        $dimensions['h'] = $dimensions['ye'] - $dimensions['ys'];
        $dimensions['cx'] = intval(($dimensions['xs'] + $dimensions['xe']) / 2);
        $dimensions['cy'] = intval(($dimensions['ys'] + $dimensions['ye']) / 2);
        return $dimensions;
    }

    public function getOptionSizedMapByID(Map $wantedMap, $token, $options, $width, $height, $ext){
        BasicFunctions::local();
        abort_unless($token == $wantedMap->show_key, 403);
        $wantedMap->touch();

        if($options != null) {
            CacheLogger::logMiss(CacheLogger::$MAP_TYPE, $wantedMap->id);
            $skin = new \App\Util\Map\SkinSymbols();
            $map = new SQLMapGenerator($wantedMap->world, $skin, $this->decodeDimensions($width, $height), $this->debug);
            $wantedMap->prepareRendering($map);
            $map->setFont("fonts/arial.ttf");
            
            switch($options) {
                case "noDrawing":
                    $layers = $wantedMap->getLayerConfiguration();
                    $final = array();
                    foreach($layers as $layer)
                        if($layer != AbstractMapGenerator::$LAYER_DRAWING)
                            $final[] = $layer;

                    $map->setLayerOrder($final);
                    break;
                case "pureDrawing":
                    if(array_search(AbstractMapGenerator::$LAYER_DRAWING, $wantedMap->getLayerConfiguration()) !== False)
                        $map->setLayerOrder([AbstractMapGenerator::$LAYER_DRAWING]);
                    else
                        $map->setLayerOrder([]);
                    $map->setBackgroundColour([0,0,0,0]);
                    break;
                default:
            }
        } else if($wantedMap->cached_at !== null && (! $wantedMap->isCached() || !$wantedMap->shouldUpdate)) {
            //use cached version if:
            //either the map has been cached and should not be updated
            //or the map should be updated and has been cached not longer than 1day ago
            CacheLogger::logHit(CacheLogger::$MAP_TYPE, $wantedMap->id);
            $map = new ImageCached(storage_path(config('tools.map.cacheDir').$wantedMap->id), $this->decodeDimensions($width, $height), $this->debug);
        } else {
            $this->cacheMapImage($wantedMap);
            $map = new ImageCached(storage_path(config('tools.map.cacheDir').$wantedMap->id), $this->decodeDimensions($width, $height), $this->debug);
        }

        $map->render();
        return $map->output($ext);
    }
    
    private function cacheMapImage(Map $wantedMap) {
        CacheLogger::logMiss(CacheLogger::$MAP_TYPE, $wantedMap->id);
        $skin = new \App\Util\Map\SkinSymbols();
        $map = new SQLMapGenerator($wantedMap->world, $skin, $this->decodeDimensions(100, 100), $this->debug);
        $wantedMap->prepareRendering($map);
        $map->setFont("fonts/arial.ttf");

        $map->renderAtNative();
        if($wantedMap->autoDimensions) {
            $wantedMap->setDimensions($map->getMapDimensions());
        }

        $map->saveTo(storage_path(config('tools.map.cacheDir').$wantedMap->id), "png");
        $wantedMap->cached_at = Carbon::now();
        $wantedMap->save();
    }

    public function getSizedMapByID(Map $wantedMap, $token, $width, $height, $ext){
        return $this->getOptionSizedMapByID($wantedMap, $token, null, $width, $height, $ext);
    }

    public function getMapByID(Map $wantedMap, $token, $ext) {
        return $this->getSizedMapByID($wantedMap, $token, null, null, $ext);
    }

    public function getSizedOverviewMap($server, $world, $type, $id, $width, $height, $ext){
        BasicFunctions::local();
        $worldData = World::getAndCheckWorld($server, $world);

        $skin = new \App\Util\Map\SkinSymbols();
        $map = new SQLMapGenerator($worldData, $skin, $this->decodeDimensions($width, $height), $this->debug);
        switch($type) {
            case 'a':
                $map->markAlly($id, [255, 255, 255], false, true);
                break;
            case 'p':
                $map->markPlayer($id, [255, 255, 255], false, true);
                break;
            case 'v':
                $map->markVillage($id, [255, 255, 255], false, true);
                break;
            default:
                abort(404, "Wrong diagram type $type");
        }
        $map->setLayerOrder([AbstractMapGenerator::$LAYER_MARK, AbstractMapGenerator::$LAYER_GRID]);
        $map->setMapDimensions([
            'xs' => 0,
            'ys' => 0,
            'xe' => 1000,
            'ye' => 1000,
        ]);
        $map->setOpaque(100);
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
        $worldData = World::getAndCheckWorld($server, $world);
        
        $playerModel = new Player($worldData);
        $players = $playerModel->orderBy('rank')->limit(10)->get();

        $skin = new \App\Util\Map\SkinSymbols();
        $map = new SQLMapGenerator($worldData, $skin, $this->decodeDimensions(800, 800), $this->debug);

        $color = [[138,43,226],[72,61,139],[69,139,116],[188,143,143],[139,105,105],[244,164,96],[139,35,35],[139,115,85],[139,69,19],[0,100,0]];
        $i = 0;
        foreach ($players as $player){
            $map->markPlayer($player->playerID, $color[$i], false, true);
            $i++;
        }
        $map->setLayerOrder([AbstractMapGenerator::$LAYER_MARK, AbstractMapGenerator::$LAYER_GRID]);
        $map->setMapDimensions([
            'xs' => 0,
            'ys' => 0,
            'xe' => 1000,
            'ye' => 1000,
        ]);
        $map->setOpaque(100);
        $map->setAutoResize(true);
        $map->render();
        return $map->output('png');
    }

    public function mapTop10($server, $world){
        $worldData = World::getAndCheckWorld($server, $world);
        $worldData->server;
        
        $playerModel = new Player($worldData);
        $players = $playerModel->orderBy('rank')->limit(10)->get();

        $skin = new \App\Util\Map\SkinSymbols();
        $map = new SQLMapGenerator($worldData, $skin, $this->decodeDimensions(800, 800), $this->debug);

        $color = [[138,43,226],[72,61,139],[69,139,116],[188,143,143],[139,105,105],[244,164,96],[139,35,35],[139,115,85],[139,69,19],[0,100,0]];

        foreach ($players as $key => $player){
            $ps[$key] = ['name' => $player->name, 'color' => $color[$key]];
        }

        return view('content.mapTop10', compact('worldData', 'server', 'ps'));
    }

    public function destroy(Map $wantedMap, $key){
        abort_unless($wantedMap->edit_key == $key, 403);
        if($wantedMap->delete()){
            return \Response::json(array(
                'data' => 'success',
                'msg' => __('tool.map.destroySuccess'),
            ));
        }else{
            return \Response::json(array(
                'data' => 'error',
                'msg' => __('tool.map.destroyError'),
            ));
        }
    }
}
