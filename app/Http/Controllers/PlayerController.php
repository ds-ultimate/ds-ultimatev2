<?php

namespace App\Http\Controllers;

use App\Ally;
use App\Conquer;
use App\Player;
use App\Util\BasicFunctions;
use App\Util\Chart;
use App\World;
use Illuminate\Http\Request;
use Khill\Lavacharts\DataTables\Formats\DateFormat;
use Khill\Lavacharts\Lavacharts;

class PlayerController extends Controller
{
    public function player($server, $world, $player){
        BasicFunctions::local();
        World::existServer($server);
        World::existWorld($server, $world);

        $worldData = World::getWorldCollection($server, $world);

        $playerData = Player::player($server, $world, $player);
        if ($playerData == null){
            //TODO: View ergänzen für Fehlermeldungen
            echo "Keine Daten über den Spieler mir der ID '$player' aud der Welt '$server$world' vorhanden.";
            exit;
        }

        $statsGeneral = ['points', 'rank', 'village'];
        $statsBash = ['gesBash', 'offBash', 'defBash', 'utBash'];

        $datas = Player::playerDataChart($server, $world, $player);

        for ($i = 0; $i < count($statsGeneral); $i++){
            $this->chart($datas, $statsGeneral[$i]);
        }
        for ($i = 0; $i < count($statsBash); $i++){
            $this->chart($datas, $statsBash[$i]);
        }

        $conquer = Conquer::playerConquerCounts($server, $world, $player);

        return view('content.player', compact('statsGeneral', 'statsBash', 'playerData', 'conquer', 'worldData'));

    }

    public function chart($playerData, $data){

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

        if ($data == 'rank'){
            \Lava::LineChart($data, $population, [
                'title' => Chart::chartTitel($data),
                'legend' => 'none',
                'hAxis' => [
                    'format' => 'dd/MM'
                ],
                'vAxis' => [
                    'direction' => -1,
                    'format' => '0',
                ]
            ]);
        }else{
            \Lava::LineChart($data, $population, [
                'title' => Chart::chartTitel($data),
                'legend' => 'none',
                'hAxis' => [
                    'format' => 'dd/MM'
                ],
                'vAxis' => [
                    'format' => 'short'
                ]
            ]);
        }

        //echo "<div id=\"chart-$data\"></div>";
        echo \Lava::render('LineChart', $data, 'chart-'.$data);

    }
}
