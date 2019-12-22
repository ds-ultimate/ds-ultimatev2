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
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;

class AttackPlannerItemController extends BaseController
{
    public function store(Request $request)
    {
        $attackplaner = AttackList::findOrFail($request->attack_list_id);
        abort_unless($request->key == $attackplaner->edit_key, 403);
        $units = ['spear', 'sword', 'axe', 'archer', 'spy', 'light', 'marcher', 'heavy', 'ram', 'catapult', 'knight', 'snob'];
        foreach ($units as $unit){
            if ($request->get($unit) == null){
                $$unit = 0;
            }else{
                $$unit = $request->get($unit);
            }
        }

        $item = new AttackListItem();
        $item->attack_list_id = $request->attack_list_id;
        $item->type = $request->type;
        $item->setVillageID($request->xStart, $request->yStart, $request->xTarget, $request->yTarget);
        $item->slowest_unit = $request->slowest_unit;
        $item->note = $request->note;
        $item->arrival_time = Carbon::parse($request->day.' '.$request->time);
        $item->send_time = $item->calcSend();
        $item->spear = $spear;
        $item->sword = $sword;
        $item->axe = $axe;
        $item->archer = $archer;
        $item->spy = $spy;
        $item->light = $light;
        $item->marcher = $marcher;
        $item->heavy = $heavy;
        $item->ram = $ram;
        $item->catapult = $catapult;
        $item->knight = $knight;
        $item->snob = $snob;
        $item->save();
    }

    public function data(AttackList $attackList, $key){
        abort_unless($attackList->show_key == $key, 403);

        $query = AttackListItem::query()->where('attack_list_id', $attackList->id)->orderBy('send_time');

        return Datatables::of($query)
            ->setRowId(function (AttackListItem $attackListItem) {
                return $attackListItem->id;
            })
            ->setRowData([
                'xStart' => function(AttackListItem $attackListItem) {
                    return $attackListItem->start_village->x;
                },
                'yStart' => function(AttackListItem $attackListItem) {
                    return $attackListItem->start_village->y;
                },
                'xTarget' => function(AttackListItem $attackListItem) {
                    return $attackListItem->target_village->x;
                },
                'yTarget' => function(AttackListItem $attackListItem) {
                    return $attackListItem->target_village->y;
                },
                'day' => function(AttackListItem $attackListItem) {
                    return $attackListItem->arrival_time->format('Y-m-d');
                },
                'time' => function(AttackListItem $attackListItem) {
                    return $attackListItem->arrival_time->format('H:i:s');
                },
                'type' => function(AttackListItem $attackListItem) {
                    return $attackListItem->type;
                },
                'slowest_unit' => function(AttackListItem $attackListItem) {
                    return $attackListItem->slowest_unit;
                },
            ])
            ->addColumn('start_village', function (AttackListItem $attackListItem) {
                $village =$attackListItem->start_village;
                return '['.$village->x.'|'.$village->y.'] '.BasicFunctions::decodeName($village->name);
            })
            ->addColumn('target_village', function (AttackListItem $attackListItem) {
                $village =$attackListItem->target_village;
                return '['.$village->x.'|'.$village->y.'] '.BasicFunctions::decodeName($village->name);
            })
            ->editColumn('type', function (AttackListItem $attackListItem) {
                return '<img id="type_img" src="'.Icon::icons($attackListItem->type).'" data-toggle="popover" data-trigger="hover" data-content="'.$attackListItem->typeIDToName().'">';
            })
            ->editColumn('slowest_unit', function (AttackListItem $attackListItem) {
                return '<img id="type_img" src="'.Icon::icons($attackListItem->slowest_unit).'" data-toggle="popover" data-trigger="hover" data-content="'.$attackListItem->unitIDToNameOutput().'">';
            })
            ->addColumn('attacker', function (AttackListItem $attackListItem) {
                return $attackListItem->attackerName();
            })
            ->addColumn('defender', function (AttackListItem $attackListItem) {
                return $attackListItem->defenderName();
            })
            ->addColumn('send_time', function (AttackListItem $attackListItem) {
                return $attackListItem->send_time->format('d.m.Y H:i:s');
            })
            ->addColumn('arrival_time', function (AttackListItem $attackListItem) {
                return $attackListItem->arrival_time->format('d.m.Y H:i:s');
            })
            ->addColumn('time', function (AttackListItem $attackListItem) {
                return $attackListItem->send_time;
            })
            ->addColumn('info', function (AttackListItem $attackListItem){
                $units = ['spear', 'sword', 'axe', 'archer', 'spy', 'light', 'marcher', 'heavy', 'ram', 'catapult', 'knight', 'snob'];
                $unitCount = '';
                foreach ($units as $unit){
                    if ($attackListItem->$unit != 0){
                        $unitCount .= "<img class='pr-3' src='".asset('images/ds_images/unit/'.$unit.'.png')."' height='15px'> <b>".BasicFunctions::numberConv($attackListItem->$unit)."</b>".(($unit != 'snob')? '<br>':'');
                    }
                }
                return '<h4 class="mb-0"><i class="fas fa-info-circle text-info" data-toggle="popover" title="'.__('ui.tabletitel.info').'" data-trigger="hover" data-content="'.(($unitCount == '')?'':$unitCount.'<hr>').'<div class=\'row\'><u class=\'font-weight-bold px-3\'>'.__('global.note_text').':</u><br><p class=\'px-3\'>'.$attackListItem->note.'</p></div>" data-placement="left"></i></h4>';
            })
            ->addColumn('action', function (AttackListItem $attackListItem){
                return '<h4 class="mb-0"><a class="text-success" target="_blank" href="'.$attackListItem->list->world->url.'/game.php?village='.$attackListItem->start_village_id.'&screen=place&mode=command&target='.$attackListItem->target_village_id.'&type=0&spear='.$attackListItem->spear.'&sword='.$attackListItem->sword.'&axe='.$attackListItem->axe.'&archer='.$attackListItem->archer.'&spy='.$attackListItem->spy.'&light='.$attackListItem->light.'&marcher='.$attackListItem->marcher.'&heavy='.$attackListItem->heavy.'&ram='.$attackListItem->ram.'&catapult='.$attackListItem->catapult.'&knight='.$attackListItem->knight.'&snob='.$attackListItem->snob.'"><i class="fas fa-play-circle"></i></a></h4>';
            })
            ->addColumn('delete', function (AttackListItem $attackListItem){
                return '<h4 class="mb-0"><a class="text-primary" onclick="edit('.$attackListItem->id.')" style="cursor: pointer;" data-toggle="modal" data-target=".bd-example-modal-xl"><i class="fas fa-edit"></i></a><a class="text-danger" onclick="destroy('.$attackListItem->id.',\''.$attackListItem->list->edit_key.'\')" style="cursor: pointer;"><i class="fas fa-times"></i></a></h4>';
            })
            ->rawColumns(['type', 'arrival_time', 'slowest_unit', 'info', 'action', 'delete'])
            ->make(true);
    }

    public function destroy(AttackListItem $attackListItem)
    {
        if ($attackListItem->list->edit_key === request('key')){
            $attackListItem->delete();
            return ['success' => true, 'message' => 'destroy !!'];
        }
    }

    public function update(Request $request, AttackListItem $attackListItem){
        $attackplaner = $attackListItem->list;
        abort_unless($request->key == $attackplaner->edit_key, 403);
        $units = ['spear', 'sword', 'axe', 'archer', 'spy', 'light', 'marcher', 'heavy', 'ram', 'catapult', 'knight', 'snob'];
        foreach ($units as $unit){
            if ($request->get($unit) == null){
                $$unit = 0;
            }else{
                $$unit = $request->get($unit);
            }
        }

        $attackListItem->type = $request->type;
        $attackListItem->setVillageID($request->xStart, $request->yStart, $request->xTarget, $request->yTarget);
        $attackListItem->slowest_unit = $request->slowest_unit;
        $attackListItem->note = $request->note;
        $attackListItem->arrival_time = Carbon::parse($request->day.' '.$request->time);
        $attackListItem->send_time = $attackListItem->calcSend();
        $attackListItem->spear = $spear;
        $attackListItem->sword = $sword;
        $attackListItem->axe = $axe;
        $attackListItem->archer = $archer;
        $attackListItem->spy = $spy;
        $attackListItem->light = $light;
        $attackListItem->marcher = $marcher;
        $attackListItem->heavy = $heavy;
        $attackListItem->ram = $ram;
        $attackListItem->catapult = $catapult;
        $attackListItem->knight = $knight;
        $attackListItem->snob = $snob;
        $attackListItem->update();
    }

}
