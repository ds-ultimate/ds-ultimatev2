<?php

namespace App\Http\Controllers\Tools;

use App\HistoryIndex;
use App\World;
use App\Tool\AnimHistMap\AnimHistMapMap;
use App\Tool\AnimHistMap\AnimHistMapJob;
use App\Util\Map\AbstractMapGenerator;
use App\Util\BasicFunctions;
use App\Util\Map\HistoryMapGenerator;

use ZipArchive;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class AnimatedHistoryMapController extends BaseController
{
    private $debug = false;

    public function __construct()
    {
        $this->debug = config('app.debug');
    }
    
    public static function isAvailable($world) {
        if(!BasicFunctions::hasWorldDataTable($world, "index")) return false;
        
        $histModel = new HistoryIndex($world);
        return $histModel->first() != null;
    }
    
    public function create($server, $world) {
        BasicFunctions::local();
        $worldData = World::getAndCheckWorld($server, $world);
        abort_unless(static::isAvailable($worldData), 404, __("ui.errors.404.toolNotAvail.animHistMap"));

        $mapModel = new AnimHistMapMap();
        if(\Auth::check()) {
            //only allow one map without title per user per world
            $uniqueMap = $mapModel->where('world_id', $worldData->id)->where('user_id', \Auth::user()->id)->whereNull('title')->first();
            if($uniqueMap != null) {
                return redirect()->route('tools.animHistMap.mode', [$uniqueMap->id, 'edit', $uniqueMap->edit_key]);
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
        $mapModel->edit_key = Str::random(40);
        $mapModel->show_key = Str::random(40);
        $mapModel->save();
        return redirect()->route('tools.animHistMap.mode', [$mapModel->id, 'edit', $mapModel->edit_key]);
    }
    
    public function preview(AnimHistMapMap $wantedMap, $key, $histIdx, $ext) {
        abort_unless($key == $wantedMap->show_key, 403);
        BasicFunctions::local();
        
        $world = $wantedMap->world;
        abort_unless(static::isAvailable($world), 404, __("ui.errors.404.toolNotAvail.animHistMap"));
        abort_if($world->maintananceMode, 503);
        
        $dim = array(
            'width' => 1000,
            'height' => 1000,
        );
        
        $histModel = HistoryIndex::find($world, $histIdx);
        
        $skin = new \App\Util\Map\SkinSymbols();
        $map = new HistoryMapGenerator($world, $histModel, $skin, $dim, $this->debug);
        
        $wantedMap->prepareRendering($map);
        
        $map->render();
        $map->renderAlignedText(AbstractMapGenerator::$ANCHOR_TOP_LEFT, 100, 20, 10, "Zeit " . $histModel->date, [255, 255, 255]);
        return $map->output($ext);
    }
    
    public function mode(AnimHistMapMap $wantedMap, $action, $key) {
        BasicFunctions::local();
        abort_unless(static::isAvailable($wantedMap->world), 404, __("ui.errors.404.toolNotAvail.animHistMap"));
        abort_if($wantedMap->world->maintananceMode, 503);

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
    
    public function modePost(Request $request, AnimHistMapMap $wantedMap, $action, $key) {
        BasicFunctions::local();
        abort_unless(static::isAvailable($wantedMap->world), 404, __("ui.errors.404.toolNotAvail.animHistMap"));
        abort_if($wantedMap->world->maintananceMode, 503);

        switch ($action) {
            case 'save':
                abort_unless($key == $wantedMap->edit_key, 403);
                return $this->save($wantedMap);
            case 'saveEdit':
                abort_unless($key == $wantedMap->edit_key, 403);
                $this->save($wantedMap);
                return $this->edit($wantedMap);
            case 'title':
                abort_unless($key == $wantedMap->edit_key, 403);
                return $this->titleSave($request, $wantedMap);
            case 'render':
                abort_unless($key == $wantedMap->show_key, 403);
                return $this->startRendering($wantedMap);
            default:
                abort(404);
        }
    }
    
    private function edit(AnimHistMapMap $wantedMap) {
        static::updateMapDimensions($wantedMap);
        
        $worldData = $wantedMap->world;
        $server = $worldData->server;
        
        $defaults = [
            "ally" => $wantedMap->getMarkersAsDefaults($worldData, 'a'),
            "player" => $wantedMap->getMarkersAsDefaults($worldData, 'p'),
            "village" => $wantedMap->getMarkersAsDefaults($worldData, 'v'),
        ];
        $mode = 'edit';
        $mapDimensions = MapController::getMapDimension($wantedMap->getDimensions());
        $defMapDimensions = MapController::getMapDimension(AbstractMapGenerator::$DEFAULT_DIMENSIONS);

        $ownMaps = array();
        if(\Auth::check()) {
            $ownMaps = AnimHistMapMap::where('user_id', \Auth::user()->id)->orderBy('world_id')->get();
        }
        
        $histIdxs = (new HistoryIndex($worldData))->get();
        
        return view('tools.animHistMap.map', compact('server', 'worldData', 'wantedMap', 'mode', 'defaults', 'mapDimensions', 'ownMaps', 'histIdxs', 'defMapDimensions'));
    }
    
    private function show(AnimHistMapMap $wantedMap) {
        $worldData = $wantedMap->world;
        $server = $worldData->server;
        
        $defaults = [
            "ally" => $wantedMap->getMarkersAsDefaults($worldData, 'a'),
            "player" => $wantedMap->getMarkersAsDefaults($worldData, 'p'),
            "village" => $wantedMap->getMarkersAsDefaults($worldData, 'v'),
        ];
        $mode = 'show';
        $mapDimensions = MapController::getMapDimension($wantedMap->getDimensions());
        $defMapDimensions = MapController::getMapDimension(AbstractMapGenerator::$DEFAULT_DIMENSIONS);

        $ownMaps = array();
        if(\Auth::check()) {
            $ownMaps = AnimHistMapMap::where('user_id', \Auth::user()->id)->orderBy('world_id')->get();
        }
        
        $histIdxs = (new HistoryIndex($worldData))->get();
        
        return view('tools.animHistMap.map', compact('server', 'worldData', 'wantedMap', 'mode', 'defaults', 'mapDimensions', 'ownMaps', 'histIdxs', 'defMapDimensions'));
    }

    private function save(AnimHistMapMap $wantedMap) {
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

        if(isset($getArray['zoomAutoHere'])) {
            $oldDim = $wantedMap->autoDimensions;
            $wantedMap->autoDimensions = (isset($getArray['zoomAuto']))?(1):(0);
            
            if($oldDim !== $wantedMap->autoDimensions) {
                static::updateMapDimensions($wantedMap);
            }
        }
        
        $wantedMap->save();

        return response()->json(MapController::getMapDimension($wantedMap->getDimensions()));
    }
    
    public function titleSave(Request $request, AnimHistMapMap $wantedMap) {
        $data = $request->validate([
            'title' => 'required',
        ]);
        $wantedMap->title = $data['title'];
        $wantedMap->save();
    }
    
    private function startRendering(AnimHistMapMap $wantedMap) {
        $job = new AnimHistMapJob();
        foreach(AnimHistMapMap::$copyToJob as $elm) {
            $job->$elm = $wantedMap->$elm;
        }
        $job->edit_key = Str::random(40);
        $job->show_key = Str::random(40);
        $job->animHistMapMap_id = $wantedMap->id;
        $job->save();
        return redirect()->route('tools.animHistMap.renderStatus', [$job->id, $job->edit_key]);
    }
    
    public function renderStatus(AnimHistMapJob $wantedJob, $key) {
        abort_unless($key == $wantedJob->edit_key, 403);
        BasicFunctions::local();
        
        $worldData = $wantedJob->world;
        
        return view('tools.animHistMap.renderStatus', compact('worldData', 'wantedJob'));
    }
    
    public function renderRerun(AnimHistMapJob $wantedJob, $key) {
        abort_unless($key == $wantedJob->edit_key, 403);
        
        $wantedJob->finished_at = null;
        $wantedJob->state = null;
        $wantedJob->save();
        
        return redirect()->route('tools.animHistMap.renderStatus', [$wantedJob->id, $wantedJob->edit_key]);
    }
    
    public function apiRenderStatus(AnimHistMapJob $wantedJob, $key) {
        abort_unless($key == $wantedJob->edit_key, 403);
        BasicFunctions::local();
        
        $retArr = $wantedJob->getStateAsArray();
        if($retArr['finished']) {
            //inject download Links
            $retArr['downloadMP4'] = route('tools.animHistMap.download', [$wantedJob->id, $wantedJob->show_key, 'mp4']);
            $retArr['downloadZIP'] = route('tools.animHistMap.download', [$wantedJob->id, $wantedJob->show_key, 'zip']);
            $retArr['downloadGIF'] = route('tools.animHistMap.download', [$wantedJob->id, $wantedJob->show_key, 'gif']);
        }
        return response()->json($retArr);
    }
    
    public function download(AnimHistMapJob $wantedJob, $key, $format) {
        abort_unless($key == $wantedJob->show_key, 403);
        
        switch($format) {
            case "mp4":
                $fileName = storage_path(config('tools.animHistMap.renderDir') . "{$wantedJob->id}/render.mp4");
                break;
            case "zip":
                $fileName = storage_path(config('tools.animHistMap.renderDir') . "{$wantedJob->id}/src.zip");
                break;
            case "gif":
                $fileName = storage_path(config('tools.animHistMap.renderDir') . "{$wantedJob->id}/animated.gif");
                break;
            default:
                abort(404);
        }
        if(!file_exists($fileName)) {
            abort(404);
        }
        
        return response()->file($fileName);
    }

    public static function renderJob(AnimHistMapJob $animJob){
        $worldData = $animJob->world;
        abort_if($worldData->maintananceMode, 503);
        
        static::updateMapDimensions($animJob);
        
        $dim = array(
            'width' => 1000,
            'height' => 1000,
        );
        
        $histModel = new HistoryIndex($worldData);
        $histData = $histModel->get();
        $cntHistData = count($histData);
        $animJob->finished_at = null;
        $conf = storage_path(config('tools.animHistMap.renderDir'));
        
        if(file_exists("$conf{$animJob->id}")) {
            static::removeRawPngDir($conf, $animJob);
            if(file_exists("$conf{$animJob->id}/render.mp4")) {
                unlink("$conf{$animJob->id}/render.mp4");
            }
            if(file_exists("$conf{$animJob->id}/src.zip")) {
                unlink("$conf{$animJob->id}/src.zip");
            }
            if(file_exists("$conf{$animJob->id}/animated.gif")) {
                unlink("$conf{$animJob->id}/animated.gif");
            }
            rmdir("$conf{$animJob->id}");
        }
        
        mkdir("$conf{$animJob->id}/png/", 0777, true);
        $maxProgress = $cntHistData * 3 + 2;
        
        for($num = 0; $num < $cntHistData; $num++) {
            // +3 because gif / mp4 / zip
            $animJob->setState($num, $maxProgress, "image", $num, $cntHistData);
            
            $skin = new \App\Util\Map\SkinSymbols();
            $map = new HistoryMapGenerator($worldData, $histData[$num], $skin, $dim, false);
            $animJob->prepareRendering($map);
            $map->render();
            
            $map->renderAlignedText(AbstractMapGenerator::$ANCHOR_TOP_LEFT, 100, 20, 10, "Zeit " . $histData[$num]->date, [255, 255, 255]);
            
            $map->saveTo("$conf{$animJob->id}/png/image-" . str_pad($num, 4, "0", STR_PAD_LEFT), 'png');
        }
        
        //mp4
        $animJob->setState($cntHistData, $maxProgress, "mp4", 0, 0);
        $imgPath = escapeshellarg("$conf{$animJob->id}/png/image-%04d.png");
        $outPath = escapeshellarg("$conf{$animJob->id}/render.mp4");
        shell_exec("ffmpeg -r 5 -f image2 -i $imgPath -vcodec libx264 -crf 25 -pix_fmt yuv420p $outPath 2>&1");
        
        //gif
        $imgPath = escapeshellarg("$conf{$animJob->id}/png/image-%04d.png");
        $outPath = escapeshellarg("$conf{$animJob->id}/animated.gif");
        $fPointer = popen("./GifEncoder $imgPath $outPath $cntHistData 200", "r");
        
        while(!feof($fPointer)) {
            $line = trim(fgets($fPointer));
            if(BasicFunctions::startsWith($line, "Add")) {
                $at = intval(substr($line, 4));
                $animJob->setState($cntHistData + 1 + $at, $maxProgress, "gifAdd", $at, $cntHistData);
            } else if(BasicFunctions::startsWith($line, "Write")) {
                $at = intval(substr($line, 6));
                $animJob->setState($cntHistData + 1 + $cntHistData + $at, $maxProgress, "gifWrite", $at, $cntHistData);
            }
        }
        
        //zip
        $animJob->setState($cntHistData * 3 + 2, $maxProgress, "zip", 0, 0);
        $zip = new ZipArchive();
        $zip->open("$conf{$animJob->id}/src.zip", ZipArchive::CREATE);
        for($num = 0; $num < count($histData); $num++) {
            $imgName = "image-" . str_pad($num, 4, "0", STR_PAD_LEFT) . ".png";
            $zip->addFile("$conf{$animJob->id}/png/$imgName", $imgName);
        }
        $zip->close();
        static::removeRawPngDir($conf, $animJob);
        
        $animJob->finished_at = Carbon::now();
        $animJob->save();
    }

    public function destroyAnimHistMapMap(AnimHistMapMap $wantedMap, $key){
        abort_unless($wantedMap->edit_key == $key, 403);
        if($wantedMap->delete()){
            return \Response::json(array(
                'data' => 'success',
                'msg' => __('tool.animHistMap.destroySuccess'),
            ));
        }else{
            return \Response::json(array(
                'data' => 'error',
                'msg' => __('tool.animHistMap.destroyError'),
            ));
        }
    }

    public function destroyAnimHistMapJob(AnimHistMapJob $wantedJob, $key){
        abort_unless($wantedJob->edit_key == $key, 403);
        if($wantedJob->delete()){
            return \Response::json(array(
                'data' => 'success',
                'msg' => __('tool.animHistMap.renderedDestroySuccess'),
            ));
        }else{
            return \Response::json(array(
                'data' => 'error',
                'msg' => __('tool.animHistMap.renderedDestroyError'),
            ));
        }
    }
    
    private static function removeRawPngDir($conf, $animJob) {
        if(file_exists("$conf{$animJob->id}/png")) {
            foreach(scandir("$conf{$animJob->id}/png") as $imgFile) {
                if(BasicFunctions::startsWith($imgFile, "image")) {
                    unlink("$conf{$animJob->id}/png/$imgFile");
                }
            }
            rmdir("$conf{$animJob->id}/png");
        }
    }
    
    private static function updateMapDimensions(AnimHistMapMap $wantedMap) {
        if(! $wantedMap->autoDimensions) return;
        
        $worldData = $wantedMap->world;
        abort_unless(static::isAvailable($worldData), 404, __("ui.errors.404.toolNotAvail.animHistMap"));
        
        $dim = array(
            'width' => 1000,
            'height' => 1000,
        );
        
        $histModel = new HistoryIndex($worldData);
        $hist = $histModel->orderBy('id', 'desc')->first();
        
        $skin = new \App\Util\Map\SkinSymbols();
        $map = new HistoryMapGenerator($worldData, $hist, $skin, $dim, false);
        
        $wantedMap->prepareRendering($map);
        $map->setMapDimensions(AbstractMapGenerator::$DEFAULT_DIMENSIONS);
        $map->setAutoResize(true);
        
        $map->render();
        $wantedMap->setDimensions($map->getMapDimensions());
        $wantedMap->save();
    }
}
