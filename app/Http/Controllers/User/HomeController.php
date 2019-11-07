<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Tool\AttackPlanner\AttackList;
use App\Tool\Map\Map;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('verified');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function overview()
    {

        $maps = Map::where('user_id', \Auth::user()->id)->orderBy('world_id')->get();
        $attackLists = AttackList::where('user_id', \Auth::user()->id)->orderBy('world_id')->get();
        $attackListsFollow = \Auth::user()->followAttackPlanner()->get();
        $mapsFollow = \Auth::user()->followMap()->get();

        return view('user.overview', compact('maps', 'attackLists', 'attackListsFollow', 'mapsFollow'));
    }
}
