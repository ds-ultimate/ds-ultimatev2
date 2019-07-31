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
        if ($playerData == null){
            //TODO: View ergänzen für Fehlermeldungen
            echo "Keine Daten über den Spieler mit der ID '$player' auf der Welt '$server$world' vorhanden.";
            exit;
        }

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

        switch($type) {
            case "all":
                $typeName = ucfirst(__('ui.allyChanges.all'));
                break;
            default:
                // FIXME: create error view
                return "Unknown type";
        }
        return view('content.playerAllyChange', compact('worldData', 'server', 'playerData', 'typeName', 'type'));
    }
    
    public function conquer($server, $world, $type, $playerID){
        BasicFunctions::local();
        World::existWorld($server, $world);

        $worldData = World::getWorld($server, $world);
        $playerData = Player::player($server, $world, $playerID);

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
            default:
                // FIXME: create error view
                return "Unknown type";
        }
        return view('content.playerConquer', compact('worldData', 'server', 'playerData', 'typeName', 'type'));
    }
}
