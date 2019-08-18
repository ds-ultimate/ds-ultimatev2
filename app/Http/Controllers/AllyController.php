<?php

namespace App\Http\Controllers;

use App\Conquer;
use App\Ally;
use App\Player;
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
        if ($allyData == null){
            //TODO: View ergänzen für Fehlermeldungen
            echo "Keine Daten über den Stamm mit der ID '$ally' auf der Welt '$server$world' vorhanden.";
            exit;
        }


        $statsGeneral = ['points', 'rank', 'village'];
        $statsBash = ['gesBash', 'offBash', 'defBash'];

        $datas = Ally::allyDataChart($server, $world, $ally);
        
        $chartJS = "";
        for ($i = 0; $i < count($statsGeneral); $i++){
            $chartJS .= Chart::generateChart($datas, $statsGeneral[$i]);
        }
        for ($i = 0; $i < count($statsBash); $i++){
            $chartJS .= Chart::generateChart($datas, $statsBash[$i]);
        }
        
        $conquer = Conquer::allyConquerCounts($server, $world, $ally);
        $allyChanges = AllyChanges::allyAllyChangeCounts($server, $world, $ally);
        
        return view('content.ally', compact('statsGeneral', 'statsBash', 'allyData', 'conquer', 'worldData', 'chartJS', 'server', 'allyChanges'));
    }
    
    public function allyChanges($server, $world, $type, $allyID){
        BasicFunctions::local();
        World::existWorld($server, $world);

        $worldData = World::getWorld($server, $world);
        $allyData = Ally::ally($server, $world, $allyID);
        
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
                // FIXME: create error view
                return "Unknown type";
        }

        return view('content.allyAllyChange', compact('worldData', 'server', 'allyData', 'typeName', 'type'));
    }
    
    public function conquer($server, $world, $type, $allyID){
        BasicFunctions::local();
        World::existWorld($server, $world);

        $worldData = World::getWorld($server, $world);
        $allyData = Ally::ally($server, $world, $allyID);

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
                // FIXME: create error view
                return "Unknown type";
        }
        return view('content.allyConquer', compact('worldData', 'server', 'allyData', 'typeName', 'type'));
    }
}
