<?php
/**
 * Created by IntelliJ IDEA.
 * User: crams
 * Date: 10.08.2019
 * Time: 20:38
 */

namespace App\Http\Controllers\Tools;


use App\Tool\AttackPlanner\AttackList;
use App\Util\BasicFunctions;
use App\World;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AttackPlannerController extends BaseController
{
    public function index($server, $world){
        BasicFunctions::local();
        World::existWorld($server, $world);

        $worldData = World::getWorld($server, $world);

        $list = new AttackList();
        $list->world_id = $worldData->id;
        if (\Auth::user()){
            $list->user_id = \Auth::user()->id;
        }
        $list->edit_key = Str::random(40);
        $list->show_key = Str::random(40);
        $list->save();

        return redirect()->route('attackPlannerMode', [$server, $world, $list->id, 'edit', $list->edit_key]);

    }

    public function mode($server, $world, AttackList $attackList, $mode, $key){
        $worldData = World::getWorld($server, $world);
        $listWorld = $attackList->world;

        switch ($mode){
            case 'edit':
                if ($attackList->edit_key == $key){
                    if ($listWorld->is($worldData)){
                        return $this->edit($attackList);
                    }else{
                        return redirect()->route('attackPlannerMode',[$listWorld->server->code, $listWorld->name, $attackList->id, $mode, $key]);
                    }
                }
                return redirect()->route('index');
            case 'show':
                if ($attackList->show_key == $key){
                    if ($listWorld->is($worldData)){
                        return $this->show($attackList);
                    }else{
                        return redirect()->route('attackPlannerMode',[$listWorld->server->code, $listWorld->name, $attackList->id, $mode, $key]);
                    }
                }
                return redirect()->route('index');
            case 'exportWB':
                if ($attackList->show_key == $key){
                    if ($listWorld->is($worldData)){
                        return $this->exportWB($attackList);
                    }else{
                        return redirect()->route('attackPlannerMode',[$listWorld->server->code, $listWorld->name, $attackList->id, $mode, $key]);
                    }
                }
                return redirect()->route('index');
            default:
                return redirect()->route('index');
        }
    }

    public function edit(AttackList $attackList){
        BasicFunctions::local();
        $worldData = $attackList->world;

        $unitConfig = simplexml_load_string($worldData->units);
        $config = simplexml_load_string($worldData->config);
        $mode = 'edit';
        $now = Carbon::createFromTimestamp(time());

        return view('tools.attackPlanner', compact('worldData', 'unitConfig', 'config', 'attackList', 'mode', 'now'));
    }

    public function show(AttackList $attackList){
        BasicFunctions::local();
        $worldData = $attackList->world;

        $unitConfig = simplexml_load_string($worldData->units);
        $config = simplexml_load_string($worldData->config);
        $mode = 'show';
        $now = Carbon::createFromTimestamp(time());

        return view('tools.attackPlanner', compact('worldData', 'unitConfig', 'config', 'attackList', 'mode', 'now'));
    }

    public function exportWB(AttackList $attackList){
        $items = $attackList->items();
        $export = '';
        foreach ($items as $item){
            $export .= $item->start_village_id.'&'.$item->target_village_id.'&'.$item->slowest_unit;
        }
    }

}
