<?php
/**
 * Created by IntelliJ IDEA.
 * User: crams
 * Date: 10.08.2019
 * Time: 20:38
 */

namespace App\Http\Controllers\Tools;


use App\Util\BasicFunctions;
use App\World;
use Illuminate\Routing\Controller as BaseController;

class DistanceCalcController extends BaseController
{

    public function index($server, $world){
        BasicFunctions::local();
        $server = Server::getAndCheckServerByCode($server);
        $worldData = World::getAndCheckWorld($server, $world);

        if($worldData->config == null || $worldData->units == null) {
            abort(404, __('tool.distCalc.notAvailable'));
        }

        $unitConfig = $worldData->unitConfig();
        $config = simplexml_load_string($worldData->config);
        
        return view('tools.distanceCalc', compact('worldData', 'server', 'unitConfig', 'config'));

    }

}
