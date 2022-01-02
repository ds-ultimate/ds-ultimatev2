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
use App\Util\Icon;
use App\Village;
use App\World;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class AttackPlannerController extends BaseController
{
    public function index($server, $world){
        BasicFunctions::local();
        World::existWorld($server, $world);

        $worldData = World::getWorld($server, $world);
        if($worldData->config == null || $worldData->units == null) {
            abort(404, __('tool.attackPlanner.notAvailable'));
        }

        if(\Auth::check()) {
            //only allow one attackPlanner without title per user per world
            $listModel = new AttackList();
            $uniqueList = $listModel->where('world_id', $worldData->id)->where('user_id', \Auth::user()->id)->whereNull('title')->first();
            if($uniqueList != null) {
                return redirect()->route('tools.attackPlannerMode', [$uniqueList->id, 'edit', $uniqueList->edit_key]);
            }
        }

        $list = new AttackList();
        $list->world_id = $worldData->id;
        if (\Auth::user()){
            $list->user_id = \Auth::user()->id;
        }
        $list->edit_key = Str::random(40);
        $list->show_key = Str::random(40);
        $list->save();

        return redirect()->route('tools.attackPlannerMode', [$list->id, 'edit', $list->edit_key]);
    }

    public function mode(AttackList $attackList, $mode, $key){
        $worldData = $attackList->world;
        if($worldData->config == null || $worldData->units == null) {
            abort(404, __('tool.attackPlanner.notAvailable'));
        }

        switch ($mode){
            case 'edit':
                abort_unless($attackList->edit_key == $key, 403);
                $attackList->touch();
                return $this->edit($attackList);
            case 'show':
                abort_unless($attackList->show_key == $key, 403);
                $attackList->touch();
                return $this->show($attackList);
            case 'exportAll':
                abort_unless($attackList->show_key == $key || $attackList->edit_key == $key, 403);
                $attackList->touch();
                return $this->exportAll($attackList);
            default:
                abort(404);
        }
    }

    public function modePost(AttackList $attackList, $mode, $key){
        $worldData = $attackList->world;
        if($worldData->config == null || $worldData->units == null) {
            abort(404, __('tool.attackPlanner.notAvailable'));
        }

        switch ($mode){
            case 'destroyOutdated':
                abort_unless($attackList->edit_key == $key, 403);
                $attackList->touch();
                return $this->destroyOutdated($attackList);
            case 'clear':
                abort_unless($attackList->edit_key == $key, 403);
                $attackList->touch();
                return $this->clear($attackList);
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
        $now = Carbon::now();

        $stats['total'] = $attackList->items()->count();
        $stats['start_village'] = $attackList->items()->get()->groupBy('start_village_id')->count();
        $stats['target_village'] = $attackList->items()->get()->groupBy('target_village_id')->count();

        foreach ($attackList->items()->get()->groupBy('type') as $type){
            $stats['type'][$type[0]->type]['id'] = $type[0]->type;
            $stats['type'][$type[0]->type]['count'] = $type->count();
        }

        foreach ($attackList->items()->get()->groupBy('slowest_unit') as $slowest_unit){
            $stats['slowest_unit'][$slowest_unit[0]->slowest_unit]['id'] = $slowest_unit[0]->slowest_unit;
            $stats['slowest_unit'][$slowest_unit[0]->slowest_unit]['count'] = $slowest_unit->count();
        }

        if (isset($stats['type'])){
            ksort($stats['type']);
        }

        if (isset($stats['slowest_unit'])){
            ksort($stats['slowest_unit']);
        }

        $ownPlanners = array();
        if(\Auth::check()) {
            $ownPlanners = AttackList::where('user_id', \Auth::user()->id)->orderBy('world_id')->get();
        }
        return view('tools.attackPlanner', compact('worldData', 'unitConfig', 'config', 'attackList', 'mode', 'now', 'server', 'stats', 'ownPlanners'));
    }

    public function show(AttackList $attackList){
        BasicFunctions::local();
        $worldData = $attackList->world;
        $server = $worldData->server->code;

        $unitConfig = simplexml_load_string($worldData->units);
        $config = simplexml_load_string($worldData->config);
        $mode = 'show';
        $now = Carbon::now();

        $ownPlanners = array();
        if(\Auth::check()) {
            $ownPlanners = AttackList::where('user_id', \Auth::user()->id)->orderBy('world_id')->get();
        }
        return view('tools.attackPlanner', compact('worldData', 'unitConfig', 'config', 'attackList', 'mode', 'now', 'server', 'ownPlanners'));
    }
    
    public function exportAll(AttackList $attackList){
        BasicFunctions::local();
        $items = $attackList->items()->take(400)->get();
        
        // Workbench
        $exportWB = "";
        foreach ($items as $item){
            $exportWB .= $item->start_village_id."&".$item->target_village_id."&".$item->unitIDToName()."&".(strtotime($item->arrival_time)*1000)."&".$item->type."&false&true&spear=".base64_encode($item->spear)."/sword=".base64_encode($item->sword)."/axe=".base64_encode($item->axe)."/archer=".base64_encode($item->archer)."/spy=".base64_encode($item->spy)."/light=".base64_encode($item->light)."/marcher=".base64_encode($item->marcher)."/heavy=".base64_encode($item->heavy)."/ram=".base64_encode($item->ram)."/catapult=".base64_encode($item->catapult)."/knight=".base64_encode($item->knight)."/snob=".base64_encode($item->snob)."/militia=MA==\n";
        }
        
        //BB-Code
        $exportBB = $this->exportTemplated($attackList, $items,
                __('tool.attackPlanner.export.BB.default.row'), __('tool.attackPlanner.export.BB.default.body'));
        
        //IGM-BB-Code
        $exportIGM = $this->exportTemplated($attackList, $items,
                __('tool.attackPlanner.export.IGM.default.row'), __('tool.attackPlanner.export.IGM.default.body'));
        
        return response()->json([
            "wb" => $exportWB,
            "bb" => $exportBB,
            "igm" => $exportIGM,
        ]);
    }
    
    private function exportTemplated($attackList, $items, $rowTemplate, $bodyTemplate) {
        $count = count($items);
        $i = 1;
        $now = Carbon::now()->locale(App::getLocale());
        $row = "";
        foreach ($items as $item){
            $dateSend = Carbon::parse($item->send_time)->locale(App::getLocale());
            $dateArrival = Carbon::parse($item->arrival_time)->locale(App::getLocale());
            $searchReplaceArrayRow = array(
                '%ELEMENT_ID%' => $i,
                '%TYPE%' => $item->typeIDToName(),
                '%TYPE_IMG%' => "[img]".Icon::icons($item->type)."[/img]",
                '%UNIT%' => "[unit]".$item->unitIDToName()."[/unit]",
                '%SOURCE%' => '[coord]'.($item->start_village!=null?$item->start_village->coordinates():'???').'[/coord]',
                '%TARGET%' => '[coord]'.($item->target_village!=null?$item->target_village->coordinates():'???').'[/coord]',
                '%ATTACKER%' => $item->attackerName(),
                '%DEFENDER%' => $item->defenderName(),
                '%SEND%' => $dateSend->format('Y-m-d H:i:s') . ":" . $item->ms,
                '%ARRIVE%' => $dateArrival->format('Y-m-d H:i:s') . ":" . $item->ms,
                '%PLACE%' => "[url=\"".$item->list->world->url."/game.php?village=".$item->start_village_id."&screen=place&target=".$item->target_village_id."\"]Versammlungsplatz[/url]"
            );
            $row .= str_replace(array_keys($searchReplaceArrayRow),array_values($searchReplaceArrayRow), $rowTemplate)."\n";

            $i++;
        }

        $searchReplaceArrayBody = array(
            '%TITLE%' => $attackList->getTitle(),
            '%ELEMENT_COUNT%' => $count,
            '%ROW%' => $row,
            '%CREATE_AT%' => $now->isoFormat('L LT'),
            '%CREATE_WITHL%' => route('index'),
            '%REATE_WITH%' => "DS-Ultimate",
        );
        
        return str_replace(array_keys($searchReplaceArrayBody),array_values($searchReplaceArrayBody), $bodyTemplate);
    }

    public function importWB(ImportAttackPlannerItemRequest $request, AttackList $attackList){
        abort_unless($attackList->edit_key == $request->get('key'), 403);
        $world = $attackList->world;
        $imports = explode(PHP_EOL, $request->import);
        foreach ($imports as $import){
            if ($import == '') continue;


            $list = explode('&', $import);
            if (count($list) < 7) continue;
            $villageModel = new Village();
            $villageModel->setTable(BasicFunctions::getDatabaseName($world->server->code, $world->name) . '.village_latest');
            $start = $villageModel->find($list[0]);
            $target = $villageModel->find($list[1]);

            if ($start != null && $target != null) {
                $arrival = (int)$list[3];

                if (isset($list[7]) && $list[7] != '') {
                    $units = explode('/', $list[7]);
                    $unitArray = [];
                    foreach ($units as $unit) {
                        $unitSplit = explode('=', $unit, 2);
                        if(count($unitSplit) < 2) continue;
                        $unitArray += [$unitSplit[0] => intval(base64_decode(str_replace('/', '', $unitSplit[1])))];
                    }
                }
                self::newItem($attackList->id, $list[0], $list[1], AttackListItem::unitNameToID($list[2]), date('Y-m-d H:i:s' , $arrival/1000), (in_array($list[4], Icon::attackPlannerTypeIcons()))?$list[4]: -1, (isset($unitArray))?$unitArray:null);
            }
        }

    }

    public function destroyOutdated(AttackList $attackList){
        AttackListItem::where([
            ['send_time', '<', Carbon::createFromTimestamp(time())],
            ['attack_list_id', $attackList->id]
        ])->delete();
        return ['success' => true, 'message' => 'destroy !!'];
    }

    public function clear(AttackList $attackList){
        AttackListItem::where([
            ['attack_list_id', $attackList->id]
        ])->delete();
        return ['success' => true, 'message' => 'cleared !!'];
    }

    public static function newItem($attack_list_id, $start_village_id, $target_village_id, $slowest_unit, $arrival_time, $type, $units){
        $attackplaner = AttackList::find($attack_list_id);
        $startVillage = Village::village($attackplaner->world->server->code, $attackplaner->world->name, $start_village_id);
        $targetVillage = Village::village($attackplaner->world->server->code, $attackplaner->world->name, $start_village_id);
        if(!isset($startVillage) || !isset($targetVillage)){
            return \Response::json(array(
                'data' => 'error',
                'msg' => __('ui.villageNotExist'),
            ));
        }
        $item = new AttackListItem();
        $item->attack_list_id = $attack_list_id;
        $item->start_village_id = $start_village_id;
        $item->target_village_id = $target_village_id;
        $item->slowest_unit = $slowest_unit;
        $item->arrival_time = $arrival_time;
        $item->send_time = $item->calcSend();
        $item->type = $type;
        if ($units != null) {
            foreach ($units as $key => $unit) {
                if ($key != 'militia'){
                    $item->$key = $unit;
                }
            }
        }
        $item->save();
    }

    public static function title(AttackList $attackList, $key, $title){
        abort_unless($attackList->edit_key == $key, 403);
        $attackList->title = $title;
        $attackList->save();
    }

    public function destroy(AttackList $attackList, $key){
        abort_unless($attackList->edit_key == $key, 403);
        if($attackList->delete()){
            return \Response::json(array(
                'data' => 'success',
                'msg' => __('tool.attackPlanner.destroySuccess'),
            ));
        }else{
            return \Response::json(array(
                'data' => 'error',
                'msg' => __('tool.attackPlanner.destroyError'),
            ));
        }
    }
    
    public function apiCreate(Request $request) {
        $req = $request->validate([
            'title' => '',
            'server' => 'required',
            'world' => 'required',
            'API_KEY' => 'required',
            'items' => 'array',
            'items.*' => 'array',
            'items.*.source' => 'required|integer',
            'items.*.destination' => 'required|integer',
            'items.*.slowest_unit' => 'required|integer',
            'items.*.arrival_time' => 'required|integer',
            'items.*.type' => 'required|integer',
        ]);
        
        if(!in_array($req['API_KEY'], explode(";", config("app.API_KEYS")))) {
            abort(403);
        }

        World::existWorld($req['server'], $req['world']);
        $worldData = World::getWorld($req['server'], $req['world']);
        if($worldData->config == null || $worldData->units == null) {
            abort(404, __('tool.attackPlanner.notAvailable'));
        }
        
        $list = new AttackList();
        $list->world_id = $worldData->id;
        $list->title = $req['title'] ?? "";
        $list->edit_key = Str::random(40);
        $list->show_key = Str::random(40);
        $list->save();
        
        foreach($req['items'] as $it) {
            $item = new AttackListItem();
            $item->attack_list_id = $list->id;
            $item->start_village_id = $it['source'];
            $item->target_village_id = $it['destination'];
            $item->slowest_unit = $it['slowest_unit'];
            $item->arrival_time = $it['arrival_time'];
            $item->send_time = $item->calcSend();
            $item->type = $it['type'];
            $item->save();
        }
        
        return \Response::json(array(
            'id' => $item->attack_list_id,
            'edit' => route('tools.attackPlannerMode', [$list->id, "edit", $list->edit_key]),
            'show' => route('tools.attackPlannerMode', [$list->id, "show", $list->show_key]),
        ));
    }
}
