<?php

namespace App\Util;


class Constants
{
    public static function buildingPoints($worldConf, $building){
        if(!Constants::validBuilding($building)) {
            return [
                "points"=>0,
                "points_factor"=>1.2,
            ];
        }
        if($building == "church_f") $building = "church";
        
        $basePoints = [
            'main'=> 10,
            'barracks'=> 16,
            'stable'=> 20,
            'garage'=> 24,
            'church'=> 10,
            'watchtower'=> 42,
            'snob'=> 512,
            'smith'=> 19,
            'place'=> 0,
            'statue'=> 24,
            'market'=> 10,
            'wood'=> 6,
            'stone'=> 6,
            'iron'=> 6,
            'farm'=> 5,
            'storage'=> 6,
            'hide'=> 5,
            'wall'=> 8,
        ];
        
        return [
            "points"=>$basePoints[$building],
            "points_factor"=>1.2,
        ];
    }
    
    public static function validBuilding($buildingName) {
        return in_array($buildingName, Constants::getValidBuildingNames());
    }
    
    public static function getValidBuildingNames() {
        return [
            "barracks", "church", "church_f", "farm", "garage", "iron",
            "main", "market", "place", "smith", "snob", "stable", "statue",
            "stone", "storage", "hide", "wall", "watchtower", "wood",
        ];
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
}
