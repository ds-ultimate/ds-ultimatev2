<?php

namespace App\Http\Controllers;

use App\Ally;
use App\World;
use App\Player;
use App\Server;
use App\Village;
use App\Conquer;
use App\AllyChanges;
use App\Util\Chart;
use App\Util\ImageChart;
use App\Util\BasicFunctions;
use Auth;
use Carbon\Carbon;
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
                abort(404, "Unknown type");
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
                abort(404, "Unknown type");
        }
        
        return $this->doAllyChangeReturn($query);
    }
    
    private function doAllyChangeReturn($query) {
        return DataTables::eloquent($query)
            ->editColumn('created_at', function ($allyChange){
                return $allyChange->created_at->format('Y-m-d H:i:s');
            })
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

    public static function getActiveWorldByServer($server){
        $server = Server::getServerByCode($server);
        $worlds = World::where('active', '!=', null)->where('server_id', $server->id)->get();
        $array = [];
        foreach ($worlds as $world){
            $array[] = ['id' => $world->name, 'text' => $world->displayName()];
        }
        return $array;
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
        $getArray = \Illuminate\Support\Facades\Request::input();
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
                
                if($allyOld == null){
                    return __('ui.old.nodata');
                }

                $new = ($ally->points == 0 || $ally->member_count == 0)? 0 : ($ally->points/$ally->member_count);
                $old = ($allyOld->points == 0 || $allyOld->member_count == 0)? 0 : ($allyOld->points/$allyOld->member_count);

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

    public function getDsConnection()
    {

            BasicFunctions::local();
            $datas = Auth::user()->dsConnection();

            return DataTables::eloquent($datas)
                ->addColumn('server', function ($connection) {
                    return "<span><span class=\"flag-icon flag-icon-" . $connection->world->server->flag . "\"></span>" . ucwords($connection->world->server->code) . "</span>";
                })
                ->addColumn('world', function ($connection) {
                    return $connection->world->displayName();
                })
                ->addColumn('player', function ($connection) {
                    $player = Player::player($connection->world->server->code, $connection->world->name, $connection->player_id);
                    return BasicFunctions::decodeName(($player != null)?$player->name:'<b>'.__('ui.player.deleted').'</b>');
                })
                ->editColumn('key', function ($connection) {
                    if ($connection->verified == 0) {
                        return'<div class="input-group mb-2">
                                            <input id="key_'.$connection->id.'" type="text" class="form-control" value="'.$connection->key.'" aria-label="Recipient\'s username" aria-describedby="basic-addon2">
                                            <div class="input-group-append">
                                                <span class="input-group-text" style="cursor:pointer" id="basic-addon2" onclick="copy(\'key_'.$connection->id.'\')"><i class="far fa-copy"></i></span>
                                            </div>
                                        </div>';
                    }
                    return'<b>'.__('ui.personalSettings.connectionVerified').'</b>';
                })
                ->addColumn('action', function ($connection) {
                    $button = '';
                    if ($connection->verified == 0) {
                        $button = '<a class="btn btn-success" style="cursor:pointer" onclick="checkConnection('.$connection->id.')"><i class="fas fa-sync text-white"></i></a> ';
                    }
                    $button .= '<a class="btn btn-danger" style="cursor:pointer" onclick="destroyConnection('.$connection->id.', \''.$connection->key.'\')"><i class="fas fa-times text-white"></i></a>';

                    return $button;
                })
                ->rawColumns(['server', 'action','key', 'player'])
                ->toJson();
    }

    public function signature($server, $world, $type, $id){
        // Content type
        $worldData = \App\World::getWorld($server, $world);
        $playerData = \App\Player::player($server, $world, $id);
        if ($playerData != false && $type == 'player') {
            $image = imagecreatefrompng('images/default/signature/bg.png');
            if ($image === false) die("Error");
            if (strpos($worldData->name, 'p') !== false || strpos($worldData->name, 'c') !== false) {
                imagettftext($image, 9, 90, 15, 70 - 8 - 4, imagecolorallocate($image, 000, 000, 000), 'fonts/arial_b.ttf', $worldData->displayName());
            } else {
                imagettftext($image, 10, 90, 18, 70 - 8 - 5, imagecolorallocate($image, 000, 000, 000), 'fonts/arial_b.ttf', $worldData->displayName());
            }
            $flag = imagecreatetruecolor(16, 12);
            imagecopyresampled($flag, imagecreatefrompng('images/default/signature/' . $server . '.png'), 0, 0, 0, 0, 16, 12, 640, 480);
            imagecopyresampled($image, $flag, 27, 8 - 5, 0, 0, 16, 12, 16, 12);
            imagettftext($image, 10, 0, 56, 20 - 5, imagecolorallocate($image, 49, 32, 6), 'fonts/arial_b.ttf', \App\Util\BasicFunctions::decodeName($playerData->name));
            imagettftext($image, 10, 0, 300, 20 - 5, imagecolorallocate($image, 49, 32, 6), 'fonts/arial_i.ttf', 'by DS-Ultimate');
            imagettftext($image, 10, 0, 56, 38 - 5, imagecolorallocate($image, 49, 32, 6), 'fonts/arial.ttf', \App\Util\BasicFunctions::thousandsCurrencyFormat($playerData->points, true) . ' [' . \App\Util\BasicFunctions::numberConv($playerData->rank) . '.]');
            imagettftext($image, 10, 0, 56, 59 - 5, imagecolorallocate($image, 49, 32, 6), 'fonts/arial.ttf', ($playerData->village_count != 0) ? \App\Util\BasicFunctions::thousandsCurrencyFormat($playerData->village_count, true) . '[' . \App\Util\BasicFunctions::numberConv($playerData->points / $playerData->village_count) . '&Oslash;]' : \App\Util\BasicFunctions::thousandsCurrencyFormat($playerData->village_count, true) . ' [- &Oslash;]');
            imagettftext($image, 10, 0, 56 + 4 + 18 + 3 + 100, 38 - 5, imagecolorallocate($image, 49, 32, 6), 'fonts/arial.ttf', \App\Util\BasicFunctions::thousandsCurrencyFormat($playerData->offBash, true) . ' [' . (isset($playerData->offBashRank) ? $playerData->offBashRank . '.]' : '--]'));
            imagettftext($image, 10, 0, 56 + 4 + 18 + 3 + 100, 59 - 5, imagecolorallocate($image, 49, 32, 6), 'fonts/arial.ttf', \App\Util\BasicFunctions::thousandsCurrencyFormat($playerData->defBash, true) . ' [' . (isset($playerData->defBashRank) ? $playerData->defBashRank . '.]' : '--]'));
            imagettftext($image, 10, 0, 56 + 4 + 18 + 3 + 100 + 4 + 18 + 3 + 100, 38 - 5, imagecolorallocate($image, 49, 32, 6), 'fonts/arial.ttf', \App\Util\BasicFunctions::thousandsCurrencyFormat($playerData->gesBash, true) . ' [' . (isset($playerData->gesBashRank) ? $playerData->gesBashRank . '.]' : '--]'));
            imagettftext($image, 10, 0, 56 + 4 + 18 + 3 + 100 + 4 + 18 + 3 + 100, 59 - 5, imagecolorallocate($image, 49, 32, 6), 'fonts/arial_b.ttf', ((isset($playerData->allyLatest))?\App\Util\BasicFunctions::decodeName($playerData->allyLatest->tag ). ' [' . $playerData->allyLatest->rank . '.]':'--'));

            //copy from PictureController getPlayerSizedPic
            $rawStatData = Player::playerDataChart($server, $world, $id, 17);
            $statData = array();
            foreach ($rawStatData as $rawData) {
                $statData[$rawData->get('timestamp')] = $rawData->get('points');
            }

            $name = \App\Util\BasicFunctions::decodeName($playerData->name);
            $playerString = __('chart.who.player') . ": $name";

            $chart = new ImageChart("fonts/NotoMono-Regular.ttf", [
                'width' => 124, //124
                'height' => 75, //75
            ], false);
            $chart->render($statData, $playerString, Chart::chartTitel('points'), Chart::displayInvers('points'), true);
            imagecopyresampled($image, $chart->getRawImage(), 402, 7 - 5, 32, 10, 89, 56, 89, 56);//src_x -> 30
        }else{
            $image = imagecreatefrompng('images/default/signature/bg_noData.png');
            if ($image === false) die("Error");
        }
        // Output
        ob_start();
        imagepng($image);
        $imagedata = ob_get_clean();
        imagedestroy($image);
        return response($imagedata, 200)
            ->header('Content-Type', 'image/png');
    }

}
