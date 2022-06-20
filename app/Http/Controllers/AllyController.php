<?php

namespace App\Http\Controllers;

use App\Conquer;
use App\Ally;
use App\AllyTop;
use App\Server;
use App\World;
use App\Util\BasicFunctions;
use App\Util\Chart;
use App\AllyChanges;

class AllyController extends Controller
{
    public function ally($server, $world, $ally){
        BasicFunctions::local();
        $server = Server::getAndCheckServerByCode($server);
        $worldData = World::getAndCheckWorld($server, $world);

        $allyData = Ally::ally($worldData, $ally);
        $allyTopData = AllyTop::ally($worldData, $ally);
        abort_if($allyData == null && $allyTopData == null, 404, __("ui.errors.404.allyNotFound", ["world" => $worldData->display_name, "ally" => $ally]));
        
        $conquer = Conquer::allyConquerCounts($worldData, $ally);
        $allyChanges = AllyChanges::allyAllyChangeCounts($worldData, $ally);
        
        if($allyData == null) {
            return view('content.allyDeleted', compact('conquer', 'worldData', 'server', 'allyChanges', 'allyTopData'));
        }
        
        
        $statsGeneral = ['points', 'rank', 'village'];
        $statsBash = ['gesBash', 'offBash', 'defBash'];

        $datas = Ally::allyDataChart($worldData, $ally);
        if(count($datas) < 1) {
            $datas[] = [
                "timestamp" => time(),
                "points" => $allyData->points,
                "rank" => $allyData->rank,
                "village" => $allyData->village,
                "gesBash" => $allyData->gesBash,
                "offBash" => $allyData->offBash,
                "defBash" => $allyData->defBash,
            ];
        }
        
        
        $chartJS = "";
        for ($i = 0; $i < count($statsGeneral); $i++){
            $chartJS .= Chart::generateChart($datas, $statsGeneral[$i]);
        }
        for ($i = 0; $i < count($statsBash); $i++){
            $chartJS .= Chart::generateChart($datas, $statsBash[$i]);
        }
        
        return view('content.ally', compact('statsGeneral', 'statsBash', 'allyData', 'conquer', 'worldData', 'chartJS', 'server', 'allyChanges', 'allyTopData'));
    }

    public function allyBashRanking($server, $world, $ally)
    {
        BasicFunctions::local();
        $server = Server::getAndCheckServerByCode($server);
        $worldData = World::getAndCheckWorld($server, $world);

        $allyData = Ally::ally($worldData, $ally);
        abort_if($allyData == null, 404, __("ui.errors.404.allyNotFound", ["world" => $worldData->display_name, "ally" => $ally]));

        return view('content.allyBashRanking', compact('allyData', 'worldData', 'server'));
    }

    public function allyChanges($server, $world, $type, $allyID){
        BasicFunctions::local();
        $server = Server::getAndCheckServerByCode($server);
        $worldData = World::getAndCheckWorld($server, $world);
        
        $allyTopData = AllyTop::ally($worldData, $allyID);
        abort_if($allyTopData == null, 404, __("ui.errors.404.allyNotFound", ["world" => $worldData->display_name, "ally" => $allyID]));

        switch($type) {
            case "all":
                $typeName = ucfirst(__('ui.allyChanges.all'));
                break;
            case "old":
                $typeName = ucfirst(__('ui.allyChanges.old'));
                break;
            case "new":
                $typeName = ucfirst(__('ui.allyChanges.new'));
                break;
            default:
                abort(404, __("ui.errors.404.unknownType", ["type" => $type]));
        }

        return view('content.allyAllyChange', compact('worldData', 'server', 'allyTopData', 'typeName', 'type'));
    }

    public function conquer($server, $world, $type, $allyID){
        BasicFunctions::local();
        $server = Server::getAndCheckServerByCode($server);
        $worldData = World::getAndCheckWorld($server, $world);
        
        $allyTopData = AllyTop::ally($worldData, $allyID);
        abort_if($allyTopData == null, 404, __("ui.errors.404.allyNotFound", ["world" => $worldData->display_name, "ally" => $allyID]));

        switch($type) {
            case "all":
                $typeName = ucfirst(__('ui.conquer.all'));
                break;
            case "old":
                $typeName = ucfirst(__('ui.conquer.lost'));
                break;
            case "new":
                $typeName = ucfirst(__('ui.conquer.won'));
                break;
            case "own":
                $typeName = ucfirst(__('ui.conquer.allyOwn'));
                break;
            default:
                abort(404, __("ui.errors.404.unknownType", ["type" => $type]));
        }

        $allHighlight = ['s', 'i', 'b', 'd', 'w', 'l'];
        if(\Auth::check()) {
            $profile = \Auth::user()->profile;
            $userHighlight = explode(":", $profile->conquerHightlight_Ally);
        } else {
            $userHighlight = $allHighlight;
        }

        $who = BasicFunctions::decodeName($allyTopData->name).
                " [".BasicFunctions::decodeName($allyTopData->tag)."]";
        $routeDatatableAPI = route('api.allyConquer', [$worldData->id, $type, $allyTopData->allyID]);
        $routeHighlightSaving = route('user.saveConquerHighlighting', ['ally']);

        return view('content.conquer', compact('server', 'worldData', 'typeName',
                'who', 'routeDatatableAPI', 'routeHighlightSaving',
                'allHighlight', 'userHighlight'));
    }

    public function rank($server, $world){
        $server = Server::getAndCheckServerByCode($server);
        $worldData = World::getAndCheckWorld($server, $world);
        return view('content.rankAlly', compact('worldData', 'server'));
    }
}
