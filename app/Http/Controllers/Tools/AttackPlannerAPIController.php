<?php
/**
 * Created by IntelliJ IDEA.
 * User: crams
 * Date: 10.08.2019
 * Time: 20:38
 */

namespace App\Http\Controllers\Tools;


use App\Tool\AttackPlanner\APIKey;
use App\Tool\AttackPlanner\AttackList;
use App\Tool\AttackPlanner\AttackListItem;
use App\World;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class AttackPlannerAPIController extends BaseController
{
    public function create(Request $request) {
        $apiKeyId = $this->validateAPIKey($request);
        $req = $request->validate(array_merge([
            'title' => '',
            'server' => 'required',
            'world' => 'required',
            'sitterMode' => 'string',
        ], static::itemVerificationArray()));

        $server = $req['server'];
        $world = $req['world'];
        \App\Http\Controllers\API\PictureController::fixWorldName($server, $world);
        $worldData = World::getAndCheckWorld($server, $world);
        abort_if($worldData->maintananceMode, 503);
        abort_if($worldData->config == null || $worldData->units == null, 404, __("ui.errors.404.toolNotAvail.attackPlanner"));
        
        $list = new AttackList();
        $list->world_id = $worldData->id;
        $list->title = $req['title'] ?? "";
        if(isset($req['sitterMode'])) {
            $list->uvMode = filter_var($req['sitterMode'], FILTER_VALIDATE_BOOLEAN);
        }
        $list->edit_key = Str::random(40);
        $list->show_key = Str::random(40);
        $list->api = true;
        $list->apiKey = $apiKeyId;
        $list->save();
        return self::apiInternalCreateItems($req, $list, $apiKeyId);
    }
    
    public function itemCreate(Request $request) {
        $apiKeyId = $this->validateAPIKey($request);
        $req = $request->validate(array_merge([
            'id' => 'required',
            'edit_key' => 'required',
        ], static::itemVerificationArray()));

        $list = AttackList::findOrFail($req['id']);
        abort_unless($list->edit_key == $req['edit_key'], 403);
        abort_if($list->world->maintananceMode, 503);
        return self::apiInternalCreateItems($req, $list, $apiKeyId);
    }
    
    private static function itemVerificationArray() {
        $troopVerify = [];
        foreach(AttackListItem::$units as $unit) {
            $troopVerify["items.*.$unit"] = 'integer';
        }
        return array_merge([
            'items' => 'array|max:1050',
            'items.*' => 'array',
            'items.*.source' => 'required|integer',
            'items.*.destination' => 'required|integer',
            'items.*.type' => 'required|integer',
            'items.*.slowest_unit' => 'required|integer|between:0,' . (count(AttackListItem::$units)-1),
            'items.*.support_boost' => 'numeric',
            'items.*.tribe_skill' => 'numeric',
            'items.*.arrival_time' => 'required|integer',
            'items.*.ms' => 'integer',
        ], $troopVerify);
    }
    
    private static function apiInternalCreateItems($req, AttackList $list, $apiKeyId) {
        $path = storage_path("customLog");
        if(!file_exists($path)) mkdir($path, 0777, true);
        $target = $path . "/attack_plan_api.log";
        $logData = Carbon::now()->format('Y-m-d H:i:s') . ";" . $apiKeyId . ";" . count($req["items"]);
        file_put_contents($target, $logData . "\n", FILE_APPEND | LOCK_EX);

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
                $it['arrival_time'], (in_array($it['type'], AttackListItem::attackPlannerTypeIcons()))?$it['type']: -1, $it,
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
        $this->validateAPIKey($request);

        $req = $request->validate([
            'id' => 'required|integer',
            'edit_key' => 'required',
        ]);

        $list = AttackList::findOrFail($req['id']);
        abort_unless($list->edit_key == $req['edit_key'], 403);
        return \Response::json(array_merge(AttackPlannerController::destroyOutdated($list) ,[
            'id' => $list->id,
        ]));
    }

    public function clear(Request $request) {
        $this->validateAPIKey($request);

        $req = $request->validate([
            'id' => 'required|integer',
            'edit_key' => 'required',
        ]);

        $list = AttackList::findOrFail($req['id']);
        abort_unless($list->edit_key == $req['edit_key'], 403);
        return \Response::json(array_merge(AttackPlannerController::clear($list) ,[
            'id' => $list->id,
        ]));
    }

    public function fetchItems(Request $request) {
        $apiKeyId = $this->validateAPIKey($request);

        $req = $request->validate([
            'id' => 'required|integer',
            'edit_key' => 'required_without:show_key',
            'show_key' => 'required_without:edit_key',
            'length' => 'integer|min:1',
            'start' => 'integer|min:0',
        ]);

        $list = AttackList::findOrFail($req['id']);
        abort_if($list->world->maintananceMode, 503);
        
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
        
        $selectCols = array_merge([
            'id',
            'start_village_id',
            'target_village_id',
            'slowest_unit',
            'send_time',
            'arrival_time',
            'type',
            'support_boost',
            'tribe_skill',
            'ms',
            'send'
        ], AttackListItem::$units);
        $items = $list->items()->select($selectCols);
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

    private function validateAPIKey(Request $request) {
        $reqKey = $request->validate([
            'API_KEY' => 'required',
        ]);

        $apiKey = APIKey::where('key', $reqKey["API_KEY"])->first();

        // Optional handling
        if ($apiKey == null) {
            abort(403, 'Invalid API key');
        }

        return $apiKey->id;
    }
}
