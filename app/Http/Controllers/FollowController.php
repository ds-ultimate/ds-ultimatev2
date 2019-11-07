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

        $modeltype = explode('\\', $model);
        $function = 'follow'.$modeltype[1];

        if ($follow  == null){
            \Auth::user()->$function()->save($item);
        }else{
            $follow->$function()->detach($item->id);
        }



    }
}
