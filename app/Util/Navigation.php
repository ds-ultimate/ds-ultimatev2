<?php
namespace App\Util;

use App\Server;
use App\World;
use App\Http\Controllers\Tools\AnimatedHistoryMapController;

class Navigation
{
    public static function generateNavArray($serModel, $worldArg) {
        $retArray = [];
        if($worldArg !== null) {
            $serverCodeName = [$worldArg->server->code, $worldArg->name];
        }

        if($serModel !== null) {
            $serverNav = [];
            foreach(World::worldsCollection($serModel) as $worlds) {
                $worldNav = [];
                foreach($worlds as $world) {
                    switch(\Request::route()->getName()) {
                        case 'tools.distanceCalc':
                            $worldNav[] = self::navElement($world->getDistplayName(), 'tools.distanceCalc', routeArgs: [$world->server->code, $world->name], translated: false);
                            break;
                        case 'tools.pointCalc':
                            $worldNav[] = self::navElement($world->getDistplayName(), 'tools.pointCalc', routeArgs: [$world->server->code, $world->name], translated: false);
                            break;
                        case 'tools.tableGenerator':
                            $worldNav[] = self::navElement($world->getDistplayName(), 'tools.tableGenerator', routeArgs: [$world->server->code, $world->name], translated: false);
                            break;
                        case 'tools.accMgrDB.index_world':
                            $worldNav[] = self::navElement($world->getDistplayName(), 'tools.accMgrDB.index_world', routeArgs: [$world->server->code, $world->name], translated: false);
                            break;
                        default:
                            $worldNav[] = self::navElement($world->getDistplayName(), 'world', routeArgs: [$world->server->code, $world->name], translated: false);
                            break;
                    }
                }
                if($worlds[0]->sortType() == "casual") {
                    $serverNav[] = self::navDropdown(title: 'ui.tabletitel.casualWorlds', subelements: $worldNav);
                } else if($worlds[0]->sortType() == "speed") {
                    $serverNav[] = self::navDropdown(title: 'ui.tabletitel.speedWorlds', subelements: $worldNav);
                } else if($worlds[0]->sortType() == "classic") {
                    $serverNav[] = self::navDropdown(title: 'ui.tabletitel.classicWorlds', subelements: $worldNav);
                } else {
                    $serverNav[] = self::navDropdown(title: 'ui.tabletitel.normalWorlds', subelements: $worldNav);
                }
            }
            $retArray[] = self::navElement('ui.titel.worldOverview', 'server', routeArgs: [$serModel->code]);
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
            if($worldArg->win_condition == 9) {
                $tools[] = self::navElement('tool.greatSiegeCalc.title', 'tools.greatSiegeCalc', routeArgs: $serverCodeName);
            }
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
            
            if(AnimatedHistoryMapController::isAvailable($worldArg)) {
                $tools[] = self::navElement('tool.animHistMap.title', 'tools.animHistMap.create', routeArgs: $serverCodeName, nofollow: true);
            } else {
                $tools[] = self::navElementDisabled('tool.animHistMap.title', 'ui.nav.disabled.missingConfig');
            }
        } else {
            $tools[] = self::navElementDisabled('tool.distCalc.title', 'ui.nav.disabled.noWorld');
            $tools[] = self::navElementDisabled('tool.attackPlanner.title', 'ui.nav.disabled.noWorld');
            $tools[] = self::navElementDisabled('tool.map.title', 'ui.nav.disabled.noWorld');
            $tools[] = self::navElementDisabled('tool.pointCalc.title', 'ui.nav.disabled.noWorld');
            $tools[] = self::navElementDisabled('tool.tableGenerator.title', 'ui.nav.disabled.noWorld');
            $tools[] = self::navElement('tool.accMgrDB.title', 'tools.accMgrDB.index');
            
            $tools[] = self::navElementDisabled('tool.animHistMap.title', 'ui.nav.disabled.noWorld');
        }
        $retArray[] = self::navDropdown(title: 'ui.server.tools', subelements: $tools);

        return $retArray;
    }

    public static function generateMobileNavArray($serModel, $worldArg) {
        $navArray = self::generateNavArray($serModel, $worldArg);
        $transSub = [];
        foreach(static::getAvailableTranslations() as $trans) {
            $transSub[] = self::navElement($trans['n'], 'locale', routeArgs: [$trans['s']], translated: false, icon: 'flag-icon '.$trans['f']);
        }
        $navArray[] = self::navDropdown(title: 'ui.language', subelements: $transSub);
        
        if(session('darkmode', false)) {
            $navArray[] = self::navElement('ui.lightmode', 'darkmode', routeArgs: ["false"], translated: true);
        } else {
            $navArray[] = self::navElement('ui.darkmode', 'darkmode', routeArgs: ["true"], translated: true);
        }
        
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
    
    public static function getAvailableTranslations() {
        $trans = [
            ['n' => 'Deutsch', 's' => 'de', 'f' => 'flag-icon-de'],
            ['n' => 'English', 's' => 'en', 'f' => 'flag-icon-gb'],
            ['n' => 'Czech', 's' => 'cz', 'f' => 'flag-icon-cz'],
            ['n' => 'Magyar', 's' => 'hu', 'f' => 'flag-icon-hu'],
        ];
        if(config('app.debug')) {
            $trans[] = ['n' => 'Empty', 's'=> 'empty', 'f' => ''];
        }
        
        return $trans;
    }
    
    public static function getAvailableLocales() {
        $loc = ['de', 'en', 'cz', 'hu'];
        if(config('app.debug')) {
            $loc[] = 'empty';
        }
        return $loc;
    }
    
    public static function getAvailableLocalesForEdit() {
        $ret = [];
        foreach(static::getAvailableLocales() as $loc) {
            $ret[$loc] = $loc;
        }
        return $ret;
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
