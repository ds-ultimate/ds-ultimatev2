<?php

namespace App\Http\Controllers;

use App\Ally;
use App\Util\BasicFunctions;
use App\Util\Chart;
use App\World;
use Illuminate\Http\Request;
use Khill\Lavacharts\DataTables\Formats\DateFormat;
use Khill\Lavacharts\Lavacharts;

class AllyController extends Controller
{
    public function ally($server, $world, $ally){
        BasicFunctions::local();
        World::existServer($server);
        World::existWorld($server, $world);

        $worldData = World::getWorldCollection($server, $world);

        $allyData = Ally::ally($server, $world, $ally);

        $statsGeneral = ['points', 'rank', 'village'];
        $statsBash = ['gesBash', 'offBash', 'defBash', 'utBash'];

        $datas = Ally::allyDataChart($server, $world, $ally);

        for ($i = 0; $i < count($statsGeneral); $i++){
            $this->chart($datas, $statsGeneral[$i]);
        }
        for ($i = 0; $i < count($statsBash); $i++){
            $this->chart($datas, $statsBash[$i]);
        }

        return view('content.ally', compact('statsGeneral', 'statsBash', 'allyData', 'worldData'));

    }

    public function chart($allyData, $data){

        $population = \Lava::DataTable();

        $population->addDateColumn('Tag')
            ->addNumberColumn(Chart::chartLabel($data));

        $oldTimestamp = 0;
        foreach ($allyData as $aData){
            if (date('Y-m-d', $aData->get('timestamp')) != $oldTimestamp){
                $population->addRow([date('Y-m-d', $aData->get('timestamp')), $aData->get($data)]);
                $oldTimestamp =date('Y-m-d', $aData->get('timestamp'));
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
                    'direction' => -1
                ]
            ]);
        }else{
            \Lava::LineChart($data, $population, [
                'title' => Chart::chartTitel($data),
                'legend' => 'none',
                'hAxis' => [
                    'format' => 'dd/MM'
                ]
            ]);
        }

        //echo "<div id=\"chart-$data\"></div>";
        echo \Lava::render('LineChart', $data, 'chart-'.$data);

    }

}
