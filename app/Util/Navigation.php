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
                if($worlds->get(0)->sortType() == "casual") {
                    $serverNav[] = self::navDropdown('ui.tabletitel.casualWorlds', $worldNav);
                } else if($worlds->get(0)->sortType() == "speed") {
                    $serverNav[] = self::navDropdown('ui.tabletitel.speedWorlds', $worldNav);
                } else if($worlds->get(0)->sortType() == "classic") {
                    $serverNav[] = self::navDropdown('ui.tabletitel.classicWorlds', $worldNav);
                } else {
                    $serverNav[] = self::navDropdown('ui.tabletitel.normalWorlds', $worldNav);
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
            $retArray[] = self::navDropdown('ui.conquer.all', [
                self::navElement(ucfirst(__('ui.conquer.all')) . " " . __('ui.nav.history'), 'worldConquer', [$worldArg->server->code, $worldArg->name, 'all'], false),
                self::navElement('ui.conquer.daily', 'conquerDaily', [$worldArg->server->code, $worldArg->name]),
            ]);
        }

        $tools = [];
        if($worldArg !== null) {
            if($worldArg->config != null && $worldArg->units != null) {
                $tools[] = self::navElement('tool.distCalc.title', 'tools.distanceCalc', $serverCodeName);
                $tools[] = self::navElement('tool.attackPlanner.title', 'tools.attackPlannerNew', $serverCodeName);
            } else {
                $tools[] = self::navElementDisabled('tool.distCalc.title', 'ui.nav.disabled.missingConfig');
                $tools[] = self::navElementDisabled('tool.attackPlanner.title', 'ui.nav.disabled.missingConfig');
            }
            $tools[] = self::navElement('tool.map.title', 'tools.mapNew', $serverCodeName);

            if($worldArg->config != null && $worldArg->buildings != null) {
                $tools[] = self::navElement('tool.pointCalc.title', 'tools.pointCalc', $serverCodeName);
            } else {
                $tools[] = self::navElementDisabled('tool.attackPlanner.title', 'ui.nav.disabled.missingConfig');
            }
            $tools[] = self::navElement('tool.tableGenerator.title', 'tools.tableGenerator', $serverCodeName);

            if($worldArg->config != null && $worldArg->units != null) {
                $tools[] = self::navElement('tool.accMgrDB.title', 'tools.accMgrDB.index_world', $serverCodeName);
            } else {
                $tools[] = self::navElementDisabled('tool.accMgrDB.title', 'ui.nav.disabled.missingConfig');
            }
        } else {
            $tools[] = self::navElementDisabled('tool.distCalc.title', 'ui.nav.disabled.noWorld');
            $tools[] = self::navElementDisabled('tool.attackPlanner.title', 'ui.nav.disabled.noWorld');
            $tools[] = self::navElementDisabled('tool.map.title', 'ui.nav.disabled.noWorld');
            $tools[] = self::navElementDisabled('tool.pointCalc.title', 'ui.nav.disabled.noWorld');
            $tools[] = self::navElementDisabled('tool.tableGenerator.title', 'ui.nav.disabled.noWorld');
            $tools[] = self::navElement('tool.accMgrDB.title', 'tools.accMgrDB.index');
        }
        $retArray[] = self::navDropdown('ui.server.tools', $tools);

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
            'icon' => $icon,
            'tooltip' => null,
            'enabled' => true,
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
