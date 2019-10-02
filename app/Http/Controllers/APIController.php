<?php

namespace App\Http\Controllers;

use App\Ally;
use App\World;
use App\Player;
use App\Server;
use App\Village;
use App\Conquer;
use App\AllyChanges;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Resources\World as WorldResource;
use App\Http\Resources\Ally as AllyResource;
use App\Http\Resources\Player as PlayerResource;
use App\Http\Resources\Village as VillageResource;

class APIController extends Controller
{
    public function getPlayers($server, $world)
    {
        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_latest');

        $datas = $playerModel->newQuery();

        return DataTables::eloquent($datas)
            ->editColumn('name', function ($player){
                return BasicFunctions::decodeName($player->name);
            })
            ->addColumn('ally', function ($player){
                return ($player->ally_id != 0)? BasicFunctions::decodeName($player->allyLatest->tag) : '-';
            })
            ->addColumn('village_points', function ($player){
                return ($player->points == 0 || $player->village_count == 0)? 0 : ($player->points/$player->village_count);
            })
            ->addColumn('utBash', function ($player){
                return $player->gesBash - $player->offBash - $player->defBash;
            })
            ->toJson();
    }

    public function getAllys($server, $world)
    {
        $allyModel = new Ally();
        $allyModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.ally_latest');

        $datas = $allyModel->newQuery();

        return DataTables::eloquent($datas)
            ->editColumn('name', function ($ally){
                return BasicFunctions::decodeName($ally->name);
            })
            ->editColumn('tag', function ($ally){
                return BasicFunctions::decodeName($ally->tag);
            })
            ->addColumn('player_points', function ($ally){
                return ($ally->points == 0 || $ally->member_count == 0)? 0 : ($ally->points/$ally->member_count);
            })
            ->addColumn('village_points', function ($ally){
                return ($ally->points == 0 || $ally->village_count == 0)? 0 : ($ally->points/$ally->village_count);
            })
            ->toJson();
    }

    public function getAllyPlayer($server, $world, $ally)
    {
        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_latest');

        $querry = $playerModel->newQuery();
        $querry->where('ally_id', $ally);

        return DataTables::eloquent($querry)
            ->editColumn('name', function ($player){
                return BasicFunctions::decodeName($player->name);
            })
            ->addColumn('ally', function ($player){
                return ($player->ally_id != 0)? BasicFunctions::decodeName($player->allyLatest->tag) : '-';
            })
            ->addColumn('village_points', function ($player){
                return ($player->points == 0 || $player->village_count == 0)? 0 : ($player->points/$player->village_count);
            })
            ->addColumn('utBash', function ($player){
                return $player->gesBash - $player->offBash - $player->defBash;
            })
            ->toJson();
    }

    public function getPlayerVillage($server, $world, $player)
    {
        $villageModel = new Village();
        $villageModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.village_latest');

        $query = $villageModel->newQuery();
        $query->where('owner', $player);

        return DataTables::eloquent($query)
            ->editColumn('name', function ($village){
                return BasicFunctions::decodeName($village->name);
            })
            ->addColumn('player', function ($village){
                return ($village->owner != 0)? BasicFunctions::decodeName($village->playerLatest->name) : '-';
            })
            ->addColumn('continent', function ($village){
                return $village->continentString();
            })
            ->addColumn('coordinates', function ($village){
                return $village->coordinates();
            })
            ->addColumn('bonus', function ($village){
                return $village->bonusText();
            })
            ->toJson();
    }

    public function getAllyAllyChanges($server, $world, $type, $allyID)
    {
        $allyChangesModel = new AllyChanges();
        $allyChangesModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.ally_changes');

        $query = $allyChangesModel->newQuery();
        
        switch($type) {
            case "all":
                $query->where('new_ally_id', $allyID)->orWhere('old_ally_id', $allyID);
                break;
            case "old":
                $query->where('old_ally_id', $allyID);
                break;
            case "new":
                $query->where('new_ally_id', $allyID);
                break;
            default:
                // FIXME: create error view
                return "Unknown type";
        }

        return $this->doAllyChangeReturn($query);
    }

    public function getPlayerAllyChanges($server, $world, $type, $playerID)
    {
        $allyChangesModel = new AllyChanges();
        $allyChangesModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.ally_changes');

        $query = $allyChangesModel->newQuery();

        switch($type) {
            case "all":
                $query->where('player_id', $playerID);
                break;
            default:
                // FIXME: create error view
                return "Unknown type";
        }
        
        return $this->doAllyChangeReturn($query);
    }
    
    private function doAllyChangeReturn($query) {
        return DataTables::eloquent($query)
            ->addColumn('player_name', function ($allyChange){
                if($allyChange->player_id == 0) return ucfirst(__('ui.player.barbarian'));
                if($allyChange->player == null) return ucfirst(__('ui.player.deleted'));
                return ($allyChange->player_id != 0)? BasicFunctions::decodeName($allyChange->player->name) : '-';
            })
            ->addColumn('old_ally_name', function ($allyChange){
                if($allyChange->old_ally_id == 0) return ucfirst(__('ui.ally.noAlly'));
                if($allyChange->oldAlly == null) return ucfirst(__('ui.ally.deleted'));
                return BasicFunctions::decodeName($allyChange->oldAlly->name);
            })
            ->addColumn('new_ally_name', function ($allyChange){
                if($allyChange->new_ally_id == 0) return ucfirst(__('ui.ally.noAlly'));
                if($allyChange->newAlly == null) return ucfirst(__('ui.ally.deleted'));
                return BasicFunctions::decodeName($allyChange->newAlly->name);
            })
            ->toJson();
    }

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
                // FIXME: create error view
                return "Unknown type";
        }

        return $this->doConquerReturn($query);
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
                // FIXME: create error view
                return "Unknown type";
        }
        
        return $this->doConquerReturn($query);
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
                // FIXME: create error view
                return "Unknown type";
        }
        
        return $this->doConquerReturn($query);
    }
    
    private function doConquerReturn($query) {
        return DataTables::eloquent($query)
            ->editColumn('timestamp', function ($conquer){
                return Carbon::createFromTimestamp($conquer->timestamp);
            })
            ->addColumn('village_name', function ($conquer){
                if($conquer->village == null) return ucfirst (__("ui.player.deleted"));
                return BasicFunctions::decodeName($conquer->village->name);
            })
            ->addColumn('old_owner_name', function ($conquer){
                if($conquer->old_owner == 0) return ucfirst(__('ui.player.barbarian'));
                if($conquer->oldPlayer == null) return ucfirst(__('ui.player.deleted'));
                return BasicFunctions::decodeName($conquer->oldPlayer->nameWithAlly());
            })
            ->addColumn('new_owner_name', function ($conquer){
                if($conquer->new_owner == 0) return ucfirst(__('ui.player.barbarian'));
                if($conquer->newPlayer == null) return ucfirst(__('ui.player.deleted'));
                return BasicFunctions::decodeName($conquer->newPlayer->nameWithAlly());
            })
            ->addColumn('old_owner_exists', function ($conquer){
                return $conquer->oldPlayer != null;
            })
            ->addColumn('new_owner_exists', function ($conquer){
                return $conquer->newPlayer != null;
            })
            ->toJson();
    }

    public static function getWorld($server, $world){
        $serverData = Server::getServerByCode($server);
        return new WorldResource(World::where([['name', '=', $world],['server_id', '=', $serverData->id]])->first());
    }

    public static function getVillageByCoord($server, $world, $x, $y){
        $villageModel = new Village();
        $villageModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.village_latest');

        return new VillageResource($villageModel->where(['x' => $x, 'y' => $y])->first());
    }

    public static function getPlayerByName($server, $world, $name){
        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_latest');

        return new PlayerResource($playerModel->where('name', urlencode($name))->first());
    }

    public static function getAllyByName($server, $world, $name){
        $allyModel = new Ally();
        $allyModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.ally_latest');

        return new AllyResource($allyModel->where('name', urlencode($name))->orWhere('tag', urlencode($name))->first());
    }

    public static function getSearchPlayerByName($server, $world){
        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_latest');
        return APIController::select2return($playerModel, array('name'), function($rawData) {
            return array(
                'id' => $rawData->playerID,
                'text' => BasicFunctions::decodeName($rawData->name),
            );
        });
    }

    public static function getSearchAllyByName($server, $world){
        $allyModel = new Ally();
        $allyModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.ally_latest');
        return APIController::select2return($allyModel, array('name', 'tag'), function($rawData) {
            return array(
                'id' => $rawData->allyID,
                'text' => BasicFunctions::decodeName($rawData->name) . ' [' . BasicFunctions::decodeName($rawData->tag) . ']',
            );
        });
    }
    
    private static function select2return(\Illuminate\Database\Eloquent\Model $model, $searchIn, callable $extractOne) {
        $getArray = \Illuminate\Support\Facades\Input::get();
        $perPage = 50;
        $search = (isset($getArray['search']))?('%'.BasicFunctions::likeSaveEscape(urlencode($getArray['search'])).'%'):('%');
        $page = (isset($getArray['page']))?($getArray['page']-1):(0);
        
        foreach($searchIn as $row) {
            $model = $model->orWhere($row, 'like', $search);
        }
        
        $dataAll = $model->offset($perPage*$page)->limit($perPage+1)->get();
        $converted = array('results' => array(), 'pagination' => array('more' => false));
        $i = 0;
        foreach($dataAll as $data) {
            if($i < $perPage) {
                $converted['results'][] = $extractOne($data);
            } else {
                $converted['pagination']['more'] = true;
            }
            $i++;
        }
        return response()->json($converted);
    }

    public function getPlayersHistory($server, $world, $day)
    {
        BasicFunctions::local();
        $days = Carbon::now()->diffInDays(Carbon::createFromFormat('Y-m-d', $day));
        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_latest');
        $datas = $playerModel->newQuery();

        return DataTables::eloquent($datas)
            ->editColumn('rank', function ($player) use($days){
                $playerOld = $player->playerHistory($days);

                if($playerOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($player, $playerOld, 'rank', true);
            })
            ->editColumn('name', function ($player){
                return BasicFunctions::decodeName($player->name);
            })
            ->addColumn('ally', function ($player){
                return ($player->ally_id != 0)? BasicFunctions::decodeName($player->allyLatest->tag) : '-';
            })
            ->editColumn('points', function ($player) use($days){
                $playerOld = $player->playerHistory($days);

                if($playerOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($player, $playerOld, 'points', false);
            })
            ->editColumn('village_count', function ($player) use($days){
                $playerOld = $player->playerHistory($days);

                if($playerOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($player, $playerOld, 'village_count', false);
            })
            ->addColumn('village_points', function ($player) use($days){
                $playerOld = $player->playerHistory($days);

                if($playerOld == null){
                    return __('ui.old.nodata');
                }
                $new = ($player->points == 0 || $player->village_count == 0)? 0 : ($player->points/$player->village_count);
                $old = ($playerOld->points == 0 || $playerOld->village_count == 0)? 0 : ($playerOld->points/$playerOld->village_count);
                return BasicFunctions::historyCalc($new, $old, 'village_count', false);
            })
            ->editColumn('gesBash', function ($player) use($days){
                $playerOld = $player->playerHistory($days);

                if($playerOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($player, $playerOld, 'gesBash', false);
            })
            ->editColumn('offBash', function ($player) use($days){
                $playerOld = $player->playerHistory($days);

                if($playerOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($player, $playerOld, 'offBash', false);
            })
            ->editColumn('defBash', function ($player) use($days){
                $playerOld = $player->playerHistory($days);

                if($playerOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($player, $playerOld, 'defBash', false);
            })
            ->addColumn('utBash', function ($player) use($days){
                $playerOld = $player->playerHistory($days);

                if($playerOld == null){
                    return __('ui.old.nodata');
                }
                $new = $player->gesBash - $player->offBash - $player->defBash;
                $old = $playerOld->gesBash - $playerOld->offBash - $playerOld->defBash;
                return BasicFunctions::historyCalc($new, $old, 'utBash', false);
            })
            ->rawColumns(['rank','points','village_count','village_points','gesBash','offBash','defBash','utBash'])
            ->toJson();
    }

    public function getAllysHistory($server, $world, $day)
    {
        BasicFunctions::local();
        $days = Carbon::now()->diffInDays(Carbon::createFromFormat('Y-m-d', $day));
        $allyModel = new Ally();
        $allyModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.ally_latest');

        $datas = $allyModel->newQuery();

        return DataTables::eloquent($datas)
            ->editColumn('rank', function ($ally) use($days){
                $allyOld = $ally->allyHistory($days);

                if($allyOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($ally, $allyOld, 'rank', true);
            })
            ->editColumn('name', function ($ally){
                return BasicFunctions::decodeName($ally->name);
            })
            ->editColumn('tag', function ($ally){
                return BasicFunctions::decodeName($ally->tag);
            })
            ->editColumn('points', function ($ally) use($days){
                $allyOld = $ally->allyHistory($days);

                if($allyOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($ally, $allyOld, 'points', false);
            })
            ->editColumn('member_count', function ($ally) use($days){
                $allyOld = $ally->allyHistory($days);

                if($allyOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($ally, $allyOld, 'member_count', false);
            })
            ->editColumn('village_count', function ($ally) use($days){
                $allyOld = $ally->allyHistory($days);

                if($allyOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($ally, $allyOld, 'village_count', false);
            })
            ->addColumn('player_points', function ($ally) use($days){
                $allyOld = $ally->allyHistory($days);

                $new = ($ally->points == 0 || $ally->member_count == 0)? 0 : ($ally->points/$ally->member_count);
                $old = ($allyOld->points == 0 || $allyOld->member_count == 0)? 0 : ($allyOld->points/$allyOld->member_count);

                if($allyOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::historyCalc($new, $old, 'player_points', false);
            })
            ->editColumn('gesBash', function ($ally) use($days){
                $allyOld = $ally->allyHistory($days);

                if($allyOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($ally, $allyOld, 'gesBash', false);
            })
            ->editColumn('offBash', function ($ally) use($days){
                $allyOld = $ally->allyHistory($days);

                if($allyOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($ally, $allyOld, 'offBash', false);
            })
            ->editColumn('defBash', function ($ally) use($days){
                $allyOld = $ally->allyHistory($days);

                if($allyOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($ally, $allyOld, 'defBash', false);
            })
            ->rawColumns(['rank','points','member_count','village_count','player_points','gesBash','offBash','defBash'])
            ->toJson();
    }

}
