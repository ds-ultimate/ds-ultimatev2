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
use App\User;
use App\Util\BasicFunctions;
use App\World;
use http\Url;
use App\Http\Requests\StoreAttackPlannerItemRequest;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class AttackPlannerItemController extends BaseController
{

    public function store(StoreAttackPlannerItemRequest $request)
    {
        $item = new AttackListItem();
        $item->attack_list_id = $request->get('attack_list_id');
        $item->type = $request->get('type');
        $item->start_village_id = $request->get('start_village_id');
        $item->target_village_id = $request->get('target_village_id');
        $item->slowest_unit = $request->get('slowest_unit');
        $item->note = $request->get('note');
        $item->send_time = Carbon::createFromTimestamp($request->get('send_time')/1000);
        $item->arrival_time = Carbon::createFromTimestamp($request->get('arrival_time')/1000);
        $item->save();
    }

    public function data(AttackList $attackList){
        $items = $attackList->items;

        return Datatables::collection($items)
            ->addColumn('attacker', function (AttackListItem $attackListItem) {
                return 'attacker';
            })
            ->addColumn('defender', function (AttackListItem $attackListItem) {
                return 'defender';
            })
            ->addColumn('time', function (AttackListItem $attackListItem) {
                return $attackListItem->send_time;
            })
            ->toJson();
    }

}
