<?php

namespace App\Http\Controllers;

use App\Conquer;
use App\Village;
use App\Server;
use App\World;
use App\Util\BasicFunctions;
use App\Util\Chart;

class VillageController extends Controller
{
    public function village($server, $world, $village){
        BasicFunctions::local();
        $server = Server::getAndCheckServerByCode($server);
        $worldData = World::getAndCheckWorld($server, $world);

        $villageData = Village::village($worldData, $village);
        abort_if($villageData == null, 404, __("ui.errors.404.villageNotFound", ["world" => $worldData->display_name, "village" => $village]));
        
        $villageHistData = $villageData->getHistoryData($worldData);
        $datas = Village::pureVillageDataChart($villageHistData);
        if(count($datas) < 1) {
            $datas[] = [
                "timestamp" => time(),
                "points" => $villageData->points,
            ];
        }
        $chartJS = Chart::generateChart($datas, 'points', gapFill: true);
        $conquer = Conquer::villageConquerCounts($worldData, $village);
        
        $villageHistory = [];
        $last = null;
        $pointBuildingMap = \App\Util\BuildingUtils::getPointBuildingMap($worldData);
        foreach($villageHistData as $vilHist) {
            $pntChange = $vilHist->points - ($last->points ?? 0);
            if($pntChange == 0) continue;
            
            $villageHistory[] = [
                "date" => $vilHist->created_at,
                "points" => $vilHist->points,
                "pointChange" => $pntChange,
                "time" => $last!==null ? $last->created_at->diff($vilHist->created_at)->format("%hh") : "-",
                "possibleChanges" => $pointBuildingMap[$pntChange] ?? null,
            ];
            $last = $vilHist;
        }
        
        usort($villageHistory, function($a, $b) {
            if($a['date']->gt($b['date'])) {
                return -1;
            }
            if($a['date']->lt($b['date'])) {
                return 1;
            }
            return 0;
        });

        return view('content.village', compact('villageData', 'conquer', 'worldData', 'chartJS', 'server', 'villageHistory'));
    }
    
    public function conquer($server, $world, $type, $villageID){
        BasicFunctions::local();
        $server = Server::getAndCheckServerByCode($server);
        $worldData = World::getAndCheckWorld($server, $world);
        
        $villageData = Village::village($worldData, $villageID);
        abort_if($villageData == null, 404, __("ui.errors.404.villageNotFound", ["world" => $worldData->display_name, "village" => $villageID]));

        switch($type) {
            case "all":
                $typeName = ucfirst(__('ui.conquer.all'));
                break;
            default:
                abort(404, __("ui.errors.404.unknownType", ["type" => $type]));
        }
        
        $allHighlight = ['s', 'i', 'b', 'd'];
        if(\Auth::check()) {
            $profile = \Auth::user()->profile;
            $userHighlight = explode(":", $profile->conquerHightlight_Village);
        } else {
            $userHighlight = $allHighlight;
        }
        
        $who = BasicFunctions::decodeName($villageData->name);
        $routeDatatableAPI = route('api.villageConquer', [$worldData->server->code, $worldData->name, $type, $villageData->villageID]);
        $routeHighlightSaving = route('user.saveConquerHighlighting', ['village']);
        
        return view('content.conquer', compact('server', 'worldData', 'typeName',
                'who', 'routeDatatableAPI', 'routeHighlightSaving',
                'allHighlight', 'userHighlight'));
    }
}
