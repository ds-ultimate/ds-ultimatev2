<?php
/**
 * Created by IntelliJ IDEA.
 * User: crams
 * Date: 10.08.2019
 * Time: 20:38
 */

namespace App\Http\Controllers\Tools;


use App\Server;
use App\World;
use Illuminate\Routing\Controller as BaseController;

class FightSimulatorController extends BaseController
{

    public function index($server, $world){
        // ui.errors.404.toolNotAvail.fightSimulator
        // tool.fightSimulator.title
        $server = Server::getAndCheckServerByCode($server);
        $worldData = World::getAndCheckWorld($server, $world);

        $config = $worldData->configData();
        $unitConfig = $worldData->unitConfig();
        abort_if($config == null || $unitConfig == null, 404, __("ui.errors.404.toolNotAvail.fightSimulator"));

        $worldUnits = $this->getAvailableUnits($unitConfig);

        return view('tools.fightSimulator', compact('worldData', 'server', 'config', 'unitConfig', 'worldUnits'));
    }
    
    private function getAvailableUnits($unitConfig) {
        $units = [];

        foreach($unitConfig as $unit => $conf) {
            $units[] = $unit;
        }

        return $units;
    }
}
