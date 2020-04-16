<?php

namespace App\Http\Controllers\API;

use App\World;
use App\Player;
use App\Conquer;
use App\Http\Controllers\Controller;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class ConquerController extends Controller
{
    public function getAllyConquer($server, $world, $type, $allyID)
    {
        $conquerModel = new Conquer();
        $conquerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.conquer');

        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_latest');

        $allyPlayers = array();
        foreach ($playerModel->newQuery()->where('ally_id', $allyID)->get() as $player) {
            $allyPlayers[] = $player->playerID;
        }
        
        $query = $conquerModel->newQuery();
        
        switch($type) {
            case "all":
                $query->whereIn('new_owner', $allyPlayers)->orWhereIn('old_owner', $allyPlayers);
                break;
            case "old":
                $query->whereIn('old_owner', $allyPlayers)->whereNotIn('new_owner', $allyPlayers);
                break;
            case "new":
                $query->whereNotIn('old_owner', $allyPlayers)->whereIn('new_owner', $allyPlayers);
                break;
            case "own":
                $query->whereIn('old_owner', $allyPlayers)->whereIn('new_owner', $allyPlayers);
                break;
            default:
                abort(404, "Unknown type");
        }

        return $this->doConquerReturn($query, World::getWorld($server, $world), Conquer::$REFERTO_ALLY, $allyID);
    }

    public function getPlayerConquer($server, $world, $type, $playerID)
    {
        $conquerModel = new Conquer();
        $conquerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.conquer');

        $query = $conquerModel->newQuery();

        switch($type) {
            case "all":
                $query->where('new_owner', $playerID)->orWhere('old_owner', $playerID);
                break;
            case "old":
                $query->where([['old_owner', "=", $playerID],['new_owner', '!=', $playerID]]);
                break;
            case "new":
                $query->where([['old_owner', "!=", $playerID],['new_owner', '=', $playerID]]);
                break;
            case "own":
                $query->where([['old_owner', "=", $playerID],['new_owner', '=', $playerID]]);
                break;
            default:
                abort(404, "Unknown type");
        }
        
        return $this->doConquerReturn($query, World::getWorld($server, $world), Conquer::$REFERTO_PLAYER, $playerID);
    }

    public function getVillageConquer($server, $world, $type, $villageID)
    {
        $conquerModel = new Conquer();
        $conquerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.conquer');

        $query = $conquerModel->newQuery();

        switch($type) {
            case "all":
                $query->where('village_id', $villageID);
                break;
            default:
                abort(404, "Unknown type");
        }
        
        return $this->doConquerReturn($query, World::getWorld($server, $world), Conquer::$REFERTO_VILLAGE, $villageID);
    }

    public function getWorldConquer($server, $world, $type)
    {
        $conquerModel = new Conquer();
        $conquerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.conquer');

        $query = $conquerModel->newQuery();

        switch($type) {
            case "all":
                break;
            default:
                abort(404, "Unknown type");
        }
        
        return $this->doConquerReturn($query, World::getWorld($server, $world), Conquer::$REFERTO_VILLAGE, 0);
    }
    
    private function doConquerReturn($query, $world, $referTO, $id) {
        return DataTables::eloquent($query)
            ->editColumn('timestamp', function (Conquer $conquer){
                return Carbon::createFromTimestamp($conquer->timestamp)->format('Y-m-d H:i:s');;
            })
            ->addColumn('village_html', function (Conquer $conquer) use($world) {
                return $conquer->linkVillageCoordsWithName($world);
            })
            ->addColumn('old_owner_html', function (Conquer $conquer) use($world) {
                return $conquer->linkOldPlayer($world);
            })
            ->addColumn('new_owner_html', function (Conquer $conquer) use($world) {
                return $conquer->linkNewPlayer($world);
            })
            ->addColumn('old_owner_ally_html', function (Conquer $conquer) use($world) {
                return $conquer->linkOldAlly($world);
            })
            ->addColumn('new_owner_ally_html', function (Conquer $conquer) use($world) {
                return $conquer->linkNewAlly($world);
            })
            ->addColumn('type', function (Conquer $conquer) {
                return $conquer->getConquerType();
            })
            ->addColumn('winLoose', function (Conquer $conquer) use($referTO, $id) {
                return $conquer->getWinLoose($referTO, $id);
            })
            ->rawColumns(['village_html', 'old_owner_html', 'new_owner_html', 'old_owner_ally_html', 'new_owner_ally_html'])
            ->removeColumn('created_at')->removeColumn('updated_at')
            ->removeColumn('new_owner')->removeColumn('old_owner')
            ->removeColumn('old_owner_name')->removeColumn('new_owner_name')
            ->removeColumn('old_ally')->removeColumn('new_ally')
            ->removeColumn('old_ally_name')->removeColumn('new_ally_name')
            ->removeColumn('old_ally_tag')->removeColumn('new_ally_tag')
            ->toJson();
    }
}
