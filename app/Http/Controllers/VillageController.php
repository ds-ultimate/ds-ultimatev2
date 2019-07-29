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
        if ($villageData == null){
            //TODO: View ergänzen für Fehlermeldungen
            echo "Keine Daten über das Dorf mit der ID '$village' auf der Welt '$server$world' vorhanden.";
            exit;
        }

        $datas = Village::villageDataChart($server, $world, $village);
        $chartJS = $this->chart($datas, 'points');
        $conquer = Conquer::villageConquerCounts($server, $world, $village);

        return view('content.village', compact('villageData', 'conquer', 'worldData', 'chartJS', 'server'));
    }

    public function chart($villageData, $data){
        if (!Chart::validType($data)) {
            return;
        }

        $population = \Lava::DataTable();

        $population->addDateColumn('Tag')
            ->addNumberColumn(Chart::chartLabel($data));

        $oldTimestamp = 0;
        foreach ($villageData as $vData){
            if (date('Y-m-d', $vData->get('timestamp')) != $oldTimestamp){
                $population->addRow([date('Y-m-d', $vData->get('timestamp')), $vData->get($data)]);
                $oldTimestamp =date('Y-m-d', $vData->get('timestamp'));
            }
        }

        \Lava::LineChart($data, $population, [
            'title' => Chart::chartTitel($data),
            'legend' => 'none',
            'hAxis' => [
                'format' => 'dd/MM'
            ],
            'vAxis' => [
                'direction' => (Chart::displayInvers($data)?(-1):(1)),
                'format' => '0',
            ]
        ]);

        return \Lava::render('LineChart', $data, 'chart-'.$data);
    }
    
    public function conquer($server, $world, $type, $villageID){
        BasicFunctions::local();
        World::existWorld($server, $world);

        $worldData = World::getWorld($server, $world);
        $villageData = Village::village($server, $world, $villageID);

        switch($type) {
            case "all":
                $typeName = ucfirst(__('ui.conquer.all'));
                break;
            default:
                // FIXME: create error view
                return "Unknown type";
        }
        return view('content.villageConquer', compact('worldData', 'server', 'villageData', 'typeName', 'type'));
    }
}
