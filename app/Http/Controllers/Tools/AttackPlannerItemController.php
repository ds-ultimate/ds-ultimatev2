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
use App\Util\Icon;
use App\World;
use http\Url;
use App\Http\Requests\StoreAttackPlannerItemRequest;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
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

    public function data(AttackList $attackList){
        $items = $attackList->items;
        $world = $attackList->world;
        $this->dbName = BasicFunctions::getDatabaseName($world->server->code, $world->name);

        return Datatables::collection($items)
            ->editColumn('start_village_id', function (AttackListItem $attackListItem) {
                $village =$attackListItem->start_village;
                return '['.$village->x.'|'.$village->y.'] '.BasicFunctions::outputName($village->name);
            })
            ->editColumn('target_village_id', function (AttackListItem $attackListItem) {
                $village =$attackListItem->target_village;
                return '['.$village->x.'|'.$village->y.'] '.BasicFunctions::outputName($village->name);
            })
            ->editColumn('type', function (AttackListItem $attackListItem) {
                return self::type_img($attackListItem->type);
            })
            ->editColumn('slowest_unit', function (AttackListItem $attackListItem) {
                return '<img id="type_img" src="'.Icon::icons($attackListItem->slowest_unit).'">';
            })
            ->addColumn('attacker', function (AttackListItem $attackListItem) {
                return BasicFunctions::outputName($attackListItem->start_village->playerLatest->name);
            })
            ->addColumn('defender', function (AttackListItem $attackListItem) {
                return BasicFunctions::outputName($attackListItem->target_village->playerLatest->name);
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
            ->rawColumns(['type', 'slowest_unit'])
            ->toJson();
    }

    public static function type_img($type){
        switch ($type){
            case 0:
                return '<img id="type_img" src="'.asset('images/ds_images/unit/unit_ram.png').'">';
            case 1:
                return '<img id="type_img" src="'.asset('images/ds_images/unit/unit_snob.png').'">';
            case 2:
                return '<img id="type_img" src="'.asset('images/ds_images/unit/fake.png').'">';
            case 3:
                return '<img id="type_img" src="'.asset('images/ds_images/unit/wall.png').'">';
            case 4:
                return '<img id="type_img" src="'.asset('images/ds_images/unit/unit_spear.png').'">';
            case 5:
                return '<img id="type_img" src="'.asset('images/ds_images/unit/unit_sword.png').'">';
            case 6:
                return '<img id="type_img" src="'.asset('images/ds_images/unit/unit_heavy.png').'">';
            case 7:
                return '<img id="type_img" src="'.asset('images/ds_images/unit/def_fake.png').'">';
        }
    }

}
