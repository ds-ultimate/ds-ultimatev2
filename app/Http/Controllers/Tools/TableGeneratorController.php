<?php
/**
 * Created by IntelliJ IDEA.
 * User: crams
 * Date: 10.08.2019
 * Time: 20:38
 */

namespace App\Http\Controllers\Tools;


use App\Ally;
use App\Player;
use App\Util\BasicFunctions;
use App\Village;
use App\World;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class TableGeneratorController extends BaseController
{
    public function index($server, $world){
        BasicFunctions::local();
        World::existWorld($server, $world);

        $worldData = World::getWorld($server, $world);

        return view('tools.tableGenerator', compact('worldData', 'server'));
    }

    public function data(Request $request){
        $function = $request->get('type');
        $output = $this->$function($request);
        return \Response::json($output);
    }

    public function playerByAlly(Request $request){
        $world = World::find($request->get('world'));
        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($world->server->code, $world->name).'.player_latest');
        $players = $playerModel->where('ally_id', $request->get('selectType'))->orderBy($request->get('sorting'), ($request->get('sorting') == 'points')?'desc':'asc')->get();
        $start = "[quote][table]\n[**]".(($request->get('number'))? 'Nr.[||]':'') ."Spieler". (($request->get('points'))? '[||]Punkte':'') . str_repeat('[||]', $request->get('columns')) . "[/**]\n";
        $end = "[/table]\n[i][b]Stand[/b]: ". Carbon::now()->format('d.m.Y H:i:s') ."[/i] || Generiert mit [url=https://ds-ultimate.de]DS-Ultimate[/url][/quote]";
        $output = $start;
        $number = 1;
        $outputArray = array();

        foreach ($players as $player){
            $count = substr_count($output, '[');
            if ($count > 900){
                $output .= $end;
                $outputArray[] .= $output;
                $output = $start;
            }

            $output .= '[*]'. (($request->get('number'))? $number++.'.[|]':'') .'[player]'. BasicFunctions::decodeName($player->name) .'[/player]'. (($request->get('points'))? '[|]'. BasicFunctions::numberConv($player->points):''). str_repeat('[|]', $request->get('columns')) . "\n";
        }

        $output .= $end;

        $outputArray[] .= $output;

        return $outputArray;
    }

    public function villageByPlayer(Request $request){
        $world = World::find($request->get('world'));
        $villageModel = new Village();
        $villageModel->setTable(BasicFunctions::getDatabaseName($world->server->code, $world->name).'.village_latest');
        $villages = $villageModel->where('owner', $request->get('selectType'))->orderBy($request->get('sorting'), ($request->get('sorting') == 'points')?'desc':'asc')->get();
        $start = "[quote][table]\n[**]".(($request->get('number'))? 'Nr.[||]':'') ."Dorf". (($request->get('points'))? '[||]Punkte':'') . str_repeat('[||]', $request->get('columns')) . "[/**]\n";
        $end = "[/table]\n[i][b]Stand[/b]: ". Carbon::now()->format('d.m.Y H:i:s') ."[/i] || Generiert mit [url=https://ds-ultimate.de]DS-Ultimate[/url][/quote]";
        $output = $start;
        $number = 1;
        $outputArray = array();

        foreach ($villages as $village){
            $count = substr_count($output, '[');
            if ($count > 900){
                $output .= $end;
                $outputArray[] .= $output;
                $output = $start;
            }

            $output .= '[*]'. (($request->get('number'))? $number++.'.[|]':'') .'[coord]'. $village->coordinates() .'[/coord]'. (($request->get('points'))? '[|]'. BasicFunctions::numberConv($village->points):''). str_repeat('[|]', $request->get('columns')) . "\n";
        }

        $output .= $end;

        $outputArray[] .= $output;

        return $outputArray;
    }

    public function villageByAlly(Request $request){
        $world = World::find($request->get('world'));
        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($world->server->code, $world->name).'.player_latest');
        $players = $playerModel->where('ally_id', $request->get('selectType'))->get();
        $start = "[quote][table]\n[**]".(($request->get('number'))? 'Nr.[||]':'') ."Dorf". (($request->get('points'))? '[||]Punkte':'') . str_repeat('[||]', $request->get('columns')) . "[/**]\n";
        $end = "[/table]\n[i][b]Stand[/b]: ". Carbon::now()->format('d.m.Y H:i:s') ."[/i] || Generiert mit [url=https://ds-ultimate.de]DS-Ultimate[/url][/quote]";
        $output = $start;
        $number = 1;
        $outputArray = array();

        foreach ($players as $player){
            $villageModel = new Village();
            $villageModel->setTable(BasicFunctions::getDatabaseName($world->server->code, $world->name).'.village_latest');
            $villages = $villageModel->where('owner', $player->playerID)->orderBy('points', 'desc')->get();

            foreach ($villages as $village){
                $count = substr_count($output, '[');
                if ($count > 900){
                    $output .= $end;
                    $outputArray[] .= $output;
                    $output = $start;
                }

                $output .= '[*]'. (($request->get('number'))? $number++.'.[|]':'') .'[coord]'. $village->coordinates() .'[/coord]'. (($request->get('points'))? '[|]'. BasicFunctions::numberConv($village->points):''). str_repeat('[|]', $request->get('columns')) . "\n";

            }
        }

        $output .= $end;

        $outputArray[] .= $output;

        return $outputArray;
    }

}
