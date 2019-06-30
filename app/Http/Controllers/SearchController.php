<?php

namespace App\Http\Controllers;

use App\Ally;
use App\Player;
use App\Util\BasicFunctions;
use App\World;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

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
            $replaceArray = array(
                '{server}' => BasicFunctions::getServer($world->name),
                '{world}' => BasicFunctions::getWorldID($world->name)
            );
            $player->setTable(str_replace(array_keys($replaceArray), array_values($replaceArray), env('DB_DATABASE_WORLD', 'c1welt_{server}{world}').'.player_latest'));
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
            $replaceArray = array(
                '{server}' => BasicFunctions::getServer($world->name),
                '{world}' => BasicFunctions::getWorldID($world->name)
            );
            $ally->setTable(str_replace(array_keys($replaceArray), array_values($replaceArray), env('DB_DATABASE_WORLD', 'c1welt_{server}{world}').'.ally_latest'));
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
