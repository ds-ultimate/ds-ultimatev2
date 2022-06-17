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
        $worldData = World::getAndCheckWorld($server, $world);

        $playerModel = new Player($worldData);
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
        $worldData = World::getAndCheckWorld($server, $world);

        $allyModel = new Ally($worldData);

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
        $worldData = World::getAndCheckWorld($server, $world);

        $playerModel = new Player($worldData);
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

    public function getAllyPlayerBashRanking($server, $world, $ally)
    {
        static::limitResults(200);
        $worldData = World::getAndCheckWorld($server, $world);

        $playerModel = new Player($worldData);
        $querry = $playerModel->newQuery();
        $querry->where('ally_id', $ally);

        return DataTables::eloquent($querry)
            ->addIndexColumn()
            ->editColumn('name', function ($player){
                return BasicFunctions::decodeName($player->name);
            })
            ->editColumn('gesBash', function ($player) use($worldData) {
                $playerOld = $player->playerHistory(1, $worldData);

                if($playerOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($player, $playerOld, 'gesBash', false);
            })
            ->editColumn('offBash', function ($player) use($worldData) {
                $playerOld = $player->playerHistory(1, $worldData);

                if($playerOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($player, $playerOld, 'offBash', false);
            })
            ->editColumn('defBash', function ($player) use($worldData) {
                $playerOld = $player->playerHistory(1, $worldData);

                if($playerOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($player, $playerOld, 'defBash', false);
            })
            ->editColumn('supBash', function ($player) use($worldData) {
                $playerOld = $player->playerHistory(1, $worldData);

                if($playerOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($player, $playerOld, 'supBash', false);
            })
            ->addColumn('allyKillsPercent', function ($player){
                return ($player->gesBash == 0 || $player->allyLatest->gesBash == 0)? 0 .'%': round(($player->gesBash/$player->allyLatest->gesBash)*100, 1).'%';
            })
            ->addColumn('playerPointPercent', function ($player){
                return ($player->points == 0 || $player->gesBash == 0) ? 0 .'%' : round(($player->gesBash/$player->points)*100, 1).'%';
            })
            ->rawColumns(['gesBash','offBash','defBash','supBash'])
            ->toJson();
    }

    public function getPlayerVillage($server, $world, $player)
    {
        static::limitResults(200);
        $worldData = World::getAndCheckWorld($server, $world);

        $villageModel = new Village($worldData);
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

    public function getPlayerHistory($server, $world, $player)
    {
        $worldData = World::getAndCheckWorld($server, $world);
        $tableNr = ((int) $player) % $worldData->hash_player;
        
        $playerModel = new Player($worldData, "player_$tableNr");
        $data = $playerModel->where('playerID', $player)->get();
        
        $newData = [];
        $dates = [];
        $lData = null;
        foreach($data as $d) {
            $date = $d->created_at->format("Y-m-d");
            if(! isset($dates[$date])) {
                $dates[$date] = 1;
                $d->last = $lData;
                $newData[] = $d;
                $lData = $d;
            }
        }

        return DataTables::of($newData)
            ->editColumn('name', function ($player){
                return BasicFunctions::decodeName($player->name);
            })
            ->editColumn('created_at', function ($player){
                return $player->created_at->format("Y-m-d");
            })
            ->addColumn('allyTag', function ($player){
                return ($player->ally_id != 0 && $player->allyLatest != null) ? BasicFunctions::decodeName($player->allyLatest->tag) : '-';
            })
            ->addColumn('rank', function ($player){
                return BasicFunctions::modelHistoryCalcPopupless($player, $player->last, "rank", true);
            })
            ->addColumn('points', function ($player){
                return BasicFunctions::modelHistoryCalcPopupless($player, $player->last, "points");
            })
            ->addColumn('village_count', function ($player){
                return BasicFunctions::modelHistoryCalcPopupless($player, $player->last, "village_count");
            })
            ->addColumn('offBash', function ($player){
                return BasicFunctions::modelHistoryCalcPopupless($player, $player->last, "offBash");
            })
            ->addColumn('defBash', function ($player){
                return BasicFunctions::modelHistoryCalcPopupless($player, $player->last, "defBash");
            })
            ->addColumn('gesBash', function ($player){
                return BasicFunctions::modelHistoryCalcPopupless($player, $player->last, "gesBash");
            })
            ->removeColumn("last", "offBashRank", "defBashRank", "supBash", "supBashRank", "gesBashRank", "updated_at")
            ->rawColumns(["rank", "points", "village_count", "offBash", "defBash", "gesBash"])
            ->toJson();
    }

    public function getAllyHistory($server, $world, $ally)
    {
        $worldData = World::getAndCheckWorld($server, $world);
        $tableNr = ((int) $ally) % $worldData->hash_ally;
        
        $allyModel = new Ally($worldData, "ally_$tableNr");
        $data = $allyModel->where('allyID', $ally)->get();
        
        $newData = [];
        $dates = [];
        $lData = null;
        foreach($data as $d) {
            $date = $d->created_at->format("Y-m-d");
            if(! isset($dates[$date])) {
                $dates[$date] = 1;
                $d->last = $lData;
                $newData[] = $d;
                $lData = $d;
            }
        }

        return DataTables::of($newData)
            ->editColumn('tag', function ($ally){
                return BasicFunctions::decodeName($ally->tag);
            })
            ->editColumn('created_at', function ($ally){
                return $ally->created_at->format("Y-m-d");
            })
            ->addColumn('rank', function ($ally){
                return BasicFunctions::modelHistoryCalcPopupless($ally, $ally->last, "rank", true);
            })
            ->addColumn('member_count', function ($ally){
                return BasicFunctions::modelHistoryCalcPopupless($ally, $ally->last, "member_count");
            })
            ->addColumn('points', function ($ally){
                return BasicFunctions::modelHistoryCalcPopupless($ally, $ally->last, "points");
            })
            ->addColumn('village_count', function ($ally){
                return BasicFunctions::modelHistoryCalcPopupless($ally, $ally->last, "village_count");
            })
            ->addColumn('offBash', function ($ally){
                return BasicFunctions::modelHistoryCalcPopupless($ally, $ally->last, "offBash");
            })
            ->addColumn('defBash', function ($ally){
                return BasicFunctions::modelHistoryCalcPopupless($ally, $ally->last, "defBash");
            })
            ->addColumn('gesBash', function ($ally){
                return BasicFunctions::modelHistoryCalcPopupless($ally, $ally->last, "gesBash");
            })
            ->removeColumn("last", "offBashRank", "defBashRank", "gesBashRank", "updated_at")
            ->rawColumns(["rank", "points", "member_count", "village_count", "offBash", "defBash", "gesBash"])
            ->toJson();
    }

    public function getPlayersHistory($server, $world, $day)
    {
        static::limitResults(110);
        $worldData = World::getAndCheckWorld($server, $world);

        BasicFunctions::local();
        $days = Carbon::now()->diffInDays(Carbon::createFromFormat('Y-m-d', $day));
        $playerModel = new Player($worldData);
        $datas = $playerModel->newQuery();

        return DataTables::eloquent($datas)
            ->editColumn('rank', function ($player) use($days, $worldData) {
                $playerOld = $player->playerHistory($days, $worldData);

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
            ->editColumn('points', function ($player) use($days, $worldData) {
                $playerOld = $player->playerHistory($days, $worldData);

                if($playerOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($player, $playerOld, 'points', false);
            })
            ->editColumn('village_count', function ($player) use($days, $worldData) {
                $playerOld = $player->playerHistory($days, $worldData);

                if($playerOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($player, $playerOld, 'village_count', false);
            })
            ->addColumn('village_points', function ($player) use($days, $worldData) {
                $playerOld = $player->playerHistory($days, $worldData);

                if($playerOld == null){
                    return __('ui.old.nodata');
                }
                $new = ($player->points == 0 || $player->village_count == 0)? 0 : ($player->points/$player->village_count);
                $old = ($playerOld->points == 0 || $playerOld->village_count == 0)? 0 : ($playerOld->points/$playerOld->village_count);
                return BasicFunctions::historyCalc($new, $old, 'village_count', false);
            })
            ->editColumn('gesBash', function ($player) use($days, $worldData) {
                $playerOld = $player->playerHistory($days, $worldData);

                if($playerOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($player, $playerOld, 'gesBash', false);
            })
            ->editColumn('offBash', function ($player) use($days, $worldData) {
                $playerOld = $player->playerHistory($days, $worldData);

                if($playerOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($player, $playerOld, 'offBash', false);
            })
            ->editColumn('defBash', function ($player) use($days, $worldData) {
                $playerOld = $player->playerHistory($days, $worldData);

                if($playerOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($player, $playerOld, 'defBash', false);
            })
            ->editColumn('supBash', function ($player) use($days, $worldData) {
                $playerOld = $player->playerHistory($days, $worldData);

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
        static::limitResults(110);
        $worldData = World::getAndCheckWorld($server, $world);

        BasicFunctions::local();
        $days = Carbon::now()->diffInDays(Carbon::createFromFormat('Y-m-d', $day));
        $allyModel = new Ally($worldData);
        $datas = $allyModel->newQuery();

        return DataTables::eloquent($datas)
            ->editColumn('rank', function ($ally) use($days, $worldData){
                $allyOld = $ally->allyHistory($days, $worldData);

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
            ->editColumn('points', function ($ally) use($days, $worldData){
                $allyOld = $ally->allyHistory($days, $worldData);

                if($allyOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($ally, $allyOld, 'points', false);
            })
            ->editColumn('member_count', function ($ally) use($days, $worldData){
                $allyOld = $ally->allyHistory($days, $worldData);

                if($allyOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($ally, $allyOld, 'member_count', false);
            })
            ->editColumn('village_count', function ($ally) use($days, $worldData){
                $allyOld = $ally->allyHistory($days, $worldData);

                if($allyOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($ally, $allyOld, 'village_count', false);
            })
            ->addColumn('player_points', function ($ally) use($days, $worldData){
                $allyOld = $ally->allyHistory($days, $worldData);

                if($allyOld == null){
                    return __('ui.old.nodata');
                }

                $new = ($ally->points == 0 || $ally->member_count == 0)? 0 : ($ally->points/$ally->member_count);
                $old = ($allyOld->points == 0 || $allyOld->member_count == 0)? 0 : ($allyOld->points/$allyOld->member_count);

                return BasicFunctions::historyCalc($new, $old, 'player_points', false);
            })
            ->editColumn('gesBash', function ($ally) use($days, $worldData){
                $allyOld = $ally->allyHistory($days, $worldData);

                if($allyOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($ally, $allyOld, 'gesBash', false);
            })
            ->editColumn('offBash', function ($ally) use($days, $worldData){
                $allyOld = $ally->allyHistory($days, $worldData);

                if($allyOld == null){
                    return __('ui.old.nodata');
                }
                return BasicFunctions::modelHistoryCalc($ally, $allyOld, 'offBash', false);
            })
            ->editColumn('defBash', function ($ally) use($days, $worldData){
                $allyOld = $ally->allyHistory($days, $worldData);

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
