<?php
/**
 * Created by IntelliJ IDEA.
 * User: crams
 * Date: 10.08.2019
 * Time: 20:38
 */

namespace App\Http\Controllers\Tools;


use App\Http\Requests\ImportAttackPlannerItemRequest;
use App\Tool\AttackPlanner\AttackList;
use App\Tool\AttackPlanner\AttackListItem;
use App\Util\BasicFunctions;
use App\Village;
use App\World;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
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

        return redirect()->route('attackPlannerMode', [$list->id, 'edit', $list->edit_key]);

    }

    public function mode(AttackList $attackList, $mode, $key){
        switch ($mode){
            case 'edit':
                abort_unless($attackList->edit_key == $key, 403);
                return $this->edit($attackList);
            case 'show':
                abort_unless($attackList->show_key == $key, 403);
                return $this->show($attackList);
            case 'exportWB':
                abort_unless($attackList->show_key == $key || $attackList->edit_key == $key, 403);
                return $this->exportWB($attackList);
            case 'destroyOutdated':
                abort_unless($attackList->edit_key == $key, 403);
                return $this->destroyOutdated($attackList);
            default:
                abort(404);
        }
    }

    public function edit(AttackList $attackList){
        BasicFunctions::local();
        $worldData = $attackList->world;
        $server = $worldData->server->code;

        $unitConfig = simplexml_load_string($worldData->units);
        $config = simplexml_load_string($worldData->config);
        $mode = 'edit';
        $now = Carbon::createFromTimestamp(time());

        return view('tools.attackPlanner', compact('worldData', 'unitConfig', 'config', 'attackList', 'mode', 'now', 'server'));
    }

    public function show(AttackList $attackList){
        BasicFunctions::local();
        $worldData = $attackList->world;
        $server = $worldData->server->code;

        $unitConfig = simplexml_load_string($worldData->units);
        $config = simplexml_load_string($worldData->config);
        $mode = 'show';
        $now = Carbon::createFromTimestamp(time());

        return view('tools.attackPlanner', compact('worldData', 'unitConfig', 'config', 'attackList', 'mode', 'now', 'server'));
    }

    public function exportWB(AttackList $attackList){
        /** @var  AttackListItem */
        $items = $attackList->items;
        foreach ($items as $item){
            if (isset($export)){
                $export .= $item->start_village_id."&".$item->target_village_id."&".$item->unitIDToName()."&".(strtotime($item->arrival_time)*1000)."&".$item->type."&false&true&spear=MA==/sword=MA==/axe=MA==/spy=MA==/light=MA==/heavy=MA==/ram=MA==/catapult=MA==/knight=MA==/snob=MA==/militia=MA== \r\n";
            }else{
                $export = $item->start_village_id."&".$item->target_village_id."&".$item->unitIDToName()."&".(strtotime($item->arrival_time)*1000)."&".$item->type."&false&true&spear=MA==/sword=MA==/axe=MA==/spy=MA==/light=MA==/heavy=MA==/ram=MA==/catapult=MA==/knight=MA==/snob=MA==/militia=MA== \r\n";
            }
        }
        return $export;
    }

    public function importWB(ImportAttackPlannerItemRequest $request, AttackList $attackList){
        abort_unless($attackList->edit_key == $request->get('key'), 403);
        $world = $attackList->world;
        $unitConfig = simplexml_load_string($world->units);
        $imports = explode(PHP_EOL, $request->import);
        foreach ($imports as $import){
            if ($import != '') {

                $list = explode('&', $import);
                $villageModel = new Village();
                $villageModel->setTable(BasicFunctions::getDatabaseName($world->server->code, $world->name) . '.village_latest');
                $start = $villageModel->find($list[0]);
                $target = $villageModel->find($list[1]);

                if ($start != null && $target != null) {
                    $dist = sqrt(pow($start->x - $target->x, 2) + pow($start->y - $target->y, 2));
                    $arrival = (int)$list[3];
                    $unit = $list[2];
                    $time = $arrival-$unitConfig->$unit->speed * 60 * $dist*1000;
                    $send = date( 'Y-m-d H:i:s' , $time/1000);
//                    //TODO:add Units to AttackList
//                    $units = explode('/', $list[7]);
//                    foreach ($units as $unit){
//                        $unitSplit = explode('=', $unit, 2);
//                        var_dump($unitSplit);
//                        $unitArray[] = [$unitSplit[0] => $unitSplit[1]];
//                    }
//
//                    $importArray[$i] = array_merge($list, $unitArray);
                    self::newItem($attackList->id, $list[0], $list[1], AttackListItem::unitNameToID($list[2]), $send, date('Y-m-d H:i:s' , $arrival/1000), $list[4]);
                }
            }
        }

    }

    public function destroyOutdated(AttackList $attackList){
        $items = $attackList->items->where('send_time', '<', Carbon::createFromTimestamp(time()));
        foreach ($items as $item){
            $item->delete();
        }
        return ['success' => true, 'message' => 'destroy !!'];
    }

    public static function newItem($attack_list_id, $start_village_id, $target_village_id, $slowest_unit, $send_time, $arrival_time, $type){
        $item = new AttackListItem();
        $item->attack_list_id = $attack_list_id;
        $item->start_village_id = $start_village_id;
        $item->target_village_id = $target_village_id;
        $item->slowest_unit = $slowest_unit;
        $item->send_time = $send_time;
        $item->arrival_time = $arrival_time;
        $item->type = $type;
        $item->save();
    }

}
