<?php

namespace App\Http\Controllers;

use App\AllyTop;
use App\PlayerTop;
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
        } else{
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
        $allPlayer = [];

        foreach ($worlds as $world){
            $player->setTable(BasicFunctions::getDatabaseName($world->server->code, $world->name).'.player_top');
            foreach ($player->where('name', 'LIKE', '%'. BasicFunctions::likeSaveEscape(urlencode($search)).'%')->get() as $data){
                $allPlayer = [
                    'world' => $world,
                    'player' => $data,
                ];
                
                if(count($allPlayer) >= SearchController::$limit)
                    return $allPlayer;
            }
        }
        return $allPlayer;
    }

    public static function searchAlly($server, $search){
        $worlds = Server::getWorldsByCode($server);
        $ally = new AllyTop();
        $allAlly = [];

        foreach ($worlds as $world){
            $ally->setTable(BasicFunctions::getDatabaseName($world->server->code, $world->name).'.ally_top');
            foreach ($ally->where('name', 'LIKE', '%'.BasicFunctions::likeSaveEscape(urlencode($search)).'%')->get() as $data){
                $allAlly = [
                    'world' => $world,
                    'ally' => $data,
                ];
                
                if(count($allAlly) >= SearchController::$limit)
                    return $allAlly;
            }
        }
        return $allAlly;
    }

    public static function searchVillage($server, $search){
        $worlds = Server::getWorldsByCode($server);
        $village = new Village();
        $allVillage = [];

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
                $allVillage = [
                    'world' => $world,
                    'village' => $data,
                ];
                
                if(count($allVillage) >= SearchController::$limit)
                    return $allVillage;
            }

            if($coordsearch) {
                foreach ($village->where('x', 'LIKE', '%'.BasicFunctions::likeSaveEscape($searchExp[0]).'%')
                        ->where('y', 'LIKE', '%'.BasicFunctions::likeSaveEscape($searchExp[1]).'%')->get() as $data){
                    $allVillage = [
                        'world' => $world,
                        'village' => $data,
                    ];

                    if(count($allVillage) >= SearchController::$limit)
                        return $allVillage;
                }
            }
        }
        return $allVillage;
    }
}
