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
        $filt = null;
        
        switch($type) {
            case "all":
                $filt = function($query) use($playerID) {
                    $query->where(function($query) use($playerID) {
                        $query->whereIn('new_owner', $allyPlayers)->orWhereIn('old_owner', $allyPlayers);
                    });
                };
                break;
            case "old":
                $filt = function($query) use($playerID) {
                    $query->where(function($query) use($playerID) {
                        $query->whereIn('new_owner', $allyPlayers)->whereNotIn('old_owner', $allyPlayers);
                    });
                };
                break;
            case "new":
                $filt = function($query) use($playerID) {
                    $query->where(function($query) use($playerID) {
                        $query->whereNotIn('new_owner', $allyPlayers)->whereIn('old_owner', $allyPlayers);
                    });
                };
                break;
            case "own":
                $filt = function($query) use($playerID) {
                    $query->where(function($query) use($playerID) {
                        $query->whereIn('new_owner', $allyPlayers)->whereIn('old_owner', $allyPlayers);
                    });
                };
                break;
            default:
                abort(404, "Unknown type");
        }

        return $this->doConquerReturn($query, World::getWorld($server, $world), Conquer::$REFERTO_ALLY, $allyID, $filt);
    }

    public function getPlayerConquer($server, $world, $type, $playerID)
    {
        $conquerModel = new Conquer();
        $conquerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.conquer');

        $query = $conquerModel->newQuery();
        $filt = null;

        switch($type) {
            case "all":
                $filt = function($query) use($playerID) {
                    $query->where(function($query) use($playerID) {
                        $query->where('new_owner', $playerID)->orWhere('old_owner', $playerID);
                    });
                };
                break;
            case "old":
                $filt = function($query) use($playerID) {
                    $query->where(function($query) use($playerID) {
                        $query->where('new_owner', '=', $playerID)->where('old_owner', '!=', $playerID);
                    });
                };
                break;
            case "new":
                $filt = function($query) use($playerID) {
                    $query->where(function($query) use($playerID) {
                        $query->where('new_owner', '!=', $playerID)->where('old_owner', '=', $playerID);
                    });
                };
                break;
            case "own":
                $filt = function($query) use($playerID) {
                    $query->where(function($query) use($playerID) {
                        $query->where('new_owner', $playerID)->where('old_owner', $playerID);
                    });
                };
                break;
            default:
                abort(404, "Unknown type");
        }
        
        return $this->doConquerReturn($query, World::getWorld($server, $world), Conquer::$REFERTO_PLAYER, $playerID, $filt);
    }

    public function getVillageConquer($server, $world, $type, $villageID)
    {
        $conquerModel = new Conquer();
        $conquerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.conquer');

        $query = $conquerModel->newQuery();
        $filt = null;

        switch($type) { 
           case "all":
                $filt = function($query) use($villageID) {
                    $query->where(function($query) use($villageID) {
                        $query->where('village_id', $villageID);
                    });
                };
                break;
            default:
                abort(404, "Unknown type");
        }
        
        return $this->doConquerReturn($query, World::getWorld($server, $world), Conquer::$REFERTO_VILLAGE, $villageID, $filt);
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
    
    private function doConquerReturn($query, $world, $referTO, $id, $filter=null) {
        $data = DataTables::eloquent($query);
        if($filter !== null) {
            $data = $data->filter($filter, true);
        }
        return $data
            ->editColumn('timestamp', function (Conquer $conquer){
                return Carbon::createFromTimestamp($conquer->timestamp)->format('Y-m-d H:i:s');
            })
            ->addColumn('village', function (Conquer $conquer) use($world) {
                return $conquer->linkVillageCoordsWithName($world);
            })
            ->editColumn('old_owner_name', function (Conquer $conquer) use($world) {
                return $conquer->linkOldPlayer($world);
            })
            ->editColumn('new_owner_name', function (Conquer $conquer) use($world) {
                return $conquer->linkNewPlayer($world);
            })
            ->editColumn('old_ally_name', function (Conquer $conquer) use($world) {
                return $conquer->linkOldAlly($world);
            })
            ->editColumn('new_ally_name', function (Conquer $conquer) use($world) {
                return $conquer->linkNewAlly($world);
            })
            ->addColumn('type', function (Conquer $conquer) {
                return $conquer->getConquerType();
            })
            ->addColumn('winLoose', function (Conquer $conquer) use($referTO, $id) {
                return $conquer->getWinLoose($referTO, $id);
            })
            ->rawColumns(['village', 'old_owner_name', 'new_owner_name', 'old_ally_name', 'new_ally_name'])
            ->removeColumn('created_at')->removeColumn('updated_at')
            ->removeColumn('new_owner')->removeColumn('old_owner')
            ->removeColumn('old_ally')->removeColumn('new_ally')
            ->removeColumn('old_ally_tag')->removeColumn('new_ally_tag')
            ->toJson();
    }
}
