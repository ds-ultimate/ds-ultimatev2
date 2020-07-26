<?php
namespace App\Util;

use App\World;

class Navigation
{
    public static function generateNavArray($serverArg, $worldArg) {
        $retArray = [];
        
        if($serverArg !== null) {
            $serverNav = [];
            foreach(World::worldsCollection($serverArg) as $worlds) {
                $worldNav = [];
                foreach($worlds as $world) {
                    $worldNav[] = self::navElement($world->displayName(), 'world', [$world->server->code, $world->name], false);
                }
                if($worlds->get(0)->sortType() == "world") {
                    $serverNav[] = self::navDropdown('ui.tabletitel.normalWorlds', $worldNav);
                } else {
                    $serverNav[] = self::navDropdown('ui.tabletitel.specialWorlds', $worldNav);
                }
            }
            $retArray[] = self::navElement('ui.titel.worldOverview', 'server', [$serverArg]);
            $retArray[] = self::navDropdown('ui.server.worlds', $serverNav);
        }
        
        if($worldArg !== null) {
            $serverCodeName = [$worldArg->server->code, $worldArg->name];
            $retArray[] = self::navDropdown('ui.server.ranking', [
                self::navElement('ui.tabletitel.top10', 'world', $serverCodeName),
                self::navElement(ucfirst(__('ui.table.player')) . " (" . __('ui.nav.current') . ")", 'worldPlayer', $serverCodeName, false),
                self::navElement(ucfirst(__('ui.table.player')) . " (" . __('ui.nav.history') . ")", 'rankPlayer', $serverCodeName, false),
                self::navElement(ucfirst(__('ui.table.ally')) . " (" . __('ui.nav.current') . ")", 'worldAlly', $serverCodeName, false),
                self::navElement(ucfirst(__('ui.table.ally')) . " (" . __('ui.nav.history') . ")", 'rankAlly', $serverCodeName, false),
            ]);
            $retArray[] = self::navElement('ui.conquer.all', 'worldConquer', [$worldArg->server->code, $worldArg->name, 'all']);
            $tools = [];
            $tools[] = self::navElement('tool.distCalc.title', 'tools.distanceCalc', $serverCodeName);
            if($worldArg->config != null && $worldArg->units != null) {
                $tools[] = self::navElement('tool.attackPlanner.title', 'tools.attackPlannerNew', $serverCodeName);
            }
            if($worldArg->units != null) {
                $tools[] = self::navElement('tool.map.title', 'tools.mapNew', $serverCodeName);
            }
            if($worldArg->config != null && $worldArg->buildings != null) {
                $tools[] = self::navElement('tool.pointCalc.title', 'tools.pointCalc', $serverCodeName);
            }
            $tools[] = self::navElement('tool.tableGenerator.title', 'tools.tableGenerator', $serverCodeName);
            $retArray[] = self::navDropdown('ui.server.tools', $tools);
        }
        
        return $retArray;
    }
    
    public static function generateMobileNavArray($serverArg, $worldArg) {
        $navArray = self::generateNavArray($serverArg, $worldArg);
        $navArray[] = self::navDropdown('ui.language', [
            self::navElement('Deutsch', 'locale', ['de'], false, 'flag-icon flag-icon-de'),
            self::navElement('English', 'locale', ['en'], false, 'flag-icon flag-icon-gb'),
        ]);
        if(\Auth::check()) {
            $userOpt = [];
            $userOpt[] = self::navElement('ui.titel.overview', 'user.overview', ['myMap']);
            if(\Gate::allows('dashboard_access')) {
                $userOpt[] = self::navElement('user.dashboard', 'admin.home');
            }
            if(\Gate::allows('translation_access')) {
            //    $userOpt[] = self::navElement('user.translations', 'translations');
            }
            $userOpt[] = self::navElement('ui.personalSettings.title', 'user.settings', ['settings-profile']);
            $userOpt[] = self::navElement('user.logout', 'logout');
            
            $navArray[] = self::navDropdown(\Auth::user()->name , $userOpt, false);
        } else {
            $navArray[] = self::navElement('user.login', 'login');
            $navArray[] = self::navElement('user.register', 'register');
        }
        return $navArray;
    }
    
    public static function navElement($title, $route, $routeArgs=null, $translated=true, $icon=null) {
        $id = str_replace(".", "", $title);
        if($translated) {
            $title = ucfirst(__($title));
        }
        return [
            'id' => $id,
            'title' => $title,
            'link' => route($route, $routeArgs),
            'subElements' => null,
            'icon' => $icon
        ];
    }
    
    public static function navDropdown($title, $subelements, $translated=true) {
        $id = str_replace(".", "", $title);
        if($translated) {
            $title = ucfirst(__($title));
        }
        return [
            'id' => $id,
            'title' => $title,
            'link' => null,
            'subElements' => $subelements,
            'icon' => null,
        ];
    }
}
