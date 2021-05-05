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
                    $worldNav[] = self::navElement($world->displayName(), 'world', routeArgs: [$world->server->code, $world->name], translated: false);
                }
                if($worlds->get(0)->sortType() == "casual") {
                    $serverNav[] = self::navDropdown(title: 'ui.tabletitel.casualWorlds', subelements: $worldNav);
                } else if($worlds->get(0)->sortType() == "speed") {
                    $serverNav[] = self::navDropdown(title: 'ui.tabletitel.speedWorlds', subelements: $worldNav);
                } else if($worlds->get(0)->sortType() == "classic") {
                    $serverNav[] = self::navDropdown(title: 'ui.tabletitel.classicWorlds', subelements: $worldNav);
                } else {
                    $serverNav[] = self::navDropdown(title: 'ui.tabletitel.normalWorlds', subelements: $worldNav);
                }
            }
            $retArray[] = self::navElement('ui.titel.worldOverview', 'server', routeArgs: [$serverArg]);
            $retArray[] = self::navDropdown(title: 'ui.server.worlds', subelements: $serverNav);
        }

        if($worldArg !== null) {
            $serverCodeName = [$worldArg->server->code, $worldArg->name];
            $retArray[] = self::navDropdown(title: 'ui.server.ranking', subelements: [
                self::navElement('ui.tabletitel.top10', 'world', $serverCodeName),
                self::navElement(ucfirst(__('ui.table.player')) . " (" . __('ui.nav.current') . ")", 'worldPlayer', routeArgs: $serverCodeName, translated: false),
                self::navElement(ucfirst(__('ui.table.player')) . " (" . __('ui.nav.history') . ")", 'rankPlayer', routeArgs: $serverCodeName, translated: false),
                self::navElement(ucfirst(__('ui.table.ally')) . " (" . __('ui.nav.current') . ")", 'worldAlly', routeArgs: $serverCodeName, translated: false),
                self::navElement(ucfirst(__('ui.table.ally')) . " (" . __('ui.nav.history') . ")", 'rankAlly', routeArgs: $serverCodeName, translated: false),
            ]);
            $retArray[] = self::navDropdown(title: 'ui.conquer.all', subelements: [
                self::navElement(ucfirst(__('ui.conquer.all')) . " " . __('ui.nav.history'), 'worldConquer', routeArgs: [$worldArg->server->code, $worldArg->name, 'all'], translated: false),
                self::navElement('ui.conquer.daily', 'conquerDaily', routeArgs: $serverCodeName, nofollow: true),
            ]);
        }

        $tools = [];
        if($worldArg !== null) {
            if($worldArg->config != null && $worldArg->units != null) {
                $tools[] = self::navElement('tool.distCalc.title', 'tools.distanceCalc', routeArgs: $serverCodeName);
                $tools[] = self::navElement('tool.attackPlanner.title', 'tools.attackPlannerNew', routeArgs: $serverCodeName, nofollow: true);
            } else {
                $tools[] = self::navElementDisabled('tool.distCalc.title', 'ui.nav.disabled.missingConfig');
                $tools[] = self::navElementDisabled('tool.attackPlanner.title', 'ui.nav.disabled.missingConfig');
            }
            $tools[] = self::navElement('tool.map.title', 'tools.mapNew', routeArgs: $serverCodeName, nofollow: true);

            if($worldArg->config != null && $worldArg->buildings != null) {
                $tools[] = self::navElement('tool.pointCalc.title', 'tools.pointCalc', routeArgs: $serverCodeName);
            } else {
                $tools[] = self::navElementDisabled('tool.pointCalc.title', 'ui.nav.disabled.missingConfig');
            }
            $tools[] = self::navElement('tool.tableGenerator.title', 'tools.tableGenerator', routeArgs: $serverCodeName);

            if($worldArg->config != null && $worldArg->units != null) {
                $tools[] = self::navElement('tool.accMgrDB.title', 'tools.accMgrDB.index_world', routeArgs: $serverCodeName);
            } else {
                $tools[] = self::navElementDisabled('tool.accMgrDB.title', 'ui.nav.disabled.missingConfig');
            }
            
            
            if(\Gate::allows('anim_hist_map_beta')) {
                $tools[] = self::navElement('tool.animHistMap.title', 'tools.animHistMap.create', routeArgs: $serverCodeName, nofollow: true);
            }
        } else {
            $tools[] = self::navElementDisabled('tool.distCalc.title', 'ui.nav.disabled.noWorld');
            $tools[] = self::navElementDisabled('tool.attackPlanner.title', 'ui.nav.disabled.noWorld');
            $tools[] = self::navElementDisabled('tool.map.title', 'ui.nav.disabled.noWorld');
            $tools[] = self::navElementDisabled('tool.pointCalc.title', 'ui.nav.disabled.noWorld');
            $tools[] = self::navElementDisabled('tool.tableGenerator.title', 'ui.nav.disabled.noWorld');
            $tools[] = self::navElement('tool.accMgrDB.title', 'tools.accMgrDB.index');
            
            if(\Gate::allows('anim_hist_map_beta')) {
                $tools[] = self::navElementDisabled('tool.animHistMap.title', 'ui.nav.disabled.noWorld');
            }
        }
        $retArray[] = self::navDropdown(title: 'ui.server.tools', subelements: $tools);

        return $retArray;
    }

    public static function generateMobileNavArray($serverArg, $worldArg) {
        $navArray = self::generateNavArray($serverArg, $worldArg);
        $navArray[] = self::navDropdown(title: 'ui.language', subelements: [
            self::navElement('Deutsch', 'locale', routeArgs: ['de'], translated: false, icon: 'flag-icon flag-icon-de'),
            self::navElement('English', 'locale', routeArgs: ['en'], translated: false, icon: 'flag-icon flag-icon-gb'),
        ]);
        if(\Auth::check()) {
            $userOpt = [];
            $userOpt[] = self::navElement('ui.titel.overview', 'user.overview', routeArgs: ['myMap']);
            if(\Gate::allows('dashboard_access')) {
                $userOpt[] = self::navElement('user.dashboard', 'admin.home');
            }
            if(\Gate::allows('translation_access')) {
            //    $userOpt[] = self::navElement('user.translations', 'translations');
            }
            $userOpt[] = self::navElement('ui.personalSettings.title', 'user.settings', routeArgs: ['settings-profile']);
            $userOpt[] = self::navElement('user.logout', 'logout');

            $navArray[] = self::navDropdown(title: \Auth::user()->name , subelements: $userOpt, translated: false);
        } else {
            $navArray[] = self::navElement('user.login', 'login');
            $navArray[] = self::navElement('user.register', 'register');
        }
        return $navArray;
    }

    public static function navElement($title, $route, $routeArgs=null, $translated=true, $icon=null, $nofollow=false) {
        $id = str_replace(".", "", $title);
        if($translated) {
            $title = ucfirst(__($title));
        }
        return [
            'id' => $id,
            'title' => $title,
            'link' => route($route, $routeArgs),
            'subElements' => null,
            'icon' => $icon,
            'tooltip' => null,
            'enabled' => true,
            'noFollow' => $nofollow,
        ];
    }

    public static function navElementDisabled($title, $tooltip, $translatedTitle=true, $translatedTooltip=true) {
        $id = str_replace(".", "", $title);
        if($translatedTitle) {
            $title = ucfirst(__($title));
        }
        if($translatedTooltip) {
            $tooltip = ucfirst(__($tooltip));
        }
        return [
            'id' => $id,
            'title' => $title,
            'link' => "",
            'subElements' => null,
            'icon' => null,
            'tooltip' => $tooltip,
            'enabled' => false,
            'noFollow' => false,
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
            'tooltip' => null,
            'enabled' => true,
        ];
    }
}
