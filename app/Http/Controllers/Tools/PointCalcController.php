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

class PointCalcController extends BaseController
{

    public function index($server, $world){
        BasicFunctions::local();
        World::existWorld($server, $world);

        $worldData = World::getWorld($server, $world);
        if($worldData->config == null) {
            //TODO real error blade here
            return "Der Punkterechner ist für diese Welt nicht verfügbar";
        }
        if($worldData->buildings == null) {
            //TODO real error blade here
            return "Der Punkterechner ist für diese Welt nicht verfügbar";
        }

        $buildConfig = simplexml_load_string($worldData->buildings);
        $config = simplexml_load_string($worldData->config);
        
        return view('tools.pointCalc', compact('worldData', 'server', 'buildConfig', 'config'));

    }

}
