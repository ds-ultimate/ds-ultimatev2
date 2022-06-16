<?php

namespace App\Util\Map;

use App\Ally;
use App\Player;
use App\World;
use App\Util\BasicFunctions;
use Illuminate\Support\Facades\DB;

class SQLMapGenerator extends AbstractMapGenerator {
    private $world;
    
    public function __construct(World $world, AbstractSkinRenderer $skin, $dim=null, $show_errs=false) {
        parent::__construct($skin, $dim, $show_errs);
        $this->world = $world;
    }
    
    protected function grabAlly() {
        //Translate ally marks into player marks + Text
        if(count($this->ally) > 0) {
            $allyModel = new Ally($this->world);
            $evaluateModel = false;
            
            foreach($this->ally as $ally) {
                if($ally['showText']) {
                    $allyModel = $allyModel->orWhere('allyID', $ally['id']);
                    $evaluateModel = true;
                }
                $this->dataAlly[$ally['id']] = $ally;
            }
            
            if($evaluateModel) {
                foreach($allyModel->get() as $ally) {
                    $this->dataAlly[$ally->allyID]['name'] = BasicFunctions::decodeName($ally->name);
                    $this->dataAlly[$ally->allyID]['tag'] = BasicFunctions::decodeName($ally->tag);
                }
            }
        }
        
        
        if(count($this->dataAlly) > 0) {
            $playerModel = new Player($this->world);
            foreach($this->dataAlly as $ally) {
                $playerModel = $playerModel->orWhere('ally_id', $ally['id']);
            }

            foreach($playerModel->get() as $player) {
                $this->dataPlayer[$player->playerID] = array(
                    "id" => (int) $player->playerID,
                    "colour" => $this->dataAlly[$player->ally_id]['colour'],
                    "name" => BasicFunctions::decodeName($player->name),
                    "showText" => false,
                    "highLight" => $this->dataAlly[$player->ally_id]['highLight'],
                    "allyID" => $player->ally_id,
                    "villNum" => 0,
                    "villX" => 0,
                    "villY" => 0,
                );
            }
        }
    }
    
    protected function grabPlayer() {
        //Translate player marks into village marks + Text
        if(count($this->player) > 0) {
            $playerModel = new Player($this->world);
            $evaluateModel = false;
            
            foreach($this->player as $player) {
                $this->dataPlayer[$player['id']] = $player;
                
                if($player['showText']) {
                    $playerModel = $playerModel->orWhere('playerID', $player['id']);
                    $evaluateModel = true;
                }
            }

            if($evaluateModel) {
                foreach($playerModel->get() as $player) {
                    $this->dataPlayer[$player->playerID]['name'] = BasicFunctions::decodeName($player->name);
                }
            }
        }
    }
    
    protected function grabVillage() {
        //DO NOT use Laravel functions here as they use way more memory than this code
        $tableName = BasicFunctions::getWorldDataTable($this->world, "village_latest") ;
        $res = DB::select("SELECT name, x, y, points, bonus_id, villageID, owner FROM $tableName WHERE".
                " x >= ".intval($this->mapDimension['xs']).
                " AND y >= ".intval($this->mapDimension['ys']).
                " AND x < ".intval($this->mapDimension['xe']).
                " AND y < ".intval($this->mapDimension['ye']));
        
        $tmpVillage = array();
        foreach($this->village as $village) {
            $tmpVillage[$village['id']] = $village;
        }
        
        $realSize = [
            'xs' => 450,
            'ys' => 450,
            'xe' => 550,
            'ye' => 550,
        ];
        foreach($res as $village) {
            if($village->x < $realSize['xs']) $realSize['xs'] = $village->x;
            if($village->x > $realSize['xe']) $realSize['xe'] = $village->x;
            if($village->y < $realSize['ys']) $realSize['ys'] = $village->y;
            if($village->y > $realSize['ye']) $realSize['ye'] = $village->y;
            
            if(isset($tmpVillage[$village->villageID])) {
                $this->dataVillage['mark'][$village->villageID] = $tmpVillage[$village->villageID];
                //For real village markers
                $this->dataVillage['mark'][$village->villageID]['name'] = $village->name;
                $this->dataVillage['mark'][$village->villageID]['x'] = $village->x;
                $this->dataVillage['mark'][$village->villageID]['y'] = $village->y;
                $this->dataVillage['mark'][$village->villageID]['owner'] = $village->owner;
                $this->dataVillage['mark'][$village->villageID]['points'] = $village->points;
                $this->dataVillage['mark'][$village->villageID]['bonus_id'] = $village->bonus_id;
                $this->dataVillage['mark'][$village->villageID]['marked'] = true;
            } else {
                $tmp = array(
                    "id" => (int) $village->villageID,
                    "colour" => ($village->owner != 0)?($this->playerColour):($this->barbarianColour),
                    "name" => $village->name,
                    "x" => $village->x,
                    "y" => $village->y,
                    "owner" => $village->owner,
                    "points" => $village->points,
                    "bonus_id" => $village->bonus_id,
                    "showText" => false,
                    "highLight" => false,
                    "marked" => false,
                );
                
                if (isset($this->dataPlayer[$village->owner]) && $village->owner != 0) {
                    //For player / ally markers
                    $this->dataVillage['mark'][$village->villageID] = $tmp;
                    $this->dataVillage['mark'][$village->villageID]['colour'] =
                            $this->dataPlayer[$village->owner]['colour'];
                    $this->dataVillage['mark'][$village->villageID]['marked'] = true;
                    $this->dataVillage['mark'][$village->villageID]['highLight'] =
                            $this->dataPlayer[$village->owner]['highLight'];
                    
                    //add village to player weight
                    $this->dataPlayer[$village->owner]['villNum']++;
                    $this->dataPlayer[$village->owner]['villX'] += $village->x;
                    $this->dataPlayer[$village->owner]['villY'] += $village->y;
                } else if($village->owner != 0) {
                    $this->dataVillage['play'][$village->villageID] = $tmp;
                } else {
                    $this->dataVillage['barb'][$village->villageID] = $tmp;
                }
            }
        }
        
        if($this->autoResize) {
            //Add border
            $realSize['xs'] = ($realSize['xs'] >= 10)?($realSize['xs']-10):(0);
            $realSize['ys'] = ($realSize['ys'] >= 10)?($realSize['ys']-10):(0);
            $realSize['xe'] = ($realSize['xe'] <= 990)?($realSize['xe']+10):(0);
            $realSize['ye'] = ($realSize['ye'] <= 990)?($realSize['ye']+10):(0);
            $this->setMapDimensions($realSize);
        }
    }
}
