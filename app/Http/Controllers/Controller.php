<?php

namespace App\Http\Controllers;

use App;
use App\Ally;
use App\Player;
use App\Util\BasicFunctions;
use App\World;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /*
     * https://ds-ultimate.de/de
     * */
    public function server($server){
        BasicFunctions::local();
        World::existServer($server);
        $worldsArray = World::worldsCollection($server);
        return view('content.server', compact('worldsArray'));
    }

    /*
     * https://ds-ultimate.de/de/164
     * */
    public function world($server, $world){
        BasicFunctions::local();
        World::existServer($server);
        World::existWorld($server, $world);

        $playerArray = Player::top10Player($server, $world);
        $allyArray = Ally::top10Ally($server, $world);
        $worldData = World::getWorldCollection($server, $world);

        return view('content.world', compact('playerArray', 'allyArray', 'worldData'));

    }

    /*
     * https://ds-ultimate.de/de/164/allys
     * */
    public function allys($server, $world){
        BasicFunctions::local();
        World::existServer($server);
        World::existWorld($server, $world);

        $allyArray = Ally::getAllyAll($server, $world);
        $worldData = World::getWorldCollection($server, $world);

        return view('content.worldAlly', compact('allyArray', 'worldData'));
    }

    /*
     * https://ds-ultimate.de/de/164/players
     * */
    public function players($server, $world, $page){
        BasicFunctions::local();
        World::existServer($server);
        World::existWorld($server, $world);

        $playerArray = Player::getAllyPlayer($server, $world, 'rank', $page);
        $worldData = World::getWorldCollection($server, $world);

        return view('content.worldPlayer', compact('playerArray', 'worldData', 'page'));
    }

}
