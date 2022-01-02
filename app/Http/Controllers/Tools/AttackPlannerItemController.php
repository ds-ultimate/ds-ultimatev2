<?php
/**
 * Created by IntelliJ IDEA.
 * User: crams
 * Date: 10.08.2019
 * Time: 20:38
 */

namespace App\Http\Controllers\Tools;


use App\Tool\AttackPlanner\AttackList;
use App\Tool\AttackPlanner\AttackListItem;
use App\Util\BasicFunctions;
use App\Util\Icon;
use App\Http\Requests\StoreAttackPlannerItemRequest;
use App\Village;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class AttackPlannerItemController extends BaseController
{

    private static $units = ['spear', 'sword', 'axe', 'archer', 'spy', 'light', 'marcher', 'heavy', 'ram', 'catapult', 'knight', 'snob'];

    public function store(Request $request)
    {
        $attackplaner = AttackList::findOrFail($request->attack_list_id);
        abort_unless($request->key == $attackplaner->edit_key, 403);
        $unitC = [];
        foreach (self::$units as $unit){
            if ($request->get($unit) == null){
                $unitC[$unit] = 0;
            }else{
                $value = $request->get($unit);
                if ($value <= 2147483648){
                    $unitC[$unit] = intval($value);
                }else{
                    return \Response::json(array(
                        'data' => 'error',
                        'title' => __('tool.attackPlanner.errorUnitCountTitle'),
                        'msg' => __('ui.unit.'.$unit).' '.__('tool.attackPlanner.errorUnitCount'),
                    ));
                }
            }
        }
        
        $item = new AttackListItem();
        $item->attack_list_id = $request->attack_list_id;
        if (!$item->setVillageID($request->xStart, $request->yStart, $request->xTarget, $request->yTarget)){
            return \Response::json(array(
                'data' => 'error',
                'title' => __('tool.attackPlanner.villageNotExistTitle'),
                'msg' => __('tool.attackPlanner.villageNotExist'),
            ));
        }
        $item->type = $request->type;
        $item->slowest_unit = $request->slowest_unit;
        $item->note = $request->note;
        if($request->time_type == 0){
            $item->arrival_time = Carbon::parse($request->day.' '.$request->time);
            $item->send_time = $item->calcSend();
        }else{
            $item->send_time = Carbon::parse($request->day.' '.$request->time);
            $item->arrival_time = $item->calcArrival();
        }
        $ms = explode('.',$request->time);
        if (count($ms) > 1){
            $item->ms = $ms[1];
        }else{
            $item->ms = 0;
        }
        $item->spear = $unitC["spear"];
        $item->sword = $unitC["sword"];
        $item->axe = $unitC["axe"];
        $item->archer = $unitC["archer"];
        $item->spy = $unitC["spy"];
        $item->light = $unitC["light"];
        $item->marcher = $unitC["marcher"];
        $item->heavy = $unitC["heavy"];
        $item->ram = $unitC["ram"];
        $item->catapult = $unitC["catapult"];
        $item->knight = $unitC["knight"];
        $item->snob = $unitC["snob"];
        $item->send_time = $item->calcSend();
        if($item->save()){
            return \Response::json(array(
                'data' => 'success',
                'title' => __('tool.attackPlanner.storeSuccessTitle'),
                'msg' => __('tool.attackPlanner.storeSuccess'),
            ));
        }else{
            return \Response::json(array(
                'data' => 'error',
                'title' => __('tool.attackPlanner.storeErrorTitle'),
                'msg' => __('tool.attackPlanner.storeError'),
            ));
        }
    }

    public function data(AttackList $attackList, $key){
        abort_unless($attackList->show_key == $key, 403);
        \App\Http\Controllers\API\DatatablesController::limitResults(200);


        $query = AttackListItem::query()->where('attack_list_id', $attackList->id);

        return Datatables::of($query)
            ->orderColumns(['send_time', 'arrival_time'], '-:column $1')
            ->setRowId(function (AttackListItem $attackListItem) {
                return $attackListItem->id;
            })
            ->setRowData([
                'xStart' => function(AttackListItem $attackListItem) {
                    if($attackListItem->start_village == null) {
                        return '???';
                    }
                    return $attackListItem->start_village->x;
                },
                'yStart' => function(AttackListItem $attackListItem) {
                    if($attackListItem->start_village == null) {
                        return '???';
                    }
                    return $attackListItem->start_village->y;
                },
                'xTarget' => function(AttackListItem $attackListItem) {
                    if($attackListItem->target_village == null) {
                        return '???';
                    }
                    return $attackListItem->target_village->x;
                },
                'yTarget' => function(AttackListItem $attackListItem) {
                    if($attackListItem->target_village == null) {
                        return '???';
                    }
                    return $attackListItem->target_village->y;
                },
                'day' => function(AttackListItem $attackListItem) {
                    return $attackListItem->arrival_time->format('Y-m-d');
                },
                'time' => function(AttackListItem $attackListItem) {
                    if (strlen($attackListItem->ms) < 3){
                        $ms = (strlen($attackListItem->ms) == 2)? '0'.$attackListItem->ms : '00'.$attackListItem->ms;
                    }else{
                        $ms = $attackListItem->ms;
                    }
                    return $attackListItem->arrival_time->format('H:i:s').'.'.$ms;
                },
                'type' => function(AttackListItem $attackListItem) {
                    return $attackListItem->type;
                },
                'slowest_unit' => function(AttackListItem $attackListItem) {
                    return $attackListItem->slowest_unit;
                },
            ])
            ->addColumn('select', function (){
                return '';
            })
            ->addColumn('start_village', function (AttackListItem $attackListItem) {
                $village = $attackListItem->start_village;
                if($village == null) {
                    return __('tool.attackPlanner.villageNotExist');
                }
                return BasicFunctions::linkVillage($attackListItem->list->world, $village->villageID, '['.$village->x.'|'.$village->y.'] '.BasicFunctions::decodeName($village->name), null, null, true);
            })
            ->addColumn('target_village', function (AttackListItem $attackListItem) {
                $village = $attackListItem->target_village;
                if($village == null) {
                    return __('tool.attackPlanner.villageNotExist');
                }
                return BasicFunctions::linkVillage($attackListItem->list->world, $village->villageID, '['.$village->x.'|'.$village->y.'] '.BasicFunctions::decodeName($village->name), null, null, true);
            })
            ->editColumn('type', function (AttackListItem $attackListItem) {
                return '<img id="type_img" src="'.Icon::icons($attackListItem->type).'" data-toggle="popover" data-trigger="hover" data-content="'.$attackListItem->typeIDToName().'">';
            })
            ->editColumn('slowest_unit', function (AttackListItem $attackListItem) {
                return '<img id="type_img" src="'.Icon::icons($attackListItem->slowest_unit).'" data-toggle="popover" data-trigger="hover" data-content="'.$attackListItem->unitIDToNameOutput().'">';
            })
            ->addColumn('attacker', function (AttackListItem $attackListItem) {
                return BasicFunctions::linkPlayer($attackListItem->list->world, $attackListItem->attackerID(), $attackListItem->attackerName(), null, null, true);
            })
            ->addColumn('defender', function (AttackListItem $attackListItem) {
                return BasicFunctions::linkPlayer($attackListItem->list->world, $attackListItem->defenderID(), $attackListItem->defenderName(), null, null, true);
            })
            ->addColumn('send_time', function (AttackListItem $attackListItem) {
                return $attackListItem->send_time->format('d.m.Y H:i:s');
            })
            ->addColumn('arrival_time', function (AttackListItem $attackListItem) {
                if (strlen($attackListItem->ms) < 3){
                    $ms = (strlen($attackListItem->ms) == 2)? '0'.$attackListItem->ms : '00'.$attackListItem->ms;
                }else{
                    $ms = $attackListItem->ms;
                }
                return $attackListItem->arrival_time->format('d.m.Y H:i:s').'.<small class="text-muted">'.$ms.'</small>';
            })
            ->addColumn('time', function (AttackListItem $attackListItem) {
                return '<countdown date="'. $attackListItem->send_time->timestamp .'"></countdown>';
            })
            ->addColumn('info', function (AttackListItem $attackListItem){
                $unitCount = '';
                foreach (self::$units as $unit){
                    if ($attackListItem->$unit != 0){
                        $unitCount .= "<img class='pr-3' src='".asset('images/ds_images/unit/'.$unit.'.png')."' height='15px'> <b>".BasicFunctions::numberConv($attackListItem->$unit)."</b>".(($unit != 'snob')? '<br>':'');
                    }
                }
                return '<h4 class="mb-0"><i class="fas fa-info-circle text-info" data-toggle="popover" title="'.__('ui.tabletitel.info').'" data-trigger="hover" data-content="'.(($unitCount == '')?'':$unitCount.'<hr>').'<div class=\'row\'><u class=\'font-weight-bold px-3\'>'.__('global.note_text').':</u><br><p class=\'px-3\'>'.$attackListItem->note.'</p></div>" data-placement="left"></i></h4>';
            })
            ->addColumn('action', function (AttackListItem $attackListItem){
                return '<h4 class="mb-0"><a class="text-success" target="_blank" href="'.$attackListItem->list->world->url.'/game.php?village='.$attackListItem->start_village_id.'&screen=place&target='.$attackListItem->target_village_id.'"><i class="'.(($attackListItem->send == 0)? 'fas fa-play-circle' : 'fas fa-redo').'" onclick="'.(($attackListItem->send == 0)? 'sendattack('.$attackListItem->id.')' : '').'"></i></a></h4>';
            })
            ->addColumn('delete', function (AttackListItem $attackListItem){
                return '<h4 class="mb-0"><a class="text-primary" onclick="edit('.$attackListItem->id.')" style="cursor: pointer;" data-toggle="modal" data-target=".bd-example-modal-xl"><i class="fas fa-edit"></i></a><a class="text-danger" onclick="destroy('.$attackListItem->id.',\''.$attackListItem->list->edit_key.'\')" style="cursor: pointer;"><i class="fas fa-times"></i></a></h4>';
            })
            ->rawColumns(['type', 'start_village', 'target_village', 'attacker', 'defender', 'arrival_time', 'slowest_unit', 'time', 'info', 'action', 'delete'])
            ->make(true);
    }

    public function destroy(Request $request, AttackListItem $attackListItem)
    {
        if ($attackListItem->list->edit_key === $request->key){
            $attackListItem->delete();
            return ['success' => true, 'message' => 'destroy !!'];
        }
    }

    public function update(Request $request, AttackListItem $attackListItem){
        $attackplaner = $attackListItem->list;
        abort_unless($request->key == $attackplaner->edit_key, 403);
        $unitC = [];
        foreach (self::$units as $unit){
            if ($request->get($unit) == null){
                $unitC[$unit] = 0;
            }else{
                $value = $request->get($unit);
                if ($value <= 2147483648){
                    $unitC[$unit] = intval($value);
                }else{
                    return \Response::json(array(
                        'data' => 'error',
                        'title' => __('tool.attackPlanner.errorUnitCountTitle'),
                        'msg' => __('ui.unit.'.$unit).' '.__('tool.attackPlanner.errorUnitCount'),
                    ));
                }
            }
        }

        $attackListItem->type = $request->type;
        if (!$attackListItem->setVillageID($request->xStart, $request->yStart, $request->xTarget, $request->yTarget)){
            return \Response::json(array(
                'data' => 'error',
                'title' => __('tool.attackPlanner.villageNotExistTitle'),
                'msg' => __('tool.attackPlanner.villageNotExist'),
            ));
        }
        $attackListItem->slowest_unit = $request->slowest_unit;
        $attackListItem->note = $request->note;
        if($request->time_type == 0){
            $attackListItem->arrival_time = Carbon::parse($request->day.' '.$request->time);
            $attackListItem->send_time = $attackListItem->calcSend();
        }else{
            $attackListItem->send_time = Carbon::parse($request->day.' '.$request->time);
            $attackListItem->arrival_time = $attackListItem->calcArrival();
        }
        $ms = explode('.',$request->time);
        if (count($ms) > 1){
            $attackListItem->ms = $ms[1];
        }else{
            $attackListItem->ms = 0;
        }
        $attackListItem->spear = $unitC["spear"];
        $attackListItem->sword = $unitC["sword"];
        $attackListItem->axe = $unitC["axe"];
        $attackListItem->archer = $unitC["archer"];
        $attackListItem->spy = $unitC["spy"];
        $attackListItem->light = $unitC["light"];
        $attackListItem->marcher = $unitC["marcher"];
        $attackListItem->heavy = $unitC["heavy"];
        $attackListItem->ram = $unitC["ram"];
        $attackListItem->catapult = $unitC["catapult"];
        $attackListItem->knight = $unitC["knight"];
        $attackListItem->snob = $unitC["snob"];
        $attackListItem->send_time = $attackListItem->calcSend();
        if($attackListItem->update()){
            return \Response::json(array(
                'data' => 'success',
                'title' => __('tool.attackPlanner.updateSuccessTitle'),
                'msg' => __('tool.attackPlanner.updateSuccess'),
            ));
        }else{
            return \Response::json(array(
                'data' => 'error',
                'title' => __('tool.attackPlanner.updateErrorTitle'),
                'msg' => __('tool.attackPlanner.updateError'),
            ));
        }
    }

    public function multiedit(Request $request) {
        $attackplaner = AttackList::find($request->id);
        abort_unless($request->key == $attackplaner->edit_key, 403);

        $server = $attackplaner->world->server->code;
        $world = $attackplaner->world->name;

        if (count($request->items) <= 0) {
            return \Response::json(array(
                'data' => 'error',
                'title' => __('tool.attackPlanner.attackCountTitle'),
                'msg' => __('tool.attackPlanner.attackCount'),
            ));
        }
        
        foreach ($request->items as $item) {
            $attackListItem = AttackListItem::find($item);
            if($item == null) {
                continue;
            }

            if (isset($request->checkboxes['type'])) {
                $attackListItem->type = $request->type;
            }

            if (isset($request->checkboxes['note'])) {
                $attackListItem->note = $request->note;
            }

            if (isset($request->checkboxes['start']) || isset($request->checkboxes['target'])){
                if (isset($request->checkboxes['start'])) {
                    $xStart = $request->xStart;
                    $yStart = $request->yStart;
                } else {
                    $villageStart = Village::village($server, $world, $attackListItem->start_village_id);
                    $xStart = $villageStart->x;
                    $yStart = $villageStart->y;
                }
                if (isset($request->checkboxes['target'])) {
                    $xTarget = $request->xTarget;
                    $yTarget = $request->yTarget;
                } else {
                    $villageTarget = Village::village($server, $world, $attackListItem->target_village_id);
                    $xTarget = $villageTarget->x;
                    $yTarget = $villageTarget->y;
                }
                if (!$attackListItem->setVillageID($xStart, $yStart, $xTarget, $yTarget)) {
                    return \Response::json(array(
                        'data' => 'error',
                        'title' => __('tool.attackPlanner.villageNotExistTitle'),
                        'msg' => __('tool.attackPlanner.villageNotExist'),
                    ));
                }
            }

            $timeType = 0; //default keep arrival_time
            if (isset($request->checkboxes['date'])) {
                if ($request->time_type == 0) {
                    $attackListItem->arrival_time = Carbon::parse($request->day. ' ' .$request->time);
                } else {
                    $attackListItem->send_time = Carbon::parse($request->day . ' ' . $request->time);
                    $attackListItem->arrival_time = $attackListItem->calcArrival();
                }
            }

            if (isset($request->checkboxes['slowest_unit'])) {
                $attackListItem->slowest_unit = $request->slowest_unit;
            }

            foreach (self::$units as $unit){
                if (isset($request->checkboxes[$unit])) {
                    $value = $request->$unit;
                    if ($value != null) {
                        if ($value <= 2147483648) {
                            $attackListItem->$unit = intval($value);
                        } else {
                            return \Response::json(array(
                                'data' => 'error',
                                'title' => __('tool.attackPlanner.errorUnitCountTitle'),
                                'msg' => __('ui.unit.' . $unit) . ' ' . __('tool.attackPlanner.errorUnitCount'),
                            ));
                        }
                    }
                }
            }

            $attackListItem->send_time = $attackListItem->calcSend();
            $attackListItem->update();
        }

        return \Response::json(array(
            'data' => 'success',
            'title' => __('tool.attackPlanner.multieditSuccessTitle'),
            'msg' => __('tool.attackPlanner.multieditSuccess'),
        ));
    }

    function sendattack(Request $request){
        $attackListItem = AttackListItem::find($request->id);
        abort_if($attackListItem === null, 404);
        abort_unless($request->key == $attackListItem->list->show_key, 403);
        $attackListItem->send = 1;
        $attackListItem->update();
    }
}
