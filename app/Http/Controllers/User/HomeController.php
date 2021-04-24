<?php

namespace App\Http\Controllers\User;

use App\DsConnection;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Tools\MapController;
use App\Tool\AttackPlanner\AttackList;
use App\Tool\Map\Map;
use App\Tool\AnimHistMap\AnimHistMapMap;
use App\Tool\AnimHistMap\AnimHistMapJob;

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
    public function overview($page){
        $maps = Map::where('user_id', \Auth::user()->id)->orderBy('world_id')->get();
        $attackLists = AttackList::where('user_id', \Auth::user()->id)->orderBy('world_id')->get();
        $attackListsFollow = \Auth::user()->followAttackList()->get();
        $mapsFollow = \Auth::user()->followMap()->get();
        $animatedMaps = AnimHistMapMap::where('user_id', \Auth::user()->id)->orderBy('world_id')->get();
        $renderedAnimatedMaps = AnimHistMapJob::where('user_id', \Auth::user()->id)->orderBy('world_id')->get();
        
        return view('user.overview', compact('page', 'maps', 'attackLists', 'attackListsFollow', 'mapsFollow', 'animatedMaps', 'renderedAnimatedMaps'));
    }

    public function settings($page){
        $connections = DsConnection::where('user_id', \Auth::user()->id);
        $mapDimensions = MapController::getMapDimension(\Auth::user()->profile->getDimensions());
        
        return view('user.settings', compact('page', 'connections', 'mapDimensions'));
    }
}
