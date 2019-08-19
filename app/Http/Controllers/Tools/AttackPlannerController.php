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
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class AttackPlannerController extends BaseController
{

    public function index($server, $world){
        BasicFunctions::local();
        World::existWorld($server, $world);

        $worldData = World::getWorld($server, $world);

        $unitConfig = simplexml_load_string($worldData->units);
        $config = simplexml_load_string($worldData->config);
        $now = Carbon::createFromTimestamp(time());

        $list = new AttackList();
        $list->world_id = $worldData->id;
        if (\Auth::user()){
            $list->user_id = \Auth::user()->id;
        }
        $list->create_key = base64_encode(Hash::make('create'.Carbon::createFromTimestamp(time())));
        $list->show_key = base64_encode(Hash::make('show'.Carbon::createFromTimestamp(time())));
        $list->save();

        return redirect()->route('attackPlannerMode', [$server, $world, $list->id, 'create', $list->create_key]);

    }

    public function mode($server, $world, AttackList $attackList, $mode, $key){
        $worldData = World::getWorld($server, $world);
        $listWorld = $attackList->world;

        switch ($mode){
            case 'create':
                if ($attackList->create_key == $key){
                    if ($listWorld->is($worldData)){
                        return $this->create($attackList);
                    }else{
                        return redirect()->route('attackPlannerMode',[$listWorld->server->code, $listWorld->name, $attackList->id, $mode, $key]);
                    }
                }
                return redirect()->route('index');
            case 'show':
                if ($attackList->show_key == $key){
                    if ($listWorld->is($worldData)){
                        return $this->show($attackList);
                    }else{
                        return redirect()->route('attackPlannerMode',[$listWorld->server->code, $listWorld->name, $attackList->id, $mode, $key]);
                    }
                }
                return redirect()->route('index');
        }
    }

    public function create(AttackList $attackList){
        BasicFunctions::local();
        $worldData = $attackList->world;

        $unitConfig = simplexml_load_string($worldData->units);
        $config = simplexml_load_string($worldData->config);
        $mode = 'create';
        $now = Carbon::createFromTimestamp(time());

        return view('tools.attackPlanner', compact('worldData', 'unitConfig', 'config', 'attackList', 'mode', 'now'));
    }

    public function show(AttackList $attackList){
        BasicFunctions::local();
        $worldData = $attackList->world;

        $unitConfig = simplexml_load_string($worldData->units);
        $config = simplexml_load_string($worldData->config);
        $mode = 'show';
        $now = Carbon::createFromTimestamp(time());

        return view('tools.attackPlanner', compact('worldData', 'unitConfig', 'config', 'attackList', 'mode', 'now'));
    }

}
