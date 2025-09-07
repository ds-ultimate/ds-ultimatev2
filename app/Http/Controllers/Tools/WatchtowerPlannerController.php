<?php

namespace App\Http\Controllers\Tools;

use App\Server;
use App\World;
use Illuminate\Routing\Controller as BaseController;

class WatchtowerPlannerController extends BaseController
{

    public function index($server, $world){
        $server = Server::getAndCheckServerByCode($server);
        $worldData = World::getAndCheckWorld($server, $world);
        abort_if($worldData->config == null || $worldData->units == null || $worldData->configData()->game->watchtower == 0, 404, __("ui.errors.404.toolNotAvail.watchtowerPlaner"));

        $unitConfig = $worldData->unitConfig();
        $config = simplexml_load_string($worldData->config);

        return view('tools.watchtowerPlanner', compact('worldData', 'server', 'unitConfig', 'config'));

    }
}
