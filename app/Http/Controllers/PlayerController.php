<?php

namespace App\Http\Controllers;

use App\Conquer;
use App\Player;
use App\PlayerTop;
use App\PlayerOtherServers;
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
        $playerTopData = PlayerTop::player($server, $world, $player);
        abort_if($playerData == null && $playerTopData == null, 404, "Keine Daten über den Spieler mit der ID '$player'" .
                " auf der Welt '$server$world' vorhanden.");
        
        $playerOtherServers = PlayerOtherServers::player($worldData->server, $player);
        
        $conquer = Conquer::playerConquerCounts($server, $world, $player);
        $allyChanges = AllyChanges::playerAllyChangeCount($server, $world, $player);
        
        if($playerData == null) {
            return view('content.playerDeleted', compact('playerTopData', 'conquer', 'worldData', 'server', 'allyChanges', 'playerOtherServers'));
        }
        
        $statsGeneral = ['points', 'rank', 'village'];
        $statsBash = ['gesBash', 'offBash', 'defBash', 'supBash'];

        $datas = Player::playerDataChart($server, $world, $player);
        
        $chartJS = "";
        for ($i = 0; $i < count($statsGeneral); $i++){
            $chartJS .= Chart::generateChart($datas, $statsGeneral[$i]);
        }
        for ($i = 0; $i < count($statsBash); $i++){
            $chartJS .= Chart::generateChart($datas, $statsBash[$i]);
        }
        
        return view('content.player', compact('statsGeneral', 'statsBash', 'playerData', 'playerTopData', 'conquer', 'worldData', 'chartJS', 'server', 'allyChanges', 'playerOtherServers'));
    }
    
    public function allyChanges($server, $world, $type, $playerID){
        BasicFunctions::local();
        World::existWorld($server, $world);

        $worldData = World::getWorld($server, $world);
        $playerTopData = PlayerTop::player($server, $world, $playerID);
        abort_if($playerTopData == null, 404, "Keine Daten über den Spieler mit der ID '$playerID'" .
                " auf der Welt '$server$world' vorhanden.");

        switch($type) {
            case "all":
                $typeName = ucfirst(__('ui.allyChanges.all'));
                break;
            default:
                abort(404, "Unknown type");
        }
        return view('content.playerAllyChange', compact('worldData', 'server', 'playerTopData', 'typeName', 'type'));
    }
    
    public function conquer($server, $world, $type, $playerID){
        BasicFunctions::local();
        World::existWorld($server, $world);

        $worldData = World::getWorld($server, $world);
        $playerTopData = PlayerTop::player($server, $world, $playerID);
        abort_if($playerTopData == null, 404, "Keine Daten über den Spieler mit der ID '$playerID'" .
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
        
        $who = BasicFunctions::decodeName($playerTopData->name);
        $routeDatatableAPI = route('api.playerConquer', [$worldData->server->code, $worldData->name, $type, $playerTopData->playerID]);
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
