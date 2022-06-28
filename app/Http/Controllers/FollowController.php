<?php

namespace App\Http\Controllers;

use App\Follow;
use App\World;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function createFollowTool(Request $request){

        $model = self::getModel($request->get('model'));
        $class = 'App\Tool\\'.$model;
        $item = $class::find($request->get('id'));

        $follow = $item->follows()->where('user_id', \Auth::user()->id)->first();

        $modeltype = explode('\\', $model);
        $function = 'follow'.$modeltype[1];

        if ($follow  == null){
            \Auth::user()->$function()->save($item);
        }else{
            $follow->$function()->detach($item->id);
        }
    }

    public function createFollow(Request $request){
        abort_unless(\Gate::allows('discord_bot_beta'), 403);
        $model = self::getModel($request->get('model'));
        $world = World::find($request->get('world'));
        $class = 'App\\'.$model;
        $classFunktion = lcfirst($request->get('model'));
        $item = $class::$classFunktion($world, $request->get('id'));

        $follow = $item->follows()->where(['user_id' => \Auth::user()->id, 'world_id' => $world->id])->first();

        $function = 'follow'.$model;

        if ($follow  == null){
            \Auth::user()->$function()->save($item);
            $followItem = Follow::where(['user_id' => \Auth::user()->id,'followable_id' => $request->get('id'), 'followable_type' => $class, 'world_id' => null])->first();
            $followItem->world_id = $world->id;
            $followItem->save();
        }else{
            $follow->$function()->where('world_id', $world->id)->detach($item->id);
        }
    }

    public static function getModel($model){
        return str_replace('_', '\\', $model);
    }
}
