<?php
/**
 * Created by IntelliJ IDEA.
 * User: crams
 * Date: 10.08.2019
 * Time: 20:38
 */

namespace App\Http\Controllers\Tools;

use App\Player;
use App\Server;
use App\Village;
use App\World;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class TableGeneratorController extends BaseController
{
    public function index($server, $world){
        $server = Server::getAndCheckServerByCode($server);
        $worldData = World::getAndCheckWorld($server, $world);
        return view('tools.tableGenerator', compact('worldData', 'server'));
    }

    private static $alowedFunctions = [
        "playerByAlly",
        "villageByPlayer",
        "villageByAlly",
        "villageAndPlayerByAlly",
    ];

    public function data(Request $request){
        $function = $request->get('type');
        if(!in_array($function, static::$alowedFunctions)) {
            return \Response::json("Invalid type");
        }
        
        $validated = $request->validate([
            "world" => "required|numeric|integer",
            "selectType" => "required|numeric|integer",
            "sorting" => "required|string|in:name,points",
            "number" => "required|boolean:strict",
            "points" => "required|boolean:strict",
            "showVillageCount" => "required|boolean:strict",
            "showPointDiff" => "required|boolean:strict",
            "columns" => "required|numeric|integer",
        ]);
        
        $output = $this->$function($validated);
        return \Response::json($output);
    }

    public function playerByAlly(array $validated){
        $world = World::find($validated['world']);
        abort_if($world == null, 404, __("ui.errors.404.noWorld", ["world" => "" . $validated['world']]));

        $playerModel = new Player($world);
        $players = $playerModel->where('ally_id', $validated["selectType"])->orderBy($validated["sorting"], ($validated["sorting"] == 'points')?'desc':'asc')->get();

        $villageModel = new Village($world);
        $start = "[quote][table]\n[**]".
            (($validated['number'])? 'Nr.[||]':'').
            __('ui.table.player').
            (($validated['points'])? '[||]'.__('ui.table.points'):'').
            (($validated['showVillageCount'])? '[||]'.__('ui.table.villages'):'').
            (($validated['showPointDiff'])? '[||]&darr;120&darr;[||]&uarr;120&uarr;':'').
            str_repeat('[||]', (int) $validated['columns']).
            "[/**]\n";
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

            $output .= '[*]'.
                (($validated['number'])? $number++.'.[|]':'').
                '[player]'. BasicFunctions::decodeName($player->name) .'[/player]'.
                (($validated['points'])? '[|]'. BasicFunctions::numberConv($player->points):'').
                (($validated['showVillageCount'])? '[|]'. BasicFunctions::numberConv($villageModel->where('owner', $player->playerID)->count()):'').
                (($validated['showPointDiff'])? '[|]'.BasicFunctions::numberConv($player->points/1.2).'[|]'.BasicFunctions::numberConv($player->points*1.2):'').
                str_repeat('[|]', $validated['columns']) .
                "\n";
        }

        $output .= $end;

        $outputArray[] .= $output;

        return $outputArray;
    }

    public function villageByPlayer(array $validated){
        $world = World::find($validated['world']);
        $villageModel = new Village($world);
        $villages = $villageModel->where('owner', $validated['selectType'])->orderBy($validated['sorting'], ($validated['sorting'] == 'points')?'desc':'asc')->get();
        $start = "[quote][table]\n[**]".
            (($validated['number'])? 'Nr.[||]':'') .
            __('ui.table.village').
            (($validated['points'])? '[||]'.__('ui.table.points'):'').
            str_repeat('[||]', $validated['columns']) . "[/**]\n";
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

            $output .= '[*]'.
                (($validated['number'])? $number++.'.[|]':'') .'[coord]'. $village->coordinates() .'[/coord]'.
                (($validated['points'])? '[|]'. BasicFunctions::numberConv($village->points):'').
                str_repeat('[|]', $validated['columns'])."\n";
        }

        $output .= $end;

        $outputArray[] .= $output;

        return $outputArray;
    }

    public function villageByAlly(array $validated){
        $world = World::find($validated['world']);
        $playerModel = new Player($world);
        $players = $playerModel->where('ally_id', $validated['selectType'])->get();
        $start = "[quote][table]\n[**]".
            (($validated['number'])? 'Nr.[||]':'').
            __('ui.table.village').
            (($validated['points'])? '[||]'.__('ui.table.points'):'').
            str_repeat('[||]', $validated['columns']).
            "[/**]\n";
        $end = "[/table]\n[i][b]Stand[/b]: ". Carbon::now()->format('d.m.Y H:i:s') ."[/i] || Generiert mit [url=https://ds-ultimate.de]DS-Ultimate[/url][/quote]";
        $output = $start;
        $number = 1;
        $outputArray = array();

        foreach ($players as $player){
            $villageModel = new Village($world);
            $villages = $villageModel->where('owner', $player->playerID)->orderBy('points', 'desc')->get();

            foreach ($villages as $village){
                $count = substr_count($output, '[');
                if ($count > 900){
                    $output .= $end;
                    $outputArray[] .= $output;
                    $output = $start;
                }

                $output .= '[*]'.
                        (($validated['number'])? $number++.'.[|]':'') .'[coord]'. $village->coordinates() .'[/coord]'.
                        (($validated['points'])? '[|]'. BasicFunctions::numberConv($village->points):'').
                        str_repeat('[|]', $validated['columns']) . "\n";

            }
        }

        $output .= $end;

        $outputArray[] .= $output;

        return $outputArray;
    }

    public function villageAndPlayerByAlly(array $validated){
        $world = World::find($validated['world']);
        $playerModel = new Player($world);
        $players = $playerModel->where('ally_id', $validated['selectType'])->orderBy($validated['sorting'], ($validated['sorting'] == 'points')?'desc':'asc')->get();
        $start = "[quote][table]\n[**]".
            (($validated['number'])? 'Nr.[||]':'').
            __('ui.table.player').'[||]'.
            __('ui.table.village').
            (($validated['points'])? '[||]'.__('ui.table.points'):'').
            str_repeat('[||]', $validated['columns']).
            "[/**]\n";
        $end = "[/table]\n[i][b]Stand[/b]: ". Carbon::now()->format('d.m.Y H:i:s') ."[/i] || Generiert mit [url=https://ds-ultimate.de]DS-Ultimate[/url][/quote]";
        $output = $start;
        $number = 1;
        $outputArray = array();

        foreach ($players as $player){
            $villageModel = new Village($world);
            $villages = $villageModel->where('owner', $player->playerID)->orderBy('points', 'desc')->get();

            foreach ($villages as $village){
                $count = substr_count($output, '[');
                if ($count > 900){
                    $output .= $end;
                    $outputArray[] .= $output;
                    $output = $start;
                }

                $output .= '[*]'.
                    (($validated['number'])? $number++.'.[|]':'').
                    '[player]'. BasicFunctions::decodeName($player->name) .'[/player][|]'.
                    '[coord]'. $village->coordinates() .'[/coord]'.
                    (($validated['points'])? '[|]'. BasicFunctions::numberConv($village->points):'').
                    str_repeat('[|]', $validated['columns']) . "\n";
            }
        }

        $output .= $end;

        $outputArray[] .= $output;

        return $outputArray;
    }
}
