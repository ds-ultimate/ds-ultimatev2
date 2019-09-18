<?php

namespace App\Http\Controllers\Admin;

use App\Tool\AttackPlanner\AttackList;
use App\Tool\AttackPlanner\AttackListItem;
use App\Tool\Map\Map;

class HomeController
{
    public function index()
    {

        $counter['maps'] = Map::all()->count();
        $counter['attackplaner'] = AttackList::all()->count();
        $counter['attacks'] = AttackListItem::all()->count();

        return view('admin.home', compact('counter'));
    }
}
