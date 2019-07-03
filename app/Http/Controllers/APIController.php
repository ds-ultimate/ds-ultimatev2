<?php

namespace App\Http\Controllers;

use App\Ally;
use App\Player;
use App\Util\BasicFunctions;
use Yajra\DataTables\Facades\DataTables;

class APIController extends Controller
{
    public function getPlayers($server, $world)
    {
        $playerModel = new Player();
        $replaceArray = array(
            '{server}' => $server,
            '{world}' => $world
        );

        $playerModel->setTable(str_replace(array_keys($replaceArray), array_values($replaceArray), env('DB_DATABASE_WORLD', 'c1welt_{server}{world}').'.player_latest'));

        $datas = $playerModel->newQuery();

        return DataTables::eloquent($datas)
            ->editColumn('name', function ($player){
                return BasicFunctions::outputName($player->name);
            })
            ->addColumn('ally', function ($player){
                return ($player->ally_id != 0)? BasicFunctions::outputName($player->allyLatest->tag) : '-';
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
        $replaceArray = array(
            '{server}' => $server,
            '{world}' => $world
        );

        $allyModel->setTable(str_replace(array_keys($replaceArray), array_values($replaceArray), env('DB_DATABASE_WORLD', 'c1welt_{server}{world}').'.ally_latest'));

        $datas = $allyModel->newQuery();

        return DataTables::eloquent($datas)
            ->editColumn('name', function ($ally){
                return BasicFunctions::outputName($ally->name);
            })
            ->editColumn('tag', function ($ally){
                return BasicFunctions::outputName($ally->tag);
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
        $replaceArray = array(
            '{server}' => $server,
            '{world}' => $world
        );

        $playerModel->setTable(str_replace(array_keys($replaceArray), array_values($replaceArray), env('DB_DATABASE_WORLD', 'c1welt_{server}{world}').'.player_latest'));

        $querry = $playerModel->newQuery();
        $querry->where('ally_id', $ally);

        return DataTables::eloquent($querry)
            ->editColumn('name', function ($player){
                return BasicFunctions::outputName($player->name);
            })
            ->addColumn('ally', function ($player){
                return ($player->ally_id != 0)? BasicFunctions::outputName($player->allyLatest->tag) : '-';
            })
            ->addColumn('village_points', function ($player){
                return ($player->points == 0 || $player->village_count == 0)? 0 : ($player->points/$player->village_count);
            })
            ->addColumn('utBash', function ($player){
                return $player->gesBash - $player->offBash - $player->defBash;
            })
            ->toJson();
    }

}
