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
use App\World;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class AttackPlannerController extends BaseController
{
    public function index($server, $world){
        $worldData = World::getAndCheckWorld($server, $world);

        abort_if($worldData->config == null || $worldData->units == null, 404, __("ui.errors.404.toolNotAvail.attackPlanner"));

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
        $list->api = false;
        $list->save();

        return redirect()->route('tools.attackPlannerMode', [$list->id, 'edit', $list->edit_key]);
    }

    public function mode(AttackList $attackList, $mode, $key){
        $worldData = $attackList->world;
        abort_if($worldData->config == null || $worldData->units == null, 404, __("ui.errors.404.toolNotAvail.attackPlanner"));
        abort_if($attackList->world->maintananceMode, 503);

        switch ($mode){
            case 'edit':
                abort_unless($attackList->edit_key == $key, 403);
                $attackList->api = false;
                $attackList->touch();
                return $this->edit($attackList);
            case 'show':
                abort_unless($attackList->show_key == $key, 403);
                $attackList->api = false;
                $attackList->touch();
                return $this->show($attackList);
            case 'exportWB':
                abort_unless($attackList->show_key == $key || $attackList->edit_key == $key, 403);
                $attackList->touch();
                return $this->exportWB($attackList);
            case 'exportBB':
                abort_unless($attackList->show_key == $key || $attackList->edit_key == $key, 403);
                $attackList->touch();
                return $this->exportBB($attackList);
            case 'exportIGM':
                abort_unless($attackList->show_key == $key || $attackList->edit_key == $key, 403);
                $attackList->touch();
                return $this->exportIGM($attackList);
            default:
                abort(404);
        }
    }

    public function modePost(Request $request, AttackList $attackList, $mode, $key){
        $worldData = $attackList->world;
        abort_if($worldData->config == null || $worldData->units == null, 404, __("ui.errors.404.toolNotAvail.attackPlanner"));
        abort_if($attackList->world->maintananceMode, 503);

        switch ($mode){
            case 'destroyOutdated':
                abort_unless($attackList->edit_key == $key, 403);
                $attackList->touch();
                return static::destroyOutdated($attackList);
            case 'destroySent':
                abort_unless($attackList->edit_key == $key, 403);
                $attackList->touch();
                return static::destroySent($attackList);
            case 'clear':
                abort_unless($attackList->edit_key == $key, 403);
                $attackList->touch();
                return static::clear($attackList);
            case 'saveAsUV':
                abort_unless($attackList->show_key == $key, 403);
                $attackList->uvMode = $request->value == true;
                if($attackList->isDirty()) {
                    $attackList->save();
                } else {
                    $attackList->touch();
                }
                return ['success' => true, 'message' => 'saved'];
            default:
                abort(404);
        }
    }

    private function edit(AttackList $attackList){
        $worldData = $attackList->world;
        $server = $worldData->server;

        $unitConfig = simplexml_load_string($worldData->units);
        $config = simplexml_load_string($worldData->config);
        $mode = 'edit';
        $now = Carbon::now();

        // calculate stats directly in the database to avoid loading all items
        $stats['total'] = $attackList->items()->count();
        $stats['start_village'] = $attackList->items()->distinct('start_village_id')->count('start_village_id');
        $stats['target_village'] = $attackList->items()->distinct('target_village_id')->count('target_village_id');

        $statsType = $attackList->items()
            ->select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->orderBy('type')
            ->get()
            ->mapWithKeys(function ($row) {
                return [$row->type => ['id' => $row->type, 'count' => $row->count]];
            })->toArray();

        if (!empty($statsType)) {
            $stats['type'] = $statsType;
        }

        $statsSlowest = $attackList->items()
            ->select('slowest_unit', DB::raw('count(*) as count'))
            ->groupBy('slowest_unit')
            ->orderBy('slowest_unit')
            ->get()
            ->mapWithKeys(function ($row) {
                return [$row->slowest_unit => ['id' => $row->slowest_unit, 'count' => $row->count]];
            })->toArray();

        if (!empty($statsSlowest)) {
            $stats['slowest_unit'] = $statsSlowest;
        }

        $ownPlanners = array();
        if(\Auth::check()) {
            $ownPlanners = AttackList::where('user_id', \Auth::user()->id)->orderBy('world_id')->get();
        }
        return view('tools.attackPlannerMain', compact('worldData', 'unitConfig', 'config', 'attackList', 'mode', 'now', 'server', 'stats', 'ownPlanners'));
    }

    private function show(AttackList $attackList){
        $worldData = $attackList->world;
        $server = $worldData->server;

        $unitConfig = simplexml_load_string($worldData->units);
        $config = simplexml_load_string($worldData->config);
        $mode = 'show';
        $now = Carbon::now();

        $ownPlanners = array();
        if(\Auth::check()) {
            $ownPlanners = AttackList::where('user_id', \Auth::user()->id)->orderBy('world_id')->get();
        }
        return view('tools.attackPlannerMain', compact('worldData', 'unitConfig', 'config', 'attackList', 'mode', 'now', 'server', 'ownPlanners'));
    }

    private function exportWB(AttackList $attackList){
        $items = $attackList->items()->get();

        // Workbench
        $exportWB = "";
        foreach ($items as $item){
            $arrTimestamp = (strtotime($item->arrival_time)*1000) + $item->ms;
            $isSent = ($item->send == 0)?"false":"true";
            $exportWB .= $item->start_village_id."&".$item->target_village_id."&".$item->unitIDToName().
                "&$arrTimestamp&".$item->type."&false&".$isSent."&spear=".base64_encode($item->spear)."/sword=".base64_encode($item->sword).
                "/axe=".base64_encode($item->axe)."/archer=".base64_encode($item->archer)."/spy=".base64_encode($item->spy).
                "/light=".base64_encode($item->light)."/marcher=".base64_encode($item->marcher)."/heavy=".base64_encode($item->heavy).
                "/ram=".base64_encode($item->ram)."/catapult=".base64_encode($item->catapult)."/knight=".base64_encode($item->knight).
                "/snob=".base64_encode($item->snob)."/militia=MA==\n";
        }

        return response()->json([
            "data" => $exportWB,
        ]);
    }

    private function exportBB(AttackList $attackList){
        $items = $attackList->items()->take(400)->get();

        //BB-Code
        $exportBB = $this->exportTemplated($attackList, $items,
            __('tool.attackPlanner.export.BB.default.row'), __('tool.attackPlanner.export.BB.default.body'));

        return response()->json([
            "data" => $exportBB,
        ]);
    }

    private function exportIGM(AttackList $attackList){
        $items = $attackList->items()->take(400)->get();

        //IGM-BB-Code
        $exportIGM = $this->exportTemplated($attackList, $items,
            __('tool.attackPlanner.export.IGM.default.row'), __('tool.attackPlanner.export.IGM.default.body'));

        return response()->json([
            "data" => $exportIGM,
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

            $uv = "";
            if($item->list->uvMode) {
                $uv = "t={$item->start_village->owner}&";
            }
            $placeURL = "{$item->list->world->url}/game.php?{$uv}village={$item->start_village_id}&screen=place&target={$item->target_village_id}";
            $placeURL .= '&spear='.$item->spear.'&sword='.$item->sword.'&axe='.$item->axe.'&archer='.$item->archer.'&spy='.$item->spy.'&light='.$item->light.'&marcher='.$item->marcher.'&heavy='.$item->heavy.'&ram='.$item->ram.'&catapult='.$item->catapult.'&knight='.$item->knight.'&snob='.$item->snob;
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
                '%PLACE%' => "[url=\"$placeURL\"]Versammlungsplatz[/url]"
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
            '%CREATE_WITH%' => "DS-Ultimate",
        );

        return str_replace(array_keys($searchReplaceArrayBody),array_values($searchReplaceArrayBody), $bodyTemplate);
    }

    public function importWB(ImportAttackPlannerItemRequest $request, AttackList $attackList){
        abort_unless($attackList->edit_key == $request->get('key'), 403);
        $imports = explode(PHP_EOL, $request->import);

        $err = [];
        $all = [];
        foreach ($imports as $import){
            $import = trim($import);
            if ($import == '') continue;

            $list = explode('&', $import);
            if (count($list) < 7) {
                $err[] = __('tool.attackPlanner.wbImportWrongData');
                continue;
            }

            $arrival = (int)$list[3];
            $unitArray = null;

            if (isset($list[7]) && $list[7] != '') {
                $units = explode('/', $list[7]);
                $unitArray = [];
                foreach ($units as $unit) {
                    $unitSplit = explode('=', $unit, 2);
                    if(count($unitSplit) < 2) continue;
                    $unitArray[$unitSplit[0]] = base64_decode(str_replace('/', '', $unitSplit[1]));
                }
            }
            $it = self::newItem($err, $attackList, $list[0], $list[1], AttackListItem::unitNameToID($list[2]),
                date('Y-m-d H:i:s' , $arrival/1000), (in_array($list[4], AttackListItem::attackPlannerTypeIcons()))?$list[4]: -1,
                $unitArray, ms: $arrival%1000);

            if($it != null) {
                $all[] = $it;
            }
        }

        $insert = new AttackListItem();
        $allOk = true;
        foreach (array_chunk($all,3000) as $t){
            $allOk &= $insert->insert($t);
        }

        if(! $allOk) {
            $err[] = "Error during insert";
        }

        if(count($err) > 0) {
            return AttackListItem::errJsonReturn($err);
        }
        return \Response::json(array(
            'data' => 'success',
            'title' => __('tool.attackPlanner.importWBSuccessTitle'),
            'msg' => __('tool.attackPlanner.importWBSuccess'),
        ));
    }

    public static function destroyOutdated(AttackList $attackList){
        AttackListItem::where([
            ['send_time', '<', Carbon::now()],
            ['attack_list_id', $attackList->id]
        ])->delete();
        return ['success' => true, 'message' => 'destroy !!'];
    }

    public static function destroySent(AttackList $attackList){
        AttackListItem::where([
            ['send', 1],
            ['attack_list_id', $attackList->id]
        ])->delete();
        return ['success' => true, 'message' => 'destroy !!'];
    }

    public static function clear(AttackList $attackList){
        AttackListItem::where([
            ['attack_list_id', $attackList->id]
        ])->delete();
        return ['success' => true, 'message' => 'cleared !!'];
    }

    public static $villageCache = null;
    public static function newItem(&$err, AttackList $parList, $start_village_id, $target_village_id, $slowest_unit, $arrival_time,
                                   $type, $units, $support_boost=0.0, $tribe_skill=0.0, $ms=0){
        if(static::$villageCache == null) {
            $tableName = BasicFunctions::getWorldDataTable($parList->world, 'village_latest');
            self::$villageCache = [];
            foreach(DB::select("SELECT villageID,x,y,bonus_id FROM $tableName") as $v) {
                self::$villageCache[$v->villageID] = $v;
            }
        }

        $curErr = [];
        $item = new AttackListItem();
        $item->attack_list_id = $parList->id;

        if(isset(self::$villageCache[$start_village_id])) {
            $item->start_village_id = $start_village_id;
            $sVillage = self::$villageCache[$start_village_id];
        } else {
            $curErr[] = __('tool.attackPlanner.villageNotExistStart');
        }
        if(isset(self::$villageCache[$target_village_id])) {
            $item->target_village_id = $target_village_id;
            $tVillage = self::$villageCache[$target_village_id];
        } else {
            $curErr[] = __('tool.attackPlanner.villageNotExistTarget');
        }

        $item->type = $type;
        $item->slowest_unit = $slowest_unit;
        $item->support_boost = $support_boost;
        $item->tribe_skill = $tribe_skill;

        $item->arrival_time = $arrival_time;
        $item->ms = $ms;
        if(count($curErr) == 0) {
            $unitConfig = $parList->world->unitConfig();
            $dist = sqrt(pow($sVillage->x - $tVillage->x, 2) + pow($sVillage->y - $tVillage->y, 2));

            if($tVillage->bonus_id >= 11 && $tVillage->bonus_id <= 21) {
                //great siege village always same distance
                if($item->slowest_unit == 4) {
                    $dist = 3; // spy
                } else {
                    $dist = 15;
                }
            }

            $unit = AttackListItem::$units[$item->slowest_unit];
            $boost = 1 + ($item->support_boost ?? 0.0) + ($item->tribe_skill ?? 0.0);
            $runningTime = round(((float)$unitConfig->$unit->speed * 60) * $dist / $boost);
            $item->send_time = $item->arrival_time->copy()->subSeconds($runningTime);
        }

        if ($units != null) {
            if(is_array($units)) {
                $curErr = array_merge($curErr, $item->setUnitsArr($units));
            } else {
                $curErr = array_merge($curErr, $item->setUnits($units, true));
            }
        } else {
            $item->setEmptyUnits();
        }

        if(count($curErr) == 0) $curErr = array_merge($curErr, $item->verifyTime());
        $err = array_merge($err, $curErr);

        if(count($curErr) > 0) {
            return null;
        }

        $insertTime = Carbon::now();
        return array_merge($item->getAttributes(), [
            'created_at' => $insertTime,
            'updated_at' => $insertTime,
        ]);
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
}
