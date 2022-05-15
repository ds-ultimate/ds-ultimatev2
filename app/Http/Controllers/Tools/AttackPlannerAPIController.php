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
    public function create(Request $request) {
        $req = $request->validate(array_merge([
            'title' => '',
            'server' => 'required',
            'world' => 'required',
            'sitterMode' => 'string',
            'API_KEY' => 'required',
        ], static::itemVerificationArray()));
        
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
            $list->uvMode = filter_var($req['sitterMode'], FILTER_VALIDATE_BOOLEAN);
        }
        $list->edit_key = Str::random(40);
        $list->show_key = Str::random(40);
        $list->api = true;
        $list->save();
        return self::apiInternalCreateItems($req, $list);
    }
    
    public function itemCreate(Request $request) {
        $req = $request->validate(array_merge([
            'id' => 'required',
            'edit_key' => 'required',
            'API_KEY' => 'required',
        ], static::itemVerificationArray()));
        
        if(!in_array($req['API_KEY'], explode(";", config("app.API_KEYS")))) {
            abort(403);
        }
        
        $list = AttackList::findOrFail($req['id']);
        abort_unless($list->edit_key == $req['edit_key'], 403);
        return self::apiInternalCreateItems($req, $list);
    }
    
    private static function itemVerificationArray() {
        return [
            'items' => 'array',
            'items.*' => 'array',
            'items.*.source' => 'required|integer',
            'items.*.destination' => 'required|integer',
            'items.*.type' => 'required|integer',
            'items.*.slowest_unit' => 'required|integer|between:0,' . (count(AttackListItem::$units)-1),
            'items.*.support_boost' => 'numeric',
            'items.*.tribe_skill' => 'numeric',
            'items.*.arrival_time' => 'required|integer',
            'items.*.ms' => 'integer',
        ];
    }
    
    private static function apiInternalCreateItems($req, AttackList $list) {
        if(! isset($req['items'])) {
            return \Response::json(array(
                'id' => $list->id,
                'edit' => route('tools.attackPlannerMode', [$list->id, "edit", $list->edit_key]),
                'show' => route('tools.attackPlannerMode', [$list->id, "show", $list->show_key]),
                'edit_key' => $list->edit_key,
                'show_key' => $list->show_key,
                'errors' => ['items array is empty'],
            ));
        }
        
        $err = [];
        $all = [];
        foreach($req['items'] as $it) {
            $it = AttackPlannerController::newItem($err, $list, $it['source'], $it['destination'], $it['slowest_unit'],
                $it['arrival_time'], (in_array($it['type'], Icon::attackPlannerTypeIcons()))?$it['type']: -1, $it,
                $it['support_boost']??0.0, $it['tribe_skill']??0.0,$it['ms']??0);
            
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
        $list = AttackList::findOrFail($req['id']);
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
        $list = AttackList::findOrFail($req['id']);
        abort_unless($list->edit_key == $req['edit_key'], 403);
        return \Response::json(array_merge(AttackPlannerController::clear($list) ,[
            'id' => $list->id,
        ]));
    }

    public function fetchItems(Request $request) {
        $req = $request->validate([
            'id' => 'required|integer',
            'edit_key' => 'required_without:show_key',
            'show_key' => 'required_without:edit_key',
            'API_KEY' => 'required',
            'length' => 'integer',
            'start' => 'integer',
        ]);
        
        if(!in_array($req['API_KEY'], explode(";", config("app.API_KEYS")))) {
            abort(403);
        }
        $list = AttackList::findOrFail($req['id']);
        
        if(isset($req['edit_key'])) {
            abort_unless($list->edit_key == $req['edit_key'], 403, "Edit key wrong");
            $result = [
                'id' => $list->id,
                'sitterMode' => $list->uvMode,
                'title' => $list->title,
                'edit' => route('tools.attackPlannerMode', [$list->id, "edit", $list->edit_key]),
                'show' => route('tools.attackPlannerMode', [$list->id, "show", $list->show_key]),
                'edit_key' => $list->edit_key,
                'show_key' => $list->show_key,
            ];
        } else if(isset($req['show_key'])) {
            abort_unless($list->show_key == $req['show_key'], 403, "Show key wrong");
            $result = [
                'id' => $list->id,
                'sitterMode' => $list->uvMode,
                'title' => $list->title,
                'show' => route('tools.attackPlannerMode', [$list->id, "show", $list->show_key]),
                'show_key' => $list->show_key,
            ];
        } else {
            abort(404, "Edit and show key are not present");
        }
        
        $items = $list->items();
        if(isset($req['start'])) {
            if($req['start'] < 0) abort(422, "Start must be at least 0");
            $items = $items->skip($req['start']);
        }
        if(isset($req['length'])) {
            if($req['length'] < 0) abort(422, "Length must be at least 0");
            $items = $items->take($req['length']);
        }
        $items = $items->get();
        $result['items'] = [];
        
        foreach($items as $it) {
            $r = [
                'id' => $it->id,
                'source' => $it->start_village_id,
                'destination' => $it->target_village_id,
                'slowest_unit' => $it->slowest_unit,
                'send_time' => $it->send_time->timestamp,
                'arrival_time' => $it->arrival_time->timestamp,
                'type' => $it->type,
                'support_boost' => $it->support_boost,
                'tribe_skill' => $it->tribe_skill,
                'ms' => $it->ms,
                'send' => $it->send,
            ];
            foreach(AttackListItem::$units as $u) {
                $r[$u] = $it->{$u};
            }
            $result['items'][] = $r;
        }
        
        return \Response::json($result);
    }
}
