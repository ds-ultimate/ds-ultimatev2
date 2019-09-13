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
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;

class AttackPlannerItemController extends BaseController
{
    private $dbName;

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

    public function data(AttackList $attackList, $key){
        abort_unless($attackList->show_key == $key, 403);

        $query = AttackListItem::query()->where('attack_list_id', $attackList->id)->orderBy('send_time');

        return Datatables::of($query)
            ->addColumn('start_village', function (AttackListItem $attackListItem) {
                $village =$attackListItem->start_village;
                return '['.$village->x.'|'.$village->y.'] '.BasicFunctions::decodeName($village->name);
            })
            ->addColumn('target_village', function (AttackListItem $attackListItem) {
                $village =$attackListItem->target_village;
                return '['.$village->x.'|'.$village->y.'] '.BasicFunctions::decodeName($village->name);
            })
            ->editColumn('type', function (AttackListItem $attackListItem) {
                return '<img id="type_img" src="'.Icon::icons($attackListItem->type).'">';
            })
            ->editColumn('slowest_unit', function (AttackListItem $attackListItem) {
                return '<img id="type_img" src="'.Icon::icons($attackListItem->slowest_unit).'">';
            })
            ->addColumn('attacker', function (AttackListItem $attackListItem) {
                if($attackListItem->start_village->owner == 0) return ucfirst(__('ui.player.barbarian'));
                if($attackListItem->start_village->playerLatest == null) return ucfirst(__('ui.player.deleted'));
                return BasicFunctions::decodeName($attackListItem->start_village->playerLatest->name);
            })
            ->addColumn('defender', function (AttackListItem $attackListItem) {
                if($attackListItem->target_village->owner == 0) return ucfirst(__('ui.player.barbarian'));
                if($attackListItem->target_village->playerLatest == null) return ucfirst(__('ui.player.deleted'));
                return BasicFunctions::decodeName($attackListItem->target_village->playerLatest->name);
            })
            ->addColumn('send_time', function (AttackListItem $attackListItem) {
                return $attackListItem->send_time->format('d.m.y H:i:s');
            })
            ->addColumn('arrival_time', function (AttackListItem $attackListItem) {
                return $attackListItem->arrival_time->format('d.m.y H:i:s');
            })
            ->addColumn('time', function (AttackListItem $attackListItem) {
                return $attackListItem->send_time;
            })
            ->addColumn('action', function (AttackListItem $attackListItem){
                return '<h4 class="mb-0"><a class="text-success" target="_blank" href="'.$attackListItem->list->world->url.'/game.php?village='.$attackListItem->start_village_id.'&screen=place&mode=command&target='.$attackListItem->target_village_id.'&type=0&spear=0&sword=0&axe=0&spy=0&light=0&heavy=0&ram=0&catapult=0&knight=0&snob=0"><i class="fas fa-play-circle"></i></a></h4>';
            })
            ->addColumn('delete', function (AttackListItem $attackListItem){
                return '<h4 class="mb-0"><a class="text-danger" onclick="destroy('.$attackListItem->id.',\''.$attackListItem->list->edit_key.'\')"><i class="fas fa-times"></i></a></h4>';
            })
            ->rawColumns(['type', 'slowest_unit', 'action', 'delete'])
            ->make(true);
    }

    public function destroy(AttackListItem $attackListItem)
    {
        if ($attackListItem->list->edit_key === request('key')){
            $attackListItem->delete();
            return ['success' => true, 'message' => 'destroy !!'];
        }
    }

}
