<?php

namespace App\Http\Controllers\API;

use App\Player;
use App\Conquer;
use App\World;
use App\Http\Controllers\Controller;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ConquerController extends Controller
{
    public function getAllyConquer($server, $world, $type, $allyID)
    {
        DatatablesController::limitResults(200);
        World::existWorld($server, $world);
        
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
                $filt = function($query) use($allyPlayers) {
                    $query->where(function($query) use($allyPlayers) {
                        $query->whereIn('new_owner', $allyPlayers)->orWhereIn('old_owner', $allyPlayers);
                    });
                };
                break;
            case "old":
                $filt = function($query) use($allyPlayers) {
                    $query->where(function($query) use($allyPlayers) {
                        $query->whereNotIn('new_owner', $allyPlayers)->whereIn('old_owner', $allyPlayers);
                    });
                };
                break;
            case "new":
                $filt = function($query) use($allyPlayers) {
                    $query->where(function($query) use($allyPlayers) {
                        $query->whereIn('new_owner', $allyPlayers)->whereNotIn('old_owner', $allyPlayers);
                    });
                };
                break;
            case "own":
                $filt = function($query) use($allyPlayers) {
                    $query->where(function($query) use($allyPlayers) {
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
        DatatablesController::limitResults(200);
        World::existWorld($server, $world);
        
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
                        $query->where('new_owner', '!=', $playerID)->where('old_owner', $playerID);
                    });
                };
                break;
            case "new":
                $filt = function($query) use($playerID) {
                    $query->where(function($query) use($playerID) {
                        $query->where('new_owner', $playerID)->where('old_owner', '!=', $playerID);
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
        DatatablesController::limitResults(200);
        World::existWorld($server, $world);
        
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
        DatatablesController::limitResults(200);
        World::existWorld($server, $world);
        
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
                $time = Carbon::createFromTimestamp($conquer->timestamp);
                return $this->multilineResponsiveTableCell($time->format('H:i:s'), $time->format('d-m-Y'));
            })
            ->addColumn('village', function (Conquer $conquer) use($world) {
                return $this->multilineResponsiveTableCell($conquer->linkVillageCoords($world), $conquer->linkVillageName($world), dT: true);
            })
            ->editColumn('old_owner_name', function (Conquer $conquer) use($world) {
                return $this->multilineResponsiveTableCell($conquer->linkOldPlayer($world), "[" . $conquer->linkOldAlly($world) . "]", uT: true);
            })
            ->editColumn('new_owner_name', function (Conquer $conquer) use($world) {
                return $this->multilineResponsiveTableCell($conquer->linkNewPlayer($world), "[" . $conquer->linkNewAlly($world) . "]", uT: true);
            })
            ->addColumn('type', function (Conquer $conquer) {
                return $conquer->getConquerType();
            })
            ->addColumn('winLoose', function (Conquer $conquer) use($referTO, $id) {
                return $conquer->getWinLoose($referTO, $id);
            })
            ->rawColumns(['timestamp', 'village', 'old_owner_name', 'new_owner_name'])
            ->removeColumn(['created_at', 'updated_at', 'old_owner', 'new_owner', 'old_ally_name', 'new_ally_name',
                'old_ally', 'new_ally', 'old_ally_tag', 'new_ally_tag'])
            ->toJson();
    }
    
    private function multilineResponsiveTableCell($up, $down, $uT=false, $dT=false) {
        $ret = "<div class='d-md-inline-block mr-1".($uT?' conquer-truncate':'')."'>$up</div>";
        $ret.= "<div class='d-md-inline-block".($dT?' conquer-truncate':'')."'>$down</div>";
        return $ret;
    }

    public function getConquerDaily($server, $world, $type, $day=false)
    {
        DatatablesController::limitResults(100);
        World::existWorld($server, $world);

        switch ($type) {
            case 'player':
                return $this->getConquerDailyPlayer($server, $world, $day);
                break;
            
            case 'ally':
                return $this->getConquerDailyAlly($server, $world, $day);
                break;
            
            default:
                abort(404);
        }
    }
    
    private function getConquerDailyPlayer($server, $world, $day) {
        $table = BasicFunctions::getDatabaseName($server, $world);
        $worldModel = World::getWorld($server, $world);
        
        $conquerModel = new Conquer();
        $conquerModel->setTable($table.'.conquer');
        $datas = $conquerModel->newQuery();
        
        $date = Carbon::createFromFormat('Y-m-d', $day ?? Carbon::now());;

        return DataTables::eloquent($datas)
            ->filter(function ($data) use ($table, $date){
                $data->where('timestamp', '>', $date->startOfDay()->getTimestamp())
                    ->where('timestamp', '<', $date->endOfDay()->getTimestamp())
                    ->select('new_owner', DB::raw('count(*) as total'))
                    ->groupBy('new_owner')
                    ->orderBy('total', 'desc')
                    ->get();
            })
            ->addIndexColumn()
            ->editColumn('name', function ($conquer) use ($worldModel, $conquerModel, $date){
                $referConquer = $conquerModel->where('new_owner', $conquer->new_owner)
                    ->where('timestamp', '>', $date->startOfDay()->getTimestamp())
                    ->where('timestamp', '<', $date->endOfDay()->getTimestamp())
                    ->first();
                
                return $referConquer->linkNewPlayer($worldModel);
            })
            ->addColumn('ally', function ($conquer) use ($worldModel, $conquerModel, $date){
                $referConquer = $conquerModel->where('new_owner', $conquer->new_owner)
                    ->where('timestamp', '>', $date->startOfDay()->getTimestamp())
                    ->where('timestamp', '<', $date->endOfDay()->getTimestamp())
                    ->first();
                
                return $referConquer->linkNewAlly($worldModel);
            })
            ->addColumn('total', function ($conquer){
                return $conquer->total;
            })
            ->rawColumns(['name', 'ally'])
            ->toJson();
    }
    
    private function getConquerDailyAlly($server, $world, $day) {
        $table = BasicFunctions::getDatabaseName($server, $world);
        $worldModel = World::getWorld($server, $world);
        
        $conquerModel = new Conquer();
        $conquerModel->setTable($table.'.conquer');
        $datas = $conquerModel->newQuery();
        
        $date = Carbon::createFromFormat('Y-m-d', $day ?? Carbon::now());;

        return DataTables::eloquent($datas)
            ->filter(function ($data) use ($table, $date){
                $data->where('timestamp', '>', $date->startOfDay()->getTimestamp())
                    ->where('timestamp', '<', $date->endOfDay()->getTimestamp())
                    ->where('new_ally', '!=', 0)
                    ->select('new_ally', DB::raw('count(*) as total'))
                    ->groupBy('new_ally')
                    ->orderBy('total', 'desc')
                    ->get();
            })
            ->addIndexColumn()
            ->editColumn('name', function ($conquer) use ($worldModel, $conquerModel, $date){
                $referConquer = $conquerModel->where('new_ally', $conquer->new_ally)
                    ->where('timestamp', '>', $date->startOfDay()->getTimestamp())
                    ->where('timestamp', '<', $date->endOfDay()->getTimestamp())
                    ->first();
                
                return $referConquer->linkNewAlly($worldModel, false);
            })
            ->addColumn('tag', function ($conquer) use ($worldModel, $conquerModel, $date){
                $referConquer = $conquerModel->where('new_ally', $conquer->new_ally)
                    ->where('timestamp', '>', $date->startOfDay()->getTimestamp())
                    ->where('timestamp', '<', $date->endOfDay()->getTimestamp())
                    ->first();
                
                return $referConquer->linkNewAlly($worldModel, true);
            })
            ->addColumn('total', function ($conquer){
                return $conquer->total;
            })
            ->rawColumns(['name', 'tag'])
            ->toJson();
    }
}
