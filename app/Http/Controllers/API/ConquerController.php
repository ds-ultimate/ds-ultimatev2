<?php

namespace App\Http\Controllers\API;

use App\Player;
use App\Conquer;
use App\World;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ConquerController extends Controller
{
    private static $whitelist = ['village', 'old_owner_name', 'new_owner_name', 'points', 'timestamp', 'old_ally_name', 'new_ally_name', 'old_ally_tag', 'new_ally_tag'];

    public function getAllyConquer(World $world, $type, $allyID) {
        DatatablesController::limitResults(200, static::$whitelist);

        $conquerModel = new Conquer($world);
        $playerModel = new Player($world);

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
                abort(404, __("ui.errors.404.unknownType", ["type" => $type]));
        }

        return $this->doConquerReturn($query, $world, Conquer::$REFERTO_ALLY, $allyID, $filt);
    }

    public function getPlayerConquer(World $world, $type, $playerID) {
        DatatablesController::limitResults(200, static::$whitelist);

        $conquerModel = new Conquer($world);
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
                abort(404, __("ui.errors.404.unknownType", ["type" => $type]));
        }

        return $this->doConquerReturn($query, $world, Conquer::$REFERTO_PLAYER, $playerID, $filt);
    }

    public function getVillageConquer(World $world, $type, $villageID) {
        DatatablesController::limitResults(200, static::$whitelist);

        $conquerModel = new Conquer($world);
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
                abort(404, __("ui.errors.404.unknownType", ["type" => $type]));
        }

        return $this->doConquerReturn($query, $world, Conquer::$REFERTO_VILLAGE, $villageID, $filt);
    }

    public function getWorldConquer(World $world, $type) {
        DatatablesController::limitResults(200, static::$whitelist);

        $conquerModel = new Conquer($world);
        $query = $conquerModel->newQuery();

        switch($type) {
            case "all":
                break;
            default:
                abort(404, __("ui.errors.404.unknownType", ["type" => $type]));
        }

        return $this->doConquerReturn($query, $world, Conquer::$REFERTO_VILLAGE, 0);
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
                return "<div class='d-md-inline-block mr-1 conquer-truncate'>".$conquer->linkOldPlayer($world)."</div>";
            })
            ->editColumn('new_owner_name', function (Conquer $conquer) use($world) {
                return "<div class='d-md-inline-block mr-1 conquer-truncate'>".$conquer->linkNewPlayer($world)."</div>";
            })
            ->editColumn('old_ally_tag', function (Conquer $conquer) use($world) {
                return $conquer->linkOldAlly($world);
            })
            ->editColumn('new_ally_tag', function (Conquer $conquer) use($world) {
                return $conquer->linkNewAlly($world);
            })
            ->addColumn('type', function (Conquer $conquer) {
                return $conquer->getConquerType();
            })
            ->addColumn('winLoose', function (Conquer $conquer) use($referTO, $id) {
                return $conquer->getWinLoose($referTO, $id);
            })
            ->rawColumns(['timestamp', 'village', 'old_owner_name', 'new_owner_name', 'old_ally_tag', 'new_ally_tag'])
            ->removeColumn(['created_at', 'updated_at', 'old_owner', 'new_owner',
                'old_ally', 'new_ally'])
            ->whitelist(static::$whitelist)
            ->toJson();
    }

    private function multilineResponsiveTableCell($up, $down, $uT=false, $dT=false) {
        $ret = "<div class='d-md-inline-block mr-1".($uT?' conquer-truncate':'')."'>$up</div>";
        $ret.= "<div class='d-md-inline-block".($dT?' conquer-truncate':'')."'>$down</div>";
        return $ret;
    }

    public function getConquerDaily(World $world, $type, $day=false) {
        switch ($type) {
            case 'player':
                $wl = ['DT_RowIndex', 'name', 'ally', 'total'];
                $wl_search = ['name'];
                DatatablesController::limitResults(200, $wl);
                return $this->getConquerDailyPlayer($world, $day, $wl_search);
                break;

            case 'ally':
                $wl = ['DT_RowIndex', 'name', 'tag', 'total'];
                $wl_search = ['name'];
                DatatablesController::limitResults(200, $wl);
                return $this->getConquerDailyAlly($world, $day, $wl_search);
                break;

            default:
                abort(404, __("ui.errors.404.unknownType", ["type" => $type]));
        }
    }

    private function getConquerDailyPlayer(World $worldData, $day, $wl) {
        $conquerModel = new Conquer($worldData);
        $datas = $conquerModel->newQuery();

        $date = Carbon::createFromFormat('Y-m-d', $day ?? Carbon::now());;

        return DataTables::eloquent($datas)
            ->filter(function ($data) use ($date){
                $data->where('timestamp', '>', $date->startOfDay()->getTimestamp())
                    ->where('timestamp', '<', $date->endOfDay()->getTimestamp())
                    ->select('new_owner', DB::raw('count(*) as total'))
                    ->groupBy('new_owner')
                    ->orderBy('total', 'desc')
                    ->get();
            })
            ->addIndexColumn()
            ->editColumn('name', function ($conquer) use ($worldData, $conquerModel, $date){
                $referConquer = $conquerModel->where('new_owner', $conquer->new_owner)
                    ->where('timestamp', '>', $date->startOfDay()->getTimestamp())
                    ->where('timestamp', '<', $date->endOfDay()->getTimestamp())
                    ->first();

                return $referConquer->linkNewPlayer($worldData);
            })
            ->addColumn('ally', function ($conquer) use ($worldData, $conquerModel, $date){
                $referConquer = $conquerModel->where('new_owner', $conquer->new_owner)
                    ->where('timestamp', '>', $date->startOfDay()->getTimestamp())
                    ->where('timestamp', '<', $date->endOfDay()->getTimestamp())
                    ->first();

                return $referConquer->linkNewAlly($worldData);
            })
            ->addColumn('total', function ($conquer){
                return $conquer->total;
            })
            ->rawColumns(['name', 'ally'])
            ->whitelist($wl)
            ->toJson();
    }

    private function getConquerDailyAlly(World $worldData, $day, $wl) {
        $conquerModel = new Conquer($worldData);
        $datas = $conquerModel->newQuery();

        $date = Carbon::createFromFormat('Y-m-d', $day ?? Carbon::now());;

        return DataTables::eloquent($datas)
            ->filter(function ($data) use ($date){
                $data->where('timestamp', '>', $date->startOfDay()->getTimestamp())
                    ->where('timestamp', '<', $date->endOfDay()->getTimestamp())
                    ->where('new_ally', '!=', 0)
                    ->select('new_ally', DB::raw('count(*) as total'))
                    ->groupBy('new_ally')
                    ->orderBy('total', 'desc')
                    ->get();
            })
            ->addIndexColumn()
            ->editColumn('name', function ($conquer) use ($worldData, $conquerModel, $date){
                $referConquer = $conquerModel->where('new_ally', $conquer->new_ally)
                    ->where('timestamp', '>', $date->startOfDay()->getTimestamp())
                    ->where('timestamp', '<', $date->endOfDay()->getTimestamp())
                    ->first();

                return $referConquer->linkNewAlly($worldData, false);
            })
            ->addColumn('tag', function ($conquer) use ($worldData, $conquerModel, $date){
                $referConquer = $conquerModel->where('new_ally', $conquer->new_ally)
                    ->where('timestamp', '>', $date->startOfDay()->getTimestamp())
                    ->where('timestamp', '<', $date->endOfDay()->getTimestamp())
                    ->first();

                return $referConquer->linkNewAlly($worldData, true);
            })
            ->addColumn('total', function ($conquer){
                return $conquer->total;
            })
            ->rawColumns(['name', 'tag'])
            ->whitelist($wl)
            ->toJson();
    }
}
