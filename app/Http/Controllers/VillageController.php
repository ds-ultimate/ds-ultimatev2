<?php

namespace App\Http\Controllers;

use App\Conquer;
use App\Village;
use App\Util\BasicFunctions;
use App\Util\Chart;
use App\World;

class VillageController extends Controller
{
    public function village($server, $world, $village){
        BasicFunctions::local();
        World::existWorld($server, $world);

        $worldData = World::getWorld($server, $world);

        $villageData = Village::village($server, $world, $village);
        abort_if($villageData == null, 404, "Keine Daten über das Dorf mit der ID '$village'" .
                "auf der Welt '$server$world' vorhanden.");

        $datas = Village::villageDataChart($server, $world, $village);
        $chartJS = Chart::generateChart($datas, 'points');
        $conquer = Conquer::villageConquerCounts($server, $world, $village);

        return view('content.village', compact('villageData', 'conquer', 'worldData', 'chartJS', 'server'));
    }
    
    public function conquer($server, $world, $type, $villageID){
        BasicFunctions::local();
        World::existWorld($server, $world);

        $worldData = World::getWorld($server, $world);
        $villageData = Village::village($server, $world, $villageID);
        abort_if($villageData == null, 404, "Keine Daten über das Dorf mit der ID '$villageID'" .
                "auf der Welt '$server$world' vorhanden.");

        switch($type) {
            case "all":
                $typeName = ucfirst(__('ui.conquer.all'));
                break;
            default:
                abort(404, "Unknown type");
        }
        return view('content.villageConquer', compact('worldData', 'server', 'villageData', 'typeName', 'type'));
    }
}
