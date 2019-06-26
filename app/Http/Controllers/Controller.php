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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\Facades\DataTables;

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

        $worldData = World::getWorldCollection($server, $world);

        return view('content.worldAlly', compact('allyArray', 'worldData'));
    }

    /*
     * https://ds-ultimate.de/de/164/players
     * */
    public function players($server, $world){
        BasicFunctions::local();
        World::existServer($server);
        World::existWorld($server, $world);

        $worldData = World::getWorldCollection($server, $world);

        return view('content.worldPlayer', compact('playerArray', 'worldData'));
    }

    public function search($search){
        //return view('content.search', compact('search'));
        $world = new World();
        $world->setTable(env('DB_DATABASE_MAIN').'.worlds');
        $worlds = $world->get();
        $player = new Player();
        $playerCollect = new Collection();

        foreach ($worlds as $world){
            $replaceArray = array(
                '{server}' => BasicFunctions::getServer($world->name),
                '{world}' => BasicFunctions::getWorldID($world->name)
            );
            $player->setTable(str_replace(array_keys($replaceArray), array_values($replaceArray), env('DB_DATABASE_WORLD', 'c1welt_{server}{world}').'.player_latest'));
            foreach ($player->where('name', 'LIKE', '%'.$search.'%')->get() as $data){
                $playerCollect = $playerCollect->merge($data);
            }
        }
        var_dump($playerCollect);
        exit;
        return DataTables::Eloquent($playerCollect);
    }

}
