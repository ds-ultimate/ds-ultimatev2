<?php

namespace App\Http\Controllers\Tools;


use App\Util\BasicFunctions;
use App\World;
use Illuminate\Routing\Controller as BaseController;

class GreatSiegeCalcController extends BaseController
{

    public function index($server, $world){
        BasicFunctions::local();
        World::existWorld($server, $world);

        $worldData = World::getWorld($server, $world);
        if($worldData->config == null || $worldData->units == null || $worldData->win_condition != 9) {
            abort(404, __('tool.greatSiegeCalc.notAvailable'));
        }

        $unitConfig = $worldData->unitConfig();
        $config = simplexml_load_string($worldData->config);
        
        return view('tools.greatSiegeCalc', compact('worldData', 'server', 'unitConfig', 'config'));
    }

}
