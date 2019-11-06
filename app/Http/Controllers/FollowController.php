<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function createFollowTool(Request $request){

        $model = str_replace('_', '\\', $request->get('model'));
        $class = 'App\Tool\\'.$model;
        $item = $class::find($request->get('id'));

        $follow = $item->follows()->where('user_id', \Auth::user()->id)->first();

        if ($follow  == null){
            \Auth::user()->followAttackPlanner()->save($item);
        }else{
            $follow->followAttackPlanner()->detach($item->id);
        }



    }
}
