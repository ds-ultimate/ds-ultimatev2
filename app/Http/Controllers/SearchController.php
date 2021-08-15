<?php

namespace App\Http\Controllers;

use App\AllyTop;
use App\Playerop;
use App\Village;
use App\Server;
use App\Util\BasicFunctions;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public static $limit = 100;
    
    public function searchForm(Request $request, $server)
    {
        if ($request->search != null) {
            return redirect()->route('search', [$server, rawurlencode($request->submit), rawurlencode($request->search)]);
        }else{
            return redirect()->back();
        }
    }

    public function search($server, $type, $search){
        BasicFunctions::local();
        switch ($type){
            case 'player':
                $result = SearchController::searchPlayer($server, $search);
                return view('content.search', compact('search', 'type', 'result', 'server'));
            case 'ally':
                $result = SearchController::searchAlly($server, $search);
                return view('content.search', compact('search', 'type', 'result', 'server'));
            case 'village':
                $result = SearchController::searchVillage($server, $search);
                return view('content.search', compact('search', 'type', 'result', 'server'));
        }
    }

    public static function searchPlayer($server, $search){
        $worlds = Server::getWorldsByCode($server);
        $player = new PlayerTop();
        $playerCollect = collect();

        foreach ($worlds as $world){
            $player->setTable(BasicFunctions::getDatabaseName($world->server->code, $world->name).'.player_top');
            foreach ($player->where('name', 'LIKE', '%'. BasicFunctions::likeSaveEscape(urlencode($search)).'%')->get() as $data){
                $players = collect();
                $players->put('world', $world);
                $players->put('player', $data);
                $playerCollect->push($players);
                
                if($playerCollect->count() >= SearchController::$limit)
                    return $playerCollect;
            }
        }
        return $playerCollect;
    }

    public static function searchAlly($server, $search){
        $worlds = Server::getWorldsByCode($server);
        $ally = new AllyTop();
        $allyCollect = collect();

        foreach ($worlds as $world){
            $ally->setTable(BasicFunctions::getDatabaseName($world->server->code, $world->name).'.ally_top');
            foreach ($ally->where('name', 'LIKE', '%'.BasicFunctions::likeSaveEscape(urlencode($search)).'%')->get() as $data){
                $allys = collect();
                $allys->put('world', $world);
                $allys->put('ally', $data);
                $allyCollect->push($allys);
                
                if($allyCollect->count() >= SearchController::$limit)
                    return $allyCollect;
            }
        }
        return $allyCollect;
    }

    public static function searchVillage($server, $search){
        $worlds = Server::getWorldsByCode($server);
        $village = new Village();
        $villageCollect = collect();

        $coordsearch = false;
        if(strpos($search, '|') !== false) {
            //Coordinates search
            $temp = explode("|", $search);
            if(count($temp) == 2
                    && ctype_digit($temp[0]) && ctype_digit($temp[1])
                    && strlen($temp[0]) > 1 && strlen($temp[1]) > 1) {
                $coordsearch = true;
                $searchExp = $temp;
            }
        }
        
        foreach ($worlds as $world){
            $village->setTable(BasicFunctions::getDatabaseName($world->server->code, $world->name).'.village_latest');
            foreach ($village->where('name', 'LIKE', '%'.BasicFunctions::likeSaveEscape(urlencode($search)).'%')->get() as $data){
                $villages = collect();
                $villages->put('world', $world);
                $villages->put('village', $data);
                $villageCollect->push($villages);
                
                if($villageCollect->count() >= SearchController::$limit)
                    return $villageCollect;
            }

            if($coordsearch) {
                foreach ($village->where('x', 'LIKE', '%'.BasicFunctions::likeSaveEscape($searchExp[0]).'%')
                        ->where('y', 'LIKE', '%'.BasicFunctions::likeSaveEscape($searchExp[1]).'%')->get() as $data){
                    $villages = collect();
                    $villages->put('world', $world);
                    $villages->put('village', $data);
                    $villageCollect->push($villages);
                    
                    if($villageCollect->count() >= SearchController::$limit)
                        return $villageCollect;
                }
            }
        }
        return $villageCollect;
    }
}
