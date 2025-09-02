<?php

namespace App\Http\Controllers\Tools;

use App\Server;
use App\World;
use Illuminate\Routing\Controller as BaseController;

class CoinCalcController extends BaseController
{
    public function getCoinCosts(World $worldData): array
    {
        $config = $worldData->configData();
        if ($config === null || !isset($config->snob)) {
            return ['wood' => 0, 'stone' => 0, 'iron' => 0];
        }
        return [
            'wood' => (int)$config->snob->coin_wood,
            'stone' => (int)$config->snob->coin_stone,
            'iron' => (int)$config->snob->coin_iron,
        ];
    }

    public function usesGoldCoins(World $worldData): bool
    {
        $config = $worldData->configData();
        if ($config === null || !isset($config->snob)) {
            return false;
        }
        return ((int)$config->snob->gold) === 1;
    }

    public function index($server, $world)
    {
        $server = Server::getAndCheckServerByCode($server);
        $worldData = World::getAndCheckWorld($server, $world);
        abort_if($worldData->config == null || !$this->usesGoldCoins($worldData), 404, __('ui.errors.404.toolNotAvail.coinCalc'));

        $coinCost = $this->getCoinCosts($worldData);

        return view('tools.coinCalc', compact('worldData', 'server', 'coinCost'));
    }
}
