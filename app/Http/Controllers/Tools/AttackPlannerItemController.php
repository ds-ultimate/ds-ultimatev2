<?php
/**
 * Created by IntelliJ IDEA.
 * User: crams
 * Date: 10.08.2019
 * Time: 20:38
 */

namespace App\Http\Controllers\Tools;


use App\Http\Controllers\API\DatatablesController;
use App\Tool\AttackPlanner\AttackList;
use App\Tool\AttackPlanner\AttackListItem;
use App\Util\BasicFunctions;
use App\Util\Icon;
use App\Village;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;

class AttackPlannerItemController extends BaseController
{
    public function store(Request $request)
    {
        $req = $request->validate(array_merge([
            'attack_list_id' => 'required|integer',
        ], static::generateEditValidation()));
        $attackplaner = AttackList::findOrFail($req['attack_list_id']);
        abort_unless($req['key'] == $attackplaner->edit_key, 403);
        abort_if($attackplaner->world->maintananceMode, 503);

        $err = [];
        $item = new AttackListItem();
        $item->attack_list_id = $req['attack_list_id'];
        $err = array_merge($err, $item->setVillageID($req['xStart'], $req['yStart'], $req['xTarget'], $req['yTarget']));
        $item->type = $req['type'];
        $item->slowest_unit = $req['slowest_unit'];
        $item->note = $req['note'];
        $item->support_boost = floatval($req['support_boost']);
        $item->tribe_skill = floatval($req['tribe_skill']);

        if($req['time_type'] == 0){
            $item->arrival_time = Carbon::parse($req['day'].' '.$req['time']);
        }else{
            $item->send_time = Carbon::parse($req['day'].' '.$req['time']);
            if(count($err) == 0) $item->arrival_time = $item->calcArrival();
        }
        $ms = explode('.',$req['time']);
        if (count($ms) > 1){
            $item->ms = $ms[1];
        }else{
            $item->ms = 0;
        }
        $err = array_merge($err, $item->setUnitsArr($req, true));

        if(count($err) == 0) {
            $item->send_time = $item->calcSend();
            $err = array_merge($err, $item->verifyTime());
        }

        if(count($err) > 0) {
            return AttackListItem::errJsonReturn($err);
        }
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
        $whitelist = ['select', 'start_village_id', 'attacker', 'target_village_id', 'defender', 'slowest_unit',
            'type', 'send_time', 'arrival_time', 'time', 'info', 'action', 'delete'];
        DatatablesController::limitResults(200, $whitelist);
        abort_if($attackList->world->maintananceMode, 503);

        $query = AttackListItem::getJoinedQuery($attackList->world)
            ->where('attack_list_id', $attackList->id);

        return DataTables::of($query)
            ->orderColumn('attacker', 'start_village__playerLatest__name $1')
            ->orderColumn('defender', 'target_village__playerLatest__name $1')
            ->orderColumns(['send_time', 'arrival_time'], '-:column $1')
            ->setRowId(function (AttackListItem $attackListItem) {
                return $attackListItem->id;
            })
            ->setRowData([
                'xStart' => function(AttackListItem $attackListItem) {
                    if($attackListItem->start_village__x == null) {
                        return '???';
                    }
                    return $attackListItem->start_village__x;
                },
                'yStart' => function(AttackListItem $attackListItem) {
                    if($attackListItem->start_village__y == null) {
                        return '???';
                    }
                    return $attackListItem->start_village__y;
                },
                'xTarget' => function(AttackListItem $attackListItem) {
                    if($attackListItem->target_village__x == null) {
                        return '???';
                    }
                    return $attackListItem->target_village__x;
                },
                'yTarget' => function(AttackListItem $attackListItem) {
                    if($attackListItem->target_village__y == null) {
                        return '???';
                    }
                    return $attackListItem->target_village__y;
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
                'sday' => function(AttackListItem $attackListItem) {
                    return $attackListItem->send_time->format('Y-m-d');
                },
                'stime' => function(AttackListItem $attackListItem) {
                    if (strlen($attackListItem->ms) < 3){
                        $ms = (strlen($attackListItem->ms) == 2)? '0'.$attackListItem->ms : '00'.$attackListItem->ms;
                    }else{
                        $ms = $attackListItem->ms;
                    }
                    return $attackListItem->send_time->format('H:i:s').'.'.$ms;
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
            ->editColumn('start_village_id', function (AttackListItem $attackListItem) use ($attackList) {
                if($attackListItem->start_village__name == null) {
                    return __('tool.attackPlanner.villageNotExist');
                }
                return BasicFunctions::linkVillage(
                    $attackList->world,
                    $attackListItem->start_village_id,
                    '['.$attackListItem->start_village__x.'|'.$attackListItem->start_village__y.'] '.
                    BasicFunctions::decodeName($attackListItem->start_village__name),
                    null,
                    null,
                    true
                );
            })
            ->editColumn('target_village_id', function (AttackListItem $attackListItem) use ($attackList) {
                if($attackListItem->target_village__name == null) {
                    return __('tool.attackPlanner.villageNotExist');
                }
                return BasicFunctions::linkVillage(
                    $attackList->world,
                    $attackListItem->target_village_id,
                    '['.$attackListItem->target_village__x.'|'.$attackListItem->target_village__y.'] '.
                    BasicFunctions::decodeName($attackListItem->target_village__name),
                    null,
                    null,
                    true
                );
            })
            ->editColumn('type', function (AttackListItem $attackListItem) {
                return '<img id="type_img" src="'.Icon::icons($attackListItem->type).'" data-toggle="popover" data-trigger="hover" data-content="'.$attackListItem->typeIDToName().'">';
            })
            ->editColumn('slowest_unit', function (AttackListItem $attackListItem) {
                return '<img id="type_img" src="'.Icon::icons($attackListItem->slowest_unit).'" data-toggle="popover" data-trigger="hover" data-content="'.$attackListItem->unitIDToNameOutput().'">';
            })
            ->addColumn('attacker', function (AttackListItem $attackListItem) use ($attackList) {
                return BasicFunctions::linkPlayer(
                    $attackList->world,
                    $attackListItem->attackerID(),
                    $attackListItem->attackerName(),
                    null,
                    null,
                    true
                );
            })
            ->addColumn('defender', function (AttackListItem $attackListItem) use ($attackList) {
                return BasicFunctions::linkPlayer(
                    $attackList->world,
                    $attackListItem->defenderID(),
                    $attackListItem->defenderName(),
                    null,
                    null,
                    true
                );
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
                foreach (AttackListItem::$units as $unit){
                    if ($attackListItem->$unit != null){
                        if(is_numeric($attackListItem->$unit)) {
                            $cntFormat = BasicFunctions::numberConv($attackListItem->$unit);
                        } else {
                            $cntFormat = BasicFunctions::escape($attackListItem->$unit);
                        }
                        $unitCount .= "<img class='pr-3' src='".asset('images/ds_images/unit/'.$unit.'.png')."' height='15px'> <b>$cntFormat</b>".(($unit != 'snob')? '<br>':'');
                    }
                }
                $speedBoost = "";
                if($attackListItem->support_boost != 0.00){
                    $speedBoost .= "<img class='pr-3' src='".asset("/images/ds_images/boost/support_boost.png")."' height='15px'>  <b> ".BasicFunctions::floatConvtoProcent($attackListItem->support_boost)."%</b> <br>";
                };
                if($attackListItem->tribe_skill != 0.00){
                    $speedBoost .= "<img class='pr-3' src='".asset("/images/ds_images/boost/tribe_skill.png")."' height='15px'> <b>".BasicFunctions::floatConvtoProcent($attackListItem->tribe_skill)."%</b> <br>";
                };
                $popoverData = "";
                if($unitCount != '' || $speedBoost != '' || ($attackListItem->note != null && strlen($attackListItem->note) > 0)) {
                    $popoverData = ' data-toggle="popover" title="'.__('ui.tabletitel.info').'" data-trigger="hover" data-content="'.(($unitCount == '')?'':$unitCount.'<hr>').(($speedBoost == '')?'':$speedBoost.'<hr>').'<div class=\'row\'><u class=\'font-weight-bold px-3\'>'.__('global.note_text').':</u><br><p class=\'px-3\'>'.$attackListItem->note.'</p></div>" data-placement="left"';
                }

                if($attackListItem->note != null && strlen($attackListItem->note) > 0) {
                    $color = "text-danger";
                } else if($unitCount != '' || $speedBoost != '') {
                    $color = "text-info";
                } else {
                    $color = "text-muted";
                }
                return '<h4 class="mb-0"><i class="fas fa-info-circle '.$color.'"'.$popoverData.'></i></h4>';
            })
            ->addColumn('action', function (AttackListItem $attackListItem) use ($attackList) {
                $uv = "";
                if($attackList->uvMode && $attackListItem->start_village__owner !== null) {
                    $uv = "t={$attackListItem->start_village__owner}&";
                }
                $href = "{$attackList->world->url}/game.php?{$uv}village={$attackListItem->start_village_id}&screen=place&target={$attackListItem->target_village_id}";
                $href .= '&spear='.$attackListItem->spear
                    .'&sword='.$attackListItem->sword
                    .'&axe='.$attackListItem->axe
                    .'&archer='.$attackListItem->archer
                    .'&spy='.$attackListItem->spy
                    .'&light='.$attackListItem->light
                    .'&marcher='.$attackListItem->marcher
                    .'&heavy='.$attackListItem->heavy
                    .'&ram='.$attackListItem->ram
                    .'&catapult='.$attackListItem->catapult
                    .'&knight='.$attackListItem->knight
                    .'&snob='.$attackListItem->snob;
                $icon = ($attackListItem->send == 0) ? 'fas fa-play-circle' : 'fas fa-redo';
                $onclick = ($attackListItem->send == 0) ? 'sendattack('.$attackListItem->id.')' : '';
                return '<h4 class="mb-0"><a class="text-success" target="_blank" href="'.$href.'"><i class="'.$icon.'" onclick="'.$onclick.'"></i></a></h4>';
            })
            ->addColumn('delete', function (AttackListItem $attackListItem){
                return '<h4 class="mb-0"><a class="text-primary" onclick="edit('.$attackListItem->id.')" style="cursor: pointer;" data-toggle="modal" data-target=".edit-modal"><i class="fas fa-edit"></i></a>' .
                    '<a class="text-danger" onclick="destroy('.$attackListItem->id.')" style="cursor: pointer;"><i class="fas fa-times"></i></a></h4>';
            })
            ->rawColumns(['type', 'start_village_id', 'target_village_id', 'attacker', 'defender', 'arrival_time', 'slowest_unit', 'time', 'info', 'action', 'delete'])
            ->whitelist($whitelist)
            ->make(true);
    }

    public function destroy(Request $request, AttackListItem $attackListItem)
    {
        $req = $request->validate([
            'key' => 'required|string',
        ]);
        abort_if($attackListItem->list == null, 404);
        if ($attackListItem->list->edit_key === $req['key']){
            $attackListItem->delete();
            return ['success' => true, 'message' => 'destroy !!'];
        }
    }

    public function update(Request $request, AttackListItem $attackListItem){
        $req = $request->validate(static::generateEditValidation());
        $attackplaner = $attackListItem->list;
        abort_if($attackplaner == null, 404);
        abort_unless($req['key'] == $attackplaner->edit_key, 403);
        abort_if($attackplaner->world->maintananceMode, 503);

        $err = [];
        $err = array_merge($err, $attackListItem->setVillageID($req['xStart'], $req['yStart'], $req['xTarget'], $req['yTarget']));
        $attackListItem->type = $req['type'];
        $attackListItem->slowest_unit = $req['slowest_unit'];
        $attackListItem->note = $req['note'] ?? "";
        $attackListItem->support_boost = floatval($req['support_boost']);
        $attackListItem->tribe_skill = floatval($req['tribe_skill']);

        if($req['time_type'] == 0){
            $attackListItem->arrival_time = Carbon::parse($req['day'].' '.$req['time']);
        }else{
            $attackListItem->send_time = Carbon::parse($req['day'].' '.$req['time']);
            if(count($err) == 0) $attackListItem->arrival_time = $attackListItem->calcArrival();
        }
        $ms = explode('.',$req['time']);
        if (count($ms) > 1){
            $attackListItem->ms = $ms[1];
        }else{
            $attackListItem->ms = 0;
        }
        $err = array_merge($err, $attackListItem->setUnitsArr($req, true));

        if(count($err) == 0) {
            $attackListItem->send_time = $attackListItem->calcSend();
            $err = array_merge($err, $attackListItem->verifyTime());
        }

        if(count($err) > 0) {
            return AttackListItem::errJsonReturn($err);
        }

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

    private static function generateEditValidation() {
        return array_merge([
            'key' => 'required|string',
            'xStart' => 'required|integer',
            'yStart' => 'required|integer',
            'xTarget' => 'required|integer',
            'yTarget' => 'required|integer',
            'type' => 'required|integer',
            'slowest_unit' => 'required|integer',
            'note' => 'string|null',
            'support_boost' => 'required|numeric',
            'tribe_skill' => 'required|numeric',

            'time_type' => 'required|integer',
            'day' => 'required|string',
            'time' => 'required|string',
        ], AttackListItem::unitVerifyArray());
    }

    public function multiedit(Request $request) {
        $attackplaner = AttackList::findorfail($request->id);
        abort_unless($request->key == $attackplaner->edit_key, 403);
        abort_if($attackplaner->world->maintananceMode, 503);

        if ($request->items == null || count($request->items) <= 0) {
            return \Response::json(array(
                'data' => 'error',
                'title' => __('tool.attackPlanner.attackCountTitle'),
                'msg' => __('tool.attackPlanner.attackCount'),
            ));
        }

        $err = [];
        foreach ($request->items as $item) {
            $curErr = [];
            $attackListItem = AttackListItem::find(intval($item));
            if($item == null) {
                continue;
            }

            if (isset($request->checkboxes['type'])) {
                $attackListItem->type = intval($request->type);
            }

            if (isset($request->checkboxes['note'])) {
                $attackListItem->note = $request->note;
            }
            if (isset($request->checkboxes['support_boost'])) {
                $attackListItem->support_boost = floatval($request->support_boost);
            }
            if (isset($request->checkboxes['tribe_skill'])) {
                $attackListItem->tribe_skill = floatval($request->tribe_skill);
            }

            if (isset($request->checkboxes['start']) || isset($request->checkboxes['target'])){
                if (isset($request->checkboxes['start'])) {
                    $xStart = intval($request->xStart);
                    $yStart = intval($request->yStart);
                } else {
                    $villageStart = Village::village($attackplaner->world, $attackListItem->start_village_id);
                    $xStart = $villageStart->x;
                    $yStart = $villageStart->y;
                }
                if (isset($request->checkboxes['target'])) {
                    $xTarget = intval($request->xTarget);
                    $yTarget = intval($request->yTarget);
                } else {
                    $villageTarget = Village::village($attackplaner->world, $attackListItem->target_village_id);
                    $xTarget = $villageTarget->x;
                    $yTarget = $villageTarget->y;
                }
                $curErr = array_merge($curErr, $attackListItem->setVillageID($xStart, $yStart, $xTarget, $yTarget));
            }

            if (isset($request->checkboxes['slowest_unit'])) {
                $attackListItem->slowest_unit = intval($request->slowest_unit);
            }

            //default keep arrival_time
            if (isset($request->checkboxes['date'])) {
                if ($request->time_type == 0) {
                    $attackListItem->arrival_time = Carbon::parse($request->day. ' ' .$request->time);
                } else {
                    $attackListItem->send_time = Carbon::parse($request->day . ' ' . $request->time);
                    if(count($curErr) == 0) $attackListItem->arrival_time = $attackListItem->calcArrival();
                }
                $ms = explode('.',$request['time']);
                if (count($ms) > 1){
                    $attackListItem->ms = $ms[1];
                }else{
                    $attackListItem->ms = 0;
                }
            }

            if(count($curErr) == 0) {
                $attackListItem->send_time = $attackListItem->calcSend();
                $curErr = array_merge($curErr, $attackListItem->verifyTime());
            }
            $curErr = array_merge($curErr, $attackListItem->setUnits($request, false));
            if(count($curErr) == 0) {
                $attackListItem->update();
            }

            $err = array_merge($err, $curErr);
        }

        if(count($err) > 0) {
            return AttackListItem::errJsonReturn($err);
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
        abort_if($attackListItem->list === null, 404);
        abort_unless($request->key == $attackListItem->list->show_key, 403);
        $attackListItem->send = 1;
        $attackListItem->update();
    }

    public function massDestroy(Request $request)
    {
        $attackplaner = AttackList::findorfail($request->id);
        abort_unless($request->key == $attackplaner->edit_key, 403);
        abort_if($attackplaner->world->maintananceMode, 503);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:attack_list_items,id',
        ]);
        AttackListItem::whereIn('id', $request->input('ids'))->where('attack_list_id', $request->id)->delete();
    }
}
