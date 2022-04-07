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
use App\Util\Icon;
use App\World;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class AttackPlannerAPIController extends BaseController
{
    public function apiCreate(Request $request) {
        $req = $request->validate([
            'title' => '',
            'server' => 'required',
            'world' => 'required',
            'sitterMode' => 'string',
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
        if(isset($req['sitterMode'])) {
            $list->uvMode = $req['sitterMode'] == true;
        }
        $list->edit_key = Str::random(40);
        $list->show_key = Str::random(40);
        $list->api = true;
        $list->save();
        return self::apiInternalCreateItems($request);
    }
    
    public function apiItemCreate(Request $request) {
        $req = $request->validate([
            'id' => 'required',
            'edit_key' => 'required',
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
        
        $list = AttackList::find($req['id']);
        abort_if($list == null, 404);
        abort_unless($list->edit_key == $req['edit_key'], 403);
        return self::apiInternalCreateItems($request);
    }
    
    private static function apiInternalCreateItems($req, AttackList $list) {
        $err = [];
        $all = [];
        foreach($req['items'] as $it) {
            $it = AttackPlannerController::newItem($err, $list, $it['source'], $it['destination'], $it['slowest_unit'],
                $it['arrival_time'], (in_array($it['type'], Icon::attackPlannerTypeIcons()))?$it['type']: -1, null);
            
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
            'id' => $list->id,
            'edit' => route('tools.attackPlannerMode', [$list->id, "edit", $list->edit_key]),
            'show' => route('tools.attackPlannerMode', [$list->id, "show", $list->show_key]),
            'edit_key' => $list->edit_key,
            'show_key' => $list->show_key,
            'errors' => $err,
        ));
    }

    public function destroyOutdated(Request $request) {
        $req = $request->validate([
            'id' => 'required|integer',
            'edit_key' => 'required',
            'API_KEY' => 'required',
        ]);
        
        if(!in_array($req['API_KEY'], explode(";", config("app.API_KEYS")))) {
            abort(403);
        }
        $list = AttackList::find($req['id']);
        abort_if($list == null, 404);
        abort_unless($list->edit_key == $req['edit_key'], 403);
        return \Response::json(array_merge(AttackPlannerController::destroyOutdated($list) ,[
            'id' => $list->id,
        ]));
    }

    public function clear(Request $request) {
        $req = $request->validate([
            'id' => 'required|integer',
            'edit_key' => 'required',
            'API_KEY' => 'required',
        ]);
        
        if(!in_array($req['API_KEY'], explode(";", config("app.API_KEYS")))) {
            abort(403);
        }
        $list = AttackList::find($req['id']);
        abort_if($list == null, 404);
        abort_unless($list->edit_key == $req['edit_key'], 403);
        return \Response::json(array_merge(AttackPlannerController::clear($list) ,[
            'id' => $list->id,
        ]));
    }
}
