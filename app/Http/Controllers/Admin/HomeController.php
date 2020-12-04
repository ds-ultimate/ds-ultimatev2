<?php

namespace App\Http\Controllers\Admin;

use App\Tool\AccMgrDB\AccountManagerTemplate;
use App\Tool\AttackPlanner\AttackList;
use App\Tool\AttackPlanner\AttackListItem;
use App\Tool\Map\Map;
use App\User;

class HomeController
{
    public function index()
    {
        $users = User::all();
        $counter['maps'] = (new Map())->count();
        $counter['attackplaner'] = (new AttackList())->count();
        $counter['attacks'] = (new AttackListItem())->count();
        $counter['accMgrDB'] = (new AccountManagerTemplate())->count();
        $counter['users'] = $users->count();
        $twitter = 0;
        $facebook = 0;
        $google = 0;
        $github = 0;
        $discord = 0;

        foreach ($users as $user){
            if(isset($user->profile->twitter_id)) $twitter++;
            if(isset($user->profile->facebook_id)) $facebook++;
            if(isset($user->profile->google_id)) $google++;
            if(isset($user->profile->github_id)) $github++;
            if(isset($user->profile->discord_id)) $discord++;
        }

        $counter['twitter'] = $twitter;
        $counter['facebook'] = $facebook;
        $counter['google'] = $google;
        $counter['github'] = $github;
        $counter['discord'] = $discord;

        return view('admin.home', compact('counter'));
    }
}
