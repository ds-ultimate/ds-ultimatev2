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
            $chartJS .= $this->chart($datas, $statsGeneral[$i]);
        }
        for ($i = 0; $i < count($statsBash); $i++){
            $chartJS .= $this->chart($datas, $statsBash[$i]);
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

    public function chart($playerData, $data){
        if (!Chart::validType($data)) {
            return;
        }

        $population = \Lava::DataTable();

        $population->addDateColumn('Tag')
            ->addNumberColumn(Chart::chartLabel($data));

        $oldTimestamp = 0;
        foreach ($playerData as $pData){
            if (date('Y-m-d', $pData->get('timestamp')) != $oldTimestamp){
                $population->addRow([date('Y-m-d', $pData->get('timestamp')), $pData->get($data)]);
                $oldTimestamp =date('Y-m-d', $pData->get('timestamp'));
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
                'format' => (Chart::vAxisFormat($data)),
            ]
        ]);

        return \Lava::render('LineChart', $data, 'chart-'.$data);
    }
}
