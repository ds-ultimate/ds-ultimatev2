<?php

namespace App\Util;

use App\World;

class BuildingUtils {
    public static $BUILDINGS = [
        'main' => ['min_level' => 1, 'max_level' => 30, 'wood' => 90, 'stone' => 80, 'iron' => 70, 'pop' => 5, 'build_time' => 900, 'point' => 10,
            'wood_factor' => 1.26, 'stone_factor' => 1.275, 'iron_factor' => 1.26, 'pop_factor' => 1.17, 'build_time_factor' => 1.2, 'point_factor' => 1.2,
        ],
        'barracks' => ['min_level' => 0, 'max_level' => 25, 'wood' => 200, 'stone' => 170, 'iron' => 90, 'pop' => 7, 'build_time' => 1800, 'point' => 16,
            'wood_factor' => 1.26, 'stone_factor' => 1.28, 'iron_factor' => 1.26, 'pop_factor' => 1.17, 'build_time_factor' => 1.2, 'point_factor' => 1.2,
        ],
        'stable' => ['min_level' => 0, 'max_level' => 20, 'wood' => 270, 'stone' => 240, 'iron' => 260, 'pop' => 8, 'build_time' => 6000, 'point' => 20,
            'wood_factor' => 1.26, 'stone_factor' => 1.28, 'iron_factor' => 1.26, 'pop_factor' => 1.17, 'build_time_factor' => 1.2, 'point_factor' => 1.2,
        ],
        'garage' => ['min_level' => 0, 'max_level' => 15, 'wood' => 300, 'stone' => 240, 'iron' => 260, 'pop' => 8, 'build_time' => 6000, 'point' => 24,
            'wood_factor' => 1.26, 'stone_factor' => 1.28, 'iron_factor' => 1.26, 'pop_factor' => 1.17, 'build_time_factor' => 1.2, 'point_factor' => 1.2,
        ],
        'church' => ['min_level' => 0, 'max_level' => 3, 'wood' => 16000, 'stone' => 20000, 'iron' => 5000, 'pop' => 5000, 'build_time' => 184980, 'point' => 10,
            'wood_factor' => 1.26, 'stone_factor' => 1.28, 'iron_factor' => 1.26, 'pop_factor' => 1.55, 'build_time_factor' => 1.2, 'point_factor' => 1.2,
        ],
        'church_f' => ['min_level' => 0, 'max_level' => 1, 'wood' => 160, 'stone' => 200, 'iron' => 50, 'pop' => 5, 'build_time' => 8160, 'point' => 10,
            'wood_factor' => 1.26, 'stone_factor' => 1.28, 'iron_factor' => 1.26, 'pop_factor' => 1.55, 'build_time_factor' => 1.2, 'point_factor' => 1.2,
        ],
        'watchtower' => ['min_level' => 0, 'max_level' => 20, 'wood' => 12000, 'stone' => 14000, 'iron' => 10000, 'pop' => 500, 'build_time' => 13200, 'point' => 42,
            'wood_factor' => 1.17, 'stone_factor' => 1.17, 'iron_factor' => 1.18, 'pop_factor' => 1.18, 'build_time_factor' => 1.2, 'point_factor' => 1.2,
        ],
        'snob' => ['min_level' => 0, 'max_level' => 1, 'wood' => 15000, 'stone' => 25000, 'iron' => 10000, 'pop' => 80, 'build_time' => 586800, 'point' => 512,
            'wood_factor' => 2, 'stone_factor' => 2, 'iron_factor' => 2, 'pop_factor' => 1.17, 'build_time_factor' => 1.2, 'point_factor' => 1.2,
        ],
        'smith' => ['min_level' => 0, 'max_level' => 20, 'wood' => 220, 'stone' => 180, 'iron' => 240, 'pop' => 20, 'build_time' => 6000, 'point' => 19,
            'wood_factor' => 1.26, 'stone_factor' => 1.275, 'iron_factor' => 1.26, 'pop_factor' => 1.17, 'build_time_factor' => 1.2, 'point_factor' => 1.2,
        ],
        'place' => ['min_level' => 0, 'max_level' => 1, 'wood' => 10, 'stone' => 40, 'iron' => 30, 'pop' => 0, 'build_time' => 10860, 'point' => 0,
            'wood_factor' => 1.26, 'stone_factor' => 1.275, 'iron_factor' => 1.26, 'pop_factor' => 1.17, 'build_time_factor' => 1.2, 'point_factor' => 1.2,
        ],
        'statue' => ['min_level' => 0, 'max_level' => 1, 'wood' => 220, 'stone' => 220, 'iron' => 220, 'pop' => 10, 'build_time' => 1500, 'point' => 24,
            'wood_factor' => 1.26, 'stone_factor' => 1.275, 'iron_factor' => 1.26, 'pop_factor' => 1.17, 'build_time_factor' => 1.2, 'point_factor' => 1.2,
        ],
        'market' => ['min_level' => 0, 'max_level' => 25, 'wood' => 100, 'stone' => 100, 'iron' => 100, 'pop' => 20, 'build_time' => 2700, 'point' => 10,
            'wood_factor' => 1.26, 'stone_factor' => 1.275, 'iron_factor' => 1.26, 'pop_factor' => 1.17, 'build_time_factor' => 1.2, 'point_factor' => 1.2,
        ],
        'wood' => ['min_level' => 0, 'max_level' => 30, 'wood' => 50, 'stone' => 60, 'iron' => 40, 'pop' => 5, 'build_time' => 900, 'point' => 6,
            'wood_factor' => 1.25, 'stone_factor' => 1.275, 'iron_factor' => 1.245, 'pop_factor' => 1.155, 'build_time_factor' => 1.2, 'point_factor' => 1.2,
        ],
        'stone' => ['min_level' => 0, 'max_level' => 30, 'wood' => 65, 'stone' => 50, 'iron' => 40, 'pop' => 10, 'build_time' => 900, 'point' => 6,
            'wood_factor' => 1.27, 'stone_factor' => 1.265, 'iron_factor' => 1.24, 'pop_factor' => 1.14, 'build_time_factor' => 1.2, 'point_factor' => 1.2,
        ],
        'iron' => ['min_level' => 0, 'max_level' => 30, 'wood' => 75, 'stone' => 65, 'iron' => 70, 'pop' => 10, 'build_time' => 1080, 'point' => 6,
            'wood_factor' => 1.252, 'stone_factor' => 1.275, 'iron_factor' => 1.24, 'pop_factor' => 1.17, 'build_time_factor' => 1.2, 'point_factor' => 1.2,
        ],
        'farm' => ['min_level' => 1, 'max_level' => 30, 'wood' => 45, 'stone' => 40, 'iron' => 30, 'pop' => 0, 'build_time' => 1200, 'point' => 5,
            'wood_factor' => 1.3, 'stone_factor' => 1.32, 'iron_factor' => 1.29, 'pop_factor' => 1, 'build_time_factor' => 1.2, 'point_factor' => 1.2,
        ],
        'storage' => ['min_level' => 1, 'max_level' => 30, 'wood' => 60, 'stone' => 50, 'iron' => 40, 'pop' => 0, 'build_time' => 1020, 'point' => 6,
            'wood_factor' => 1.265, 'stone_factor' => 1.27, 'iron_factor' => 1.245, 'pop_factor' => 1.15, 'build_time_factor' => 1.2, 'point_factor' => 1.2,
        ],
        'hide' => ['min_level' => 0, 'max_level' => 10, 'wood' => 50, 'stone' => 60, 'iron' => 50, 'pop' => 2, 'build_time' => 1800, 'point' => 5,
            'wood_factor' => 1.25, 'stone_factor' => 1.25, 'iron_factor' => 1.25, 'pop_factor' => 1.17, 'build_time_factor' => 1.2, 'point_factor' => 1.2,
        ],
        'wall' => ['min_level' => 0, 'max_level' => 20, 'wood' => 50, 'stone' => 100, 'iron' => 20, 'pop' => 5, 'build_time' => 3600, 'point' => 8,
            'wood_factor' => 1.26, 'stone_factor' => 1.275, 'iron_factor' => 1.26, 'pop_factor' => 1.17, 'build_time_factor' => 1.2, 'point_factor' => 1.2,
        ],
        //not buildable in normal villages -> ingore for point calc
        //also none of that information is shown in /interface.php?func=get_building_info exept for max=30 min=1
        'university' => ['min_level' => -1, 'max_level' => -1, 'wood' => -1, 'stone' => -1, 'iron' => -1, 'pop' => -1, 'build_time' => -1, 'point' => -1,
            'wood_factor' => -1, 'stone_factor' => -1, 'iron_factor' => -1, 'pop_factor' => -1, 'build_time_factor' => -1, 'point_factor' => -1,
        ],
    ];
    
    public static $MAIN_REDUCTION = 1.05;
    
    public static function validBuilding($buildingName) {
        return in_array($buildingName, array_keys(static::$BUILDINGS));
    }
    
    //useless function just to make sure everything is tranlated properly
    private function dummy() {
        __("ui.buildings.main");
        __("ui.buildings.barracks");
        __("ui.buildings.stable");
        __("ui.buildings.garage");
        __("ui.buildings.church");
        __("ui.buildings.church_f");
        __("ui.buildings.watchtower");
        __("ui.buildings.snob");
        __("ui.buildings.smith");
        __("ui.buildings.place");
        __("ui.buildings.statue");
        __("ui.buildings.market");
        __("ui.buildings.wood");
        __("ui.buildings.stone");
        __("ui.buildings.iron");
        __("ui.buildings.farm");
        __("ui.buildings.storage");
        __("ui.buildings.hide");
        __("ui.buildings.wall");
    }
    
    const BUILDING_SIZE_SMALL = 1;
    const BUILDING_SIZE_MEDIUM = 2;
    const BUILDING_SIZE_BIG = 3;
    public static function getImage($buildingName, $size=BuildingUtils::BUILDING_SIZE_SMALL, $level=1) {
        if(!static::validBuilding($buildingName)) {
            return Icon::icons(-1);
        }
        if($buildingName == "church_f") $buildingName = "church";

        if($size == static::BUILDING_SIZE_SMALL) {
            return asset("images/ds_images/buildings/small/$buildingName.png");
        }
        else if($size == static::BUILDING_SIZE_MEDIUM) {
            return asset("images/ds_images/buildings/mid/$buildingName$level.png");
        }
        else if($size == static::BUILDING_SIZE_BIG) {
            return asset("images/ds_images/buildings/big/$buildingName$level.png");
        }
        else {
            throw new \InvalidArgumentException("Invalid size");
        }
    }
    
    public static function getFarmSpace($level) {
        $space = 240 * pow(100, ($level - 1) / 29);
        return floor($space);
    }
    
    public static function calculateRemainingFarm($levels) {
        $gesFarm = static::getFarmSpace($levels['farm'] ?? 0);
        foreach($levels as $name=>$lv) {
            $gesFarm-= static::calculateExpoential($name, "pop", $lv);
        }
        return $gesFarm;
    }
    
    public static function getStorageSpace($level) {
        $space = 1000 * pow(400, ($level - 1) / 29);
        return floor($space);
    }
    
    public static function calculatePoints($levels) {
        $gesPoints = 0;
        foreach($levels as $name=>$lv) {
            $gesPoints+= static::calculateExpoential($name, "point", $lv);
        }
        return $gesPoints;
    }
    
    public static function getMaxLevel($buildingName) {
        if(!static::validBuilding($buildingName)) {
            return -1;
        }
        $props = static::$BUILDINGS[$buildingName];
        return $props['max_level'];
    }
    
    public static function calculateExpoential($buildingName, $propName, $level) {
        if(!static::validBuilding($buildingName)) {
            return -1;
        }
        if($level == 0) return 0;
        
        $props = static::$BUILDINGS[$buildingName];
        return round($props[$propName] * pow($props[$propName."_factor"], $level-1));
    }
    
    public static function getPointBuildingMap(World $worldData=null) {
        if($worldData != null && $worldData->buildings != null) {
            $buildingConfig = simplexml_load_string($worldData->buildings);
            $names = [];
            foreach($buildingConfig as $key => $val) {
                $names[] = $key;
            }
        } else {
            $names = array_keys(static::$BUILDINGS);
        }
        
        $map = [];
        foreach($names as $name) {
            if($name == 'university') continue;
            if(!isset(static::$BUILDINGS[$name])) {
                //error silence? don't want to get spam
                continue;
            }
            $settings = static::$BUILDINGS[$name];
            
            for($i = max($settings['min_level'], 1); $i <= $settings['max_level']; $i++) {
                $pnt = round($settings['point'] * pow($settings['point_factor'], $i-1));
                $pnt_last = $i>1 ? round($settings['point'] * pow($settings['point_factor'], $i-2)) : 0;
                $pnt_diff = $pnt - $pnt_last;
                if(! isset($map[$pnt_diff])) {
                    $map[$pnt_diff] = [];
                }
                $map[$pnt_diff][] = [$name, $i];
            }
        }
        return $map;
    }
}
