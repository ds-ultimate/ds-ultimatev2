<?php

namespace App\Util;


class Constants
{
    public static function diffBuildingPoints($worldConf){
        return [
            'main'=> [0, 10, 2, 2, 3, 4, 4, 5, 6, 7, 9, 10, 12, 15, 18, 21, 26,
                31, 37, 44, 53, 64, 77, 92, 110, 133, 159, 191, 229, 274, 330],
            
            'barracks'=> [0, 16, 3, 4, 5, 5, 7, 8, 9, 12, 14, 16, 20, 24, 28,
                34, 42, 49, 59, 71, 85, 102, 123, 147, 177, 212],
            
            'stable'=> [0, 20, 4, 5, 6, 6, 9, 10, 12, 14, 17, 21, 25, 29, 36,
                43, 51, 62, 74, 88, 107],
            
            'garage'=> [0, 24, 5, 6, 6, 9, 10, 12, 14, 17, 21, 25, 29, 36,
                43, 51],
            
            'church'=> [0, 10, 2, 2],
            
            'firstChurch'=> [0, 10],
            
            'watchtower'=> [0, 42, 8, 10, 13, 14, 18, 20, 25, 31, 36, 43, 52,
                62, 75, 90, 108, 130, 155, 186, 224],
            
            'snob'=> [0, 512, 102, 123],
            
            'smith'=> [0, 19, 4, 4, 6, 6, 8, 10, 11, 14, 16, 20, 23, 28, 34,
                41, 49, 58, 71, 84, 101],
            
            'place'=> [0, 0],
            
            'statue'=> [0, 24],
            
            'market'=> [0, 10, 2, 2, 3, 4, 4, 5, 6, 7, 9, 10, 12, 15, 18, 21,
                26, 31, 37, 44, 53, 64, 77, 92, 110, 133, 159, 191, 229, 274, 330],
            
            'wood'=> [0, 6, 1, 2, 1, 2, 3, 3, 3, 5, 5, 6, 8, 8, 11, 13, 15,
                19, 22, 27, 32, 38, 46, 55, 66, 80, 95, 115, 137, 165, 198],
            
            'stone'=> [0, 6, 1, 2, 1, 2, 3, 3, 3, 5, 5, 6, 8, 8, 11, 13, 15,
                19, 22, 27, 32, 38, 46, 55, 66, 80, 95, 115, 137, 165, 198],
            
            'iron'=> [0, 6, 1, 2, 1, 2, 3, 3, 3, 5, 5, 6, 8, 8, 11, 13, 15,
                19, 22, 27, 32, 38, 46, 55, 66, 80, 95, 115, 137, 165, 198],
            
            'farm'=> [0, 5, 1, 1, 2, 1, 2, 3, 3, 3, 5, 5, 6, 8, 8, 11, 13,
                15, 19, 22, 27, 32, 38, 46, 55, 66, 80, 95, 115, 137, 165],
            
            'storage'=> [0, 6, 1, 2, 1, 2, 3, 3, 3, 5, 5, 6, 8, 8, 11, 13,
                15, 19, 22, 27, 32, 38, 46, 55, 66, 80, 95, 115, 137, 165, 198],
            
            'hide'=> [0, 5, 1, 1, 2, 1, 2, 3, 3, 3, 5],
            
            'wall'=> [0, 8, 2, 2, 2, 3, 3, 4, 5, 5, 7, 9, 9, 12, 15, 17, 20,
                25, 29, 36, 43],
        ];
    }
    
    public static function gesBuildingPoints($worldConf) {
        $diffPoints = Constants::diffBuildingPoints($worldConf);
        
        $gesPoints = array();
        foreach($diffPoints as $name => $value) {
            $temp = array();
            $ges = 0;
            for($i = 0; $i < count($value); $i++) {
                $ges += $value[$i];
                $temp[] = $ges;
            }
            $gesPoints[$name] = $temp;
        }
        return $gesPoints;
    }
    
    //useless function just to make sure everything is tranlated properly
    private function dummy() {
        __("ui.buildings.main");
        __("ui.buildings.barracks");
        __("ui.buildings.stable");
        __("ui.buildings.garage");
        __("ui.buildings.church");
        __("ui.buildings.firstChurch");
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
