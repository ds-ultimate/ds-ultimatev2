<?php

namespace App\Http\Controllers;

use App\Conquer;
use App\Ally;
use App\AllyTop;
use App\Util\BasicFunctions;
use App\Util\Chart;
use App\World;
use App\AllyChanges;

class AllyController extends Controller
{
    public function ally($server, $world, $ally){
        BasicFunctions::local();
        World::existWorld($server, $world);

        $worldData = World::getWorld($server, $world);

        $allyData = Ally::ally($server, $world, $ally);
        $allyTopData = AllyTop::ally($server, $world, $ally);
        abort_if($allyData == null && $allyTopData == null, 404, "Keine Daten über den Stamm mit der ID '$ally'" .
                " auf der Welt '$server$world' vorhanden.");
        
        $conquer = Conquer::allyConquerCounts($server, $world, $ally);
        $allyChanges = AllyChanges::allyAllyChangeCounts($server, $world, $ally);
        
        if($allyData == null) {
            return view('content.allyDeleted', compact('conquer', 'worldData', 'server', 'allyChanges', 'allyTopData'));
        }
        
        
        $statsGeneral = ['points', 'rank', 'village'];
        $statsBash = ['gesBash', 'offBash', 'defBash'];

        $datas = Ally::allyDataChart($server, $world, $ally);
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
        World::existWorld($server, $world);

        $worldData = World::getWorld($server, $world);

        $allyData = Ally::ally($server, $world, $ally);
        abort_if($allyData == null, 404, "Keine Daten über den Stamm mit der ID '$ally'" .
            " auf der Welt '$server$world' vorhanden.");

        return view('content.allyBashRanking', compact('allyData', 'worldData', 'server'));
    }

    public function allyChanges($server, $world, $type, $allyID){
        BasicFunctions::local();
        World::existWorld($server, $world);

        $worldData = World::getWorld($server, $world);
        $allyTopData = AllyTop::ally($server, $world, $allyID);
        abort_if($allyTopData == null, 404, "Keine Daten über den Stamm mit der ID '$allyID'" .
                " auf der Welt '$server$world' vorhanden.");

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
                abort(404, "Unknown type");
        }

        return view('content.allyAllyChange', compact('worldData', 'server', 'allyTopData', 'typeName', 'type'));
    }

    public function conquer($server, $world, $type, $allyID){
        BasicFunctions::local();
        World::existWorld($server, $world);

        $worldData = World::getWorld($server, $world);
        $allyTopData = AllyTop::ally($server, $world, $allyID);
        abort_if($allyTopData == null, 404, "Keine Daten über den Stamm mit der ID '$allyID'" .
                " auf der Welt '$server$world' vorhanden.");

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
                abort(404, "Unknown type");
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
        $routeDatatableAPI = route('api.allyConquer', [$worldData->server->code, $worldData->name, $type, $allyTopData->allyID]);
        $routeHighlightSaving = route('user.saveConquerHighlighting', ['ally']);

        return view('content.conquer', compact('server', 'worldData', 'typeName',
                'who', 'routeDatatableAPI', 'routeHighlightSaving',
                'allHighlight', 'userHighlight'));
    }

    public function rank($server, $world){
        World::existWorld($server, $world);

        $worldData = World::getWorld($server, $world);

        return view('content.rankAlly', compact('worldData', 'server'));
    }
}
