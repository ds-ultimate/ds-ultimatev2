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

        abort_if($worldData->config == null || $worldData->units == null, 404, __("ui.errors.404.toolNotAvail.distCalc"));

        $unitConfig = $worldData->unitConfig();
        $config = simplexml_load_string($worldData->config);
        
        return view('tools.distanceCalc', compact('worldData', 'server', 'unitConfig', 'config'));

    }

}
