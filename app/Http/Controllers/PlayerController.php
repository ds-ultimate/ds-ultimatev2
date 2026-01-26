<?php

namespace App\Http\Controllers;

use App\Conquer;
use App\Player;
use App\PlayerTop;
use App\PlayerOtherServers;
use App\Util\BasicFunctions;
use App\Util\Chart;
use App\Server;
use App\World;
use App\AllyChanges;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function player($server, $world, $player, Request $request){
        $server = Server::getAndCheckServerByCode($server);
        $worldData = World::getAndCheckWorld($server, $world);

        $reqParams = $request->validate([
            "diagramConquer" => "boolean"
        ]);
        if(isset($reqParams["diagramConquer"])) {
            \Session::put("playerDiagramConquer", $reqParams["diagramConquer"]);
        }

        $playerData = Player::player($worldData, $player);
        $playerTopData = PlayerTop::player($worldData, $player);
        abort_if($playerData == null && $playerTopData == null, 404, __("ui.errors.404.playerNotFound", ["world" => $worldData->getDistplayName(), "player" => $player]));

        $playerOtherServers = PlayerOtherServers::player($worldData->server, $player);

        $conquer = Conquer::playerConquerCounts($worldData, $player);
        $allyChanges = AllyChanges::playerAllyChangeCount($worldData, $player);

        if($playerData == null) {
            return view('content.playerDeleted', compact('playerTopData', 'conquer', 'worldData', 'server', 'allyChanges', 'playerOtherServers'));
        }

        $statsGeneral = ['points', 'rank', 'village'];
        $statsBash = ['gesBash', 'offBash', 'defBash', 'supBash'];

        $datas = Player::playerDataChart($worldData, $player);
        if(count($datas) < 1) {
            $datas[] = [
                "timestamp" => time(),
                "points" => $playerData->points,
                "rank" => $playerData->rank,
                "village" => $playerData->village,
                "gesBash" => $playerData->gesBash,
                "offBash" => $playerData->offBash,
                "defBash" => $playerData->defBash,
                "supBash" => $playerData->supBash,
            ];
        }

        $showConquersInBash = \Session::get("playerDiagramConquer") ?? false;
        $conquerAnnotations = [];
        if($showConquersInBash) {
            $minTimestamp = $datas[0]["timestamp"];
            $maxTimestamp = $datas[0]["timestamp"];
            foreach($datas as $d) {
                if($d["timestamp"] < $minTimestamp) {
                    $minTimestamp = $d["timestamp"];
                }
                if($d["timestamp"] > $maxTimestamp) {
                    $maxTimestamp = $d["timestamp"];
                }
            }

            $conquerModel = new Conquer($worldData);
            $conquers = $conquerModel->where(function($query) use ($player) {
                return $query->where('new_owner', $player)->orWhere('old_owner', $player);
            })->where("timestamp", ">", $minTimestamp)->where("timestamp", "<", $maxTimestamp)
                    ->orderBy("timestamp", "asc")->get();

            foreach($conquers as $c) {
                if($c["new_owner"] == $player) {
                    $conquerAnnotations[] = [
                        $c["timestamp"],
                        "win",
                        $c["old_owner_name"] . " [" . $c["old_ally_tag"] . "]",
                    ];
                } else {
                    $conquerAnnotations[] = [
                        $c["timestamp"],
                        "loose",
                        $c["new_owner_name"] . " [" . $c["new_ally_tag"] . "]",
                    ];
                }
            }
        }

        $chartJS = "";
        for ($i = 0; $i < count($statsGeneral); $i++){
            $chartJS .= Chart::generateChart($datas, $statsGeneral[$i]);
        }
        for ($i = 0; $i < count($statsBash); $i++){
            $chartJS .= Chart::generateChart($datas, $statsBash[$i], annotations: $conquerAnnotations);
        }

        return view('content.player', compact('statsGeneral', 'statsBash', 'playerData', 'playerTopData', 'conquer', 'worldData', 'chartJS', 'server', 'allyChanges', 'playerOtherServers', 'showConquersInBash'));
    }

    public function allyChanges($server, $world, $type, $playerID){
        $server = Server::getAndCheckServerByCode($server);
        $worldData = World::getAndCheckWorld($server, $world);

        $playerTopData = PlayerTop::player($worldData, $playerID);
        abort_if($playerTopData == null, 404, __("ui.errors.404.playerNotFound", ["world" => $worldData->getDistplayName(), "player" => $playerID]));

        switch($type) {
            case "all":
                $typeName = ucfirst(__('ui.allyChanges.all'));
                break;
            default:
                abort(404, __("ui.errors.404.unknownType", ["type" => $type]));
        }
        return view('content.playerAllyChange', compact('worldData', 'server', 'playerTopData', 'typeName', 'type'));
    }

    public function conquer($server, $world, $type, $playerID){
        $server = Server::getAndCheckServerByCode($server);
        $worldData = World::getAndCheckWorld($server, $world);

        $playerTopData = PlayerTop::player($worldData, $playerID);
        abort_if($playerTopData == null, 404, __("ui.errors.404.playerNotFound", ["world" => $worldData->getDistplayName(), "player" => $playerID]));

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
                abort(404, __("ui.errors.404.unknownType", ["type" => $type]));
        }

        $allHighlight = ['s', 'i', 'b', 'd', 'w', 'l'];
        if(\Auth::check()) {
            $profile = \Auth::user()->profile;
            $userHighlight = explode(":", $profile->conquerHightlight_Player);
        } else {
            $userHighlight = $allHighlight;
        }

        $who = BasicFunctions::decodeName($playerTopData->name);
        $routeDatatableAPI = route('api.playerConquer', [$worldData->id, $type, $playerTopData->playerID]);
        $routeHighlightSaving = route('user.saveConquerHighlighting', ['player']);
        $tableStateName = "tableStateName";

        return view('content.conquer', compact('server', 'worldData', 'typeName',
                'who', 'routeDatatableAPI', 'routeHighlightSaving',
                'allHighlight', 'userHighlight', 'tableStateName'));
    }

    public function rank($server, $world){
        $server = Server::getAndCheckServerByCode($server);
        $worldData = World::getAndCheckWorld($server, $world);
        return view('content.rankPlayer', compact('worldData', 'server'));
    }
}
