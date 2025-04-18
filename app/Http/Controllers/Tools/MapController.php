<?php

namespace App\Http\Controllers\Tools;

use App\Player;
use App\Server;
use App\World;
use App\Tool\Map\Map;
use App\Util\CacheLogger;
use App\Util\ImageCached;
use App\Util\Map\AbstractMapGenerator;
use App\Util\Map\SQLMapGenerator;

use Carbon\Carbon;
use Illuminate\Http\Request;
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
        abort_if($wantedMap->world->maintananceMode, 503);

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

    public function modePost(Request $request, Map $wantedMap, $action, $key){
        abort_if($wantedMap->world->maintananceMode, 503);

        switch ($action) {
            case 'save':
                abort_unless($key == $wantedMap->edit_key, 403);
                $wantedMap->touch();
                return $this->save($request, $wantedMap);
            case 'saveEdit':
                abort_unless($key == $wantedMap->edit_key, 403);
                $wantedMap->touch();
                $this->save($request, $wantedMap);
                return $this->edit($wantedMap);
            case 'saveCanvas':
                abort_unless($key == $wantedMap->edit_key, 403);
                $wantedMap->touch();
                return $this->saveCanvas($request, $wantedMap);
            case 'title':
                abort_unless($key == $wantedMap->edit_key, 403);
                $wantedMap->touch();
                return $this->saveTitle($request, $wantedMap);
            default:
                abort(404);
        }
    }

    private function edit(Map $wantedMap){
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

    private function show(Map $wantedMap){
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

    private function save(Request $request, Map $wantedMap) {
        $data = $request->validate([
            'mark' => 'array|max:200',
            'mark.ally' => 'array',
            'mark.player' => 'array',
            'mark.village' => 'array',
            'mark.*.*.id' => 'numeric|integer|nullable',
            'mark.*.*.colour' => 'string|size:6',
            'mark.*.*.textHere' => 'boolean',
            'mark.*.*.text' => 'string|max:3',
            'mark.*.*.hLightHere' => 'boolean',
            'mark.*.*.hLight' => 'string|max:3',
            'default' => 'array',
            'default.background' => 'string|size:6',
            'default.player' => 'string|size:6',
            'default.barbarian' => 'string|size:6',
            'showBarbarianHere' => 'boolean',
            'showBarbarian' => 'string|max:3',
            'showPlayerHere' => 'boolean',
            'showPlayer' => 'string|max:3',
            'zoomValue' => 'required|numeric|integer',
            'centerX' => 'required|numeric|integer',
            'centerY' => 'required|numeric|integer',
            'markerFactor' => 'required|numeric|min:0|max:1',
            'continentNumbersHere' => 'boolean',
            'continentNumbers' => 'string|max:3',
            'autoUpdateHere' => 'boolean',
            'autoUpdate' => 'string|max:3',
            'zoomAutoHere' => 'boolean',
            'zoomAuto' => 'string|max:3',
        ]);

        if(isset($data['mark'])) {
            $wantedMap->setMarkers($data['mark']);
        }
        if(isset($data['default'])) {
            $wantedMap->setDefaultColours(
                    (isset($data['default']['background']))?($data['default']['background']):(null),
                    (isset($data['default']['player']))?($data['default']['player']):(null),
                    (isset($data['default']['barbarian']))?($data['default']['barbarian']):(null)
            );
        }
        //do this after setting Default Colours as it modifies the same Property
        if(isset($data['showBarbarianHere'])) {
            if(!isset($data['showBarbarian'])) {
                $wantedMap->disableBarbarian();
            }
        }
        if(isset($data['showPlayerHere'])) {
            if(!isset($data['showPlayer'])) {
                $wantedMap->disablePlayer();
            }
        }

        $zoom = (int) $data['zoomValue'];
        $cX = (int) $data['centerX'];
        $cY = (int) $data['centerY'];

        $wantedMap->setDimensions([
            'xs' => ceil($cX - $zoom / 2),
            'xe' => ceil($cX + $zoom / 2),
            'ys' => ceil($cY - $zoom / 2),
            'ye' => ceil($cY + $zoom / 2),
        ]);

        if(isset($data['markerFactor'])) {
            $wantedMap->markerFactor = $data['markerFactor'];
        }

        if(isset($data['continentNumbersHere'])) {
            $wantedMap->continentNumbers = (isset($data['continentNumbers']))?(1):(0);
        }

        if(isset($data['autoUpdateHere'])) {
            $wantedMap->shouldUpdate = (isset($data['autoUpdate']))?(1):(0);
        }

        if(isset($data['zoomAutoHere'])) {
            $wantedMap->autoDimensions = (isset($data['zoomAuto']))?(1):(0);
        }

        $wantedMap->cached_at = null;
        $this->cacheMapImage($wantedMap);
        $wantedMap->save();

        return response()->json(MapController::getMapDimension($wantedMap->getDimensions()));
    }

    private function saveCanvas(Request $request, Map $wantedMap) {
        $data = $request->validate([
            'type' => 'required|string',
            'data' => 'required',
        ]);
        
        switch($data['type']) {
            case "image":
                if(!isset($wantedMap->dimensions) || $wantedMap->dimensions == null)
                    $wantedMap->setDimensions(AbstractMapGenerator::$DEFAULT_DIMENSIONS);

                $wantedMap->drawing_dim = $wantedMap->dimensions;
                $wantedMap->drawing_png = \App\Util\PictureRender::base64ToPng($data['data']);
                $wantedMap->cached_at = null;
                $wantedMap->save();
                break;
            case "object":
                $wantedMap->drawing_obj = $data['data'];
                $wantedMap->save();
                break;
            default:
                abort(404);
        }

        return response()->json(MapController::getMapDimension($wantedMap->getDimensions()));
    }

    private static function saveTitle(Request $request, Map $wantedMap){
        $data = $request->validate([
            'title' => 'required|string',
        ]);
        
        $wantedMap->title = $data['title'];
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
        abort_unless($token == $wantedMap->show_key, 403);
        abort_if($wantedMap->world->maintananceMode, 503);
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
                abort(404, __("ui.errors.404.unknownType", ["type" => $type]));
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

    public function mapTop10Tmp($server, $world){
        $worldData = World::getAndCheckWorld($server, $world);
        
        $allyModel = new \App\Ally($worldData);
        $allies = $allyModel->orderBy('rank')->limit(15)->get();

        $color = [
            [255, 25, 25], [250, 255, 0], [0, 0, 128], [19, 244, 239], [255, 170, 0], [255, 255, 255], [0, 0, 0], [104, 255, 0],
            [128, 0, 0], [189, 183, 107], [32, 178, 170], [30, 144, 255], [170, 110, 40], [255, 20, 147], [145, 30, 180],
        ];
        $skin = new \App\Util\Map\SkinBot($color);
        $map = new SQLMapGenerator($worldData, $skin, $this->decodeDimensions(800, 800), $this->debug);
        $map->setLegend(new \App\Util\Map\BasicLegend());

        $i = 0;
        foreach ($allies as $ally){
            $map->markAlly($ally->allyID, $color[$i], true, true);
            $i++;
        }
        $map->setLayerOrder([AbstractMapGenerator::$LAYER_GRID, AbstractMapGenerator::$LAYER_MARK]);
        $map->setMapDimensions([
            'xs' => 0,
            'ys' => 0,
            'xe' => 1000,
            'ye' => 1000,
        ]);
        $map->setOpaque(100);
        $map->setAutoResize(true);
        $map->renderAtNative();
        return $map->output('png');
    }

    public function mapTop10P($server, $world){
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
        $server = Server::getAndCheckServerByCode($server);
        $worldData = World::getAndCheckWorld($server, $world);
        
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
