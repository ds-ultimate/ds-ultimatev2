<?php

namespace App\Http\Controllers;

use App\Ally;
use App\Player;
use App\Util\BasicFunctions;
use App\World;
use Illuminate\Http\Request;

class SearchController extends Controller
{

    public function searchForm(Request $request)
    {
        if ($request->search != null) {
            return redirect()->route('search', [$request->submit, $request->search]);
        }else{
            return redirect()->back();
        }
    }

    public static function searchPlayer($search){
        $world = new World();
        $world->setTable(env('DB_DATABASE_MAIN').'.worlds');
        $worlds = $world->get();
        $player = new Player();
        $playerCollect = collect();

        foreach ($worlds as $world){
            $player->setTable(BasicFunctions::getDatabaseName(
                    $world->name,'').'.player_latest');
            foreach ($player->where('name', 'LIKE', '%'.$search.'%')->get() as $data){
                $players = collect();
                $players->put('world', $world);
                $players->put('player', $data);
                $playerCollect->push($players);
            }
        }
        return $playerCollect;
    }

    public static function searchAlly($search){
        $world = new World();
        $world->setTable(env('DB_DATABASE_MAIN').'.worlds');
        $worlds = $world->get();
        $ally = new Ally();
        $allyCollect = collect();

        foreach ($worlds as $world){
            $ally->setTable(BasicFunctions::getDatabaseName(
                    $world->name,'').'.ally_latest');
            foreach ($ally->where('name', 'LIKE', '%'.$search.'%')->get() as $data){
                $allys = collect();
                $allys->put('world', $world);
                $allys->put('ally', $data);
                $allyCollect->push($allys);
            }
        }
        return $allyCollect;
    }
}
