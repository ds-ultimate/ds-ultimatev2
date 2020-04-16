<?php

namespace App\Http\Controllers;

use App\Conquer;
use App\Player;
use App\Util\BasicFunctions;
use App\Util\Chart;
use App\World;
use App\AllyChanges;

class PlayerController extends Controller
{
    public function player($server, $world, $player){
        BasicFunctions::local();
        World::existWorld($server, $world);

        $worldData = World::getWorld($server, $world);

        $playerData = Player::player($server, $world, $player);
        abort_if($playerData == null, 404, "Keine Daten über den Spieler mit der ID '$player'" .
                " auf der Welt '$server$world' vorhanden.");

        $statsGeneral = ['points', 'rank', 'village'];
        $statsBash = ['gesBash', 'offBash', 'defBash', 'utBash'];

        $datas = Player::playerDataChart($server, $world, $player);
        
        $chartJS = "";
        for ($i = 0; $i < count($statsGeneral); $i++){
            $chartJS .= Chart::generateChart($datas, $statsGeneral[$i]);
        }
        for ($i = 0; $i < count($statsBash); $i++){
            $chartJS .= Chart::generateChart($datas, $statsBash[$i]);
        }

        $conquer = Conquer::playerConquerCounts($server, $world, $player);
        $allyChanges = AllyChanges::playerAllyChangeCount($server, $world, $player);

        return view('content.player', compact('statsGeneral', 'statsBash', 'playerData', 'conquer', 'worldData', 'chartJS', 'server', 'allyChanges'));

    }
    
    public function allyChanges($server, $world, $type, $playerID){
        BasicFunctions::local();
        World::existWorld($server, $world);

        $worldData = World::getWorld($server, $world);
        $playerData = Player::player($server, $world, $playerID);
        abort_if($playerData == null, 404, "Keine Daten über den Spieler mit der ID '$playerID'" .
                " auf der Welt '$server$world' vorhanden.");

        switch($type) {
            case "all":
                $typeName = ucfirst(__('ui.allyChanges.all'));
                break;
            default:
                abort(404, "Unknown type");
        }
        return view('content.playerAllyChange', compact('worldData', 'server', 'playerData', 'typeName', 'type'));
    }
    
    public function conquer($server, $world, $type, $playerID){
        BasicFunctions::local();
        World::existWorld($server, $world);

        $worldData = World::getWorld($server, $world);
        $playerData = Player::player($server, $world, $playerID);
        abort_if($playerData == null, 404, "Keine Daten über den Spieler mit der ID '$playerID'" .
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
                $typeName = ucfirst(__('ui.conquer.playerOwn'));
                break;
            default:
                abort(404, "Unknown type");
        }
        
        $allHighlight = ['s', 'i', 'b', 'd', 'w', 'l'];
        if(\Auth::check()) {
            $profile = \Auth::user()->profile;
            $userHighlight = explode(":", $profile->conquerHightlight_Player);
        } else {
            $userHighlight = $allHighlight;
        }
        
        $who = BasicFunctions::decodeName($playerData->name);
        $routeDatatableAPI = route('api.playerConquer', [$worldData->server->code, $worldData->name, $type, $playerData->playerID]);
        $routeHighlightSaving = route('user.saveConquerHighlighting', ['player']);
        
        return view('content.conquer', compact('server', 'worldData', 'typeName',
                'who', 'routeDatatableAPI', 'routeHighlightSaving',
                'allHighlight', 'userHighlight'));
    }

    public function rank($server, $world){
        World::existWorld($server, $world);

        $worldData = World::getWorld($server, $world);

        return view('content.rankPlayer', compact('worldData', 'server'));
    }
}
