<?php

namespace App\Http\Controllers\API;

use App\Ally;
use App\Player;
use App\Village;
use App\Http\Controllers\Controller;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class DatatablesController extends Controller
{
    public function getPlayers($server, $world)
    {
        static::limitResults(200);

        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_latest');

        $datas = $playerModel->newQuery();

        return DataTables::eloquent($datas)
            ->editColumn('name', function ($player){
                return BasicFunctions::decodeName($player->name);
            })
            ->addColumn('ally', function ($player){
                return ($player->ally_id != 0 && $player->allyLatest != null)? BasicFunctions::decodeName($player->allyLatest->tag) : '-';
            })
            ->addColumn('village_points', function ($player){
                return ($player->points == 0 || $player->village_count == 0)? 0 : ($player->points/$player->village_count);
            })
            ->toJson();
    }

    public function getAllys($server, $world)
    {
        static::limitResults(200);

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
        static::limitResults(200);

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
            ->toJson();
    }

    public function getPlayerVillage($server, $world, $player)
    {
        static::limitResults(200);

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

    public function getPlayersHistory($server, $world, $day)
    {
        static::limitResults(50);

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
                return ($player->ally_id != 0 && $player->allyLatest != null)? BasicFunctions::decodeName($player->allyLatest->tag) : '-';
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
            ->editColumn('supBash', function ($player) use($days){
                $playerOld = $player->playerHistory($days);

                if($playerOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($player, $playerOld, 'supBash', false);
            })
            ->rawColumns(['rank','points','village_count','village_points','gesBash','offBash','defBash','supBash'])
            ->toJson();
    }

    public function getAllysHistory($server, $world, $day)
    {
        static::limitResults(50);

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

    public static function limitResults($amount) {
        request()->validate([
            'length' => 'required|integer|min:1|max:'.$amount,
            'start' => 'required|integer'
        ]);
    }
}
