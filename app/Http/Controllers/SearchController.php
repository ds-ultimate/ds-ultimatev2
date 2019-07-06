<?php

namespace App\Http\Controllers;

use App\Ally;
use App\Player;
use App\Server;
use App\Util\BasicFunctions;
use Illuminate\Http\Request;

class SearchController extends Controller
{

    public function searchForm(Request $request, $server)
    {
        if ($request->search != null) {
            return redirect()->route('search', [$server, $request->submit, $request->search]);
        }else{
            return redirect()->back();
        }
    }

    public static function searchPlayer($server, $search){
        $worlds = Server::getWorldsByCode($server);
        $player = new Player();
        $playerCollect = collect();

        foreach ($worlds as $world){
            $player->setTable(BasicFunctions::getDatabaseName($world->server->code, $world->name).'.player_latest');
            foreach ($player->where('name', 'LIKE', '%'. BasicFunctions::likeSaveEscape(urlencode($search)).'%')->get() as $data){
                $players = collect();
                $players->put('world', $world);
                $players->put('player', $data);
                $playerCollect->push($players);
            }
        }
        return $playerCollect;
    }

    public static function searchAlly($server, $search){
        $worlds = Server::getWorldsByCode($server);
        $ally = new Ally();
        $allyCollect = collect();

        foreach ($worlds as $world){
            $ally->setTable(BasicFunctions::getDatabaseName($world->server->code, $world->name).'.ally_latest');
            foreach ($ally->where('name', 'LIKE', '%'.BasicFunctions::likeSaveEscape(urlencode($search)).'%')->get() as $data){
                $allys = collect();
                $allys->put('world', $world);
                $allys->put('ally', $data);
                $allyCollect->push($allys);
            }
        }
        return $allyCollect;
    }
}
