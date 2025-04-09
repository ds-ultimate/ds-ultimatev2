<?php

namespace App\Util\Map;

use App\HistoryIndex;
use App\World;
use App\Util\BasicFunctions;

class HistoryMapGenerator extends AbstractMapGenerator {
    private $world;
    
    public function __construct(World $world, HistoryIndex $histIdx, AbstractSkinRenderer $skin, $dim=null, $show_errs=false) {
        parent::__construct($skin, $dim, $show_errs);
        $this->world = $world;
        
        $this->allyFile = $histIdx->allyFile($world);
        $this->playerFile = $histIdx->playerFile($world);
        $this->villageFile = $histIdx->villageFile($world);
    }
    
    protected function grabAlly() {
        //Translate ally marks into player marks + Text
        if(count($this->ally) > 0) {
            
            $ids = [];
            $searchFile = false;
            foreach($this->ally as $ally) {
                if($ally['showText']) {
                    $ids[] = $ally['id'];
                    $searchFile = true;
                }
                $this->dataAlly[$ally['id']] = $ally;
            }
            
            if($searchFile) {
                $file = gzopen($this->allyFile, "r");
                while(! gzeof($file)) {
                    $line = explode(";", str_replace("\n", "", gzgets($file, 4096)));
                    
                    if(in_array($line[0], $ids)) {
                        $this->dataAlly[$line[0]]['name'] = BasicFunctions::decodeName($line[1]);
                        $this->dataAlly[$line[0]]['tag'] = BasicFunctions::decodeName($line[2]);
                    }
                }
                gzclose($file);
            }

            foreach($ids as $allyId) {
                if(!isset($this->dataAlly[$allyId]['name']) || !isset($this->dataAlly[$allyId]['tag'])) {
                    $this->dataAlly[$allyId]['name'] = "";
                    $this->dataAlly[$allyId]['tag'] = "";
                }
            }
        }
        
        
        if(count($this->dataAlly) > 0) {
            $ids = [];
            foreach($this->dataAlly as $ally) {
                $ids[] = $ally['id'];
            }
            
            $file = gzopen($this->playerFile, "r");
            while(! gzeof($file)) {
                $lineOrig = gzgets($file, 4096);
                if($lineOrig === false) continue;
                $line = explode(";", str_replace("\n", "", $lineOrig));

                if(in_array($line[2], $ids)) {
                    $this->dataPlayer[$line[0]] = array(
                        "id" => (int) $line[0],
                        "colour" => $this->dataAlly[$line[2]]['colour'],
                        "name" => BasicFunctions::decodeName($line[1]),
                        "showText" => false,
                        "highLight" => $this->dataAlly[$line[2]]['highLight'],
                        "allyID" => $line[2],
                        "villNum" => 0,
                        "villX" => 0,
                        "villY" => 0,
                    );
                }
            }
            gzclose($file);
        }
    }
    
    protected function grabPlayer() {
        //Translate player marks into village marks + Text
        if(count($this->player) > 0) {
            $ids = [];
            $searchFile = false;
            foreach($this->player as $player) {
                if($player['showText']) {
                    $ids[] = $player['id'];
                    $searchFile = true;
                }
                $this->dataPlayer[$player['id']] = $player;
            }
            
            if($searchFile) {
                $file = gzopen($this->playerFile, "r");
                while(! gzeof($file)) {
                    $lineOrig = gzgets($file, 4096);
                    if($lineOrig === false) continue;
                    $line = explode(";", str_replace("\n", "", $lineOrig));

                    if(in_array($line[0], $ids)) {
                        $this->dataPlayer[$line[0]]['name'] = BasicFunctions::decodeName($line[1]);
                    }
                }
                gzclose($file);

                foreach($ids as $playerId) {
                    if(!isset($this->dataPlayer[$playerId]['name'])) {
                        // just because a village has somebody as the owner doesn't mean that the owner is part of the player.txt
                        $this->dataPlayer[$playerId]['name'] = "";
                    }
                }
            }
        }
    }
    
    protected function grabVillage() {
        $tmpVillage = [];
        foreach($this->village as $village) {
            $tmpVillage[$village['id']] = $village;
        }
        
        $realSize = [
            'xs' => 450,
            'ys' => 450,
            'xe' => 550,
            'ye' => 550,
        ];
        
        $file = gzopen($this->villageFile, "r");
        while(! gzeof($file)) {
            $lineOrig = gzgets($file, 4096);
            if($lineOrig === false) continue;
            $line = explode(";", str_replace("\n", "", $lineOrig));
            
            if($line[6] < 22 || $line[6] > 32) {
                // Ignore Strongholds because players like to be funny and place them at absurd places
                if($line[2] < $realSize['xs']) $realSize['xs'] = $line[2];
                if($line[2] > $realSize['xe']) $realSize['xe'] = $line[2];
                if($line[3] < $realSize['ys']) $realSize['ys'] = $line[3];
                if($line[3] > $realSize['ye']) $realSize['ye'] = $line[3];
            }
            
            if(isset($tmpVillage[$line[0]])) {
                $this->dataVillage['mark'][$line[0]] = $tmpVillage[$line[0]];
                //For real village markers
                $this->dataVillage['mark'][$line[0]]['name'] = $line[1];
                $this->dataVillage['mark'][$line[0]]['x'] = $line[2];
                $this->dataVillage['mark'][$line[0]]['y'] = $line[3];
                $this->dataVillage['mark'][$line[0]]['owner'] = $line[5];
                $this->dataVillage['mark'][$line[0]]['points'] = $line[4];
                $this->dataVillage['mark'][$line[0]]['bonus_id'] = $line[6];
                $this->dataVillage['mark'][$line[0]]['marked'] = true;
            } else {
                $tmp = array(
                    "id" => (int) $line[0],
                    "colour" => ($line[5] != 0)?($this->playerColour):($this->barbarianColour),
                    "name" => $line[1],
                    "x" => $line[2],
                    "y" => $line[3],
                    "owner" => $line[5],
                    "points" => $line[4],
                    "bonus_id" => $line[6],
                    "showText" => false,
                    "highLight" => false,
                    "marked" => false,
                );
                
                if (isset($this->dataPlayer[$line[5]]) && $line[5] != 0) {
                    //For player / ally markers
                    $this->dataVillage['mark'][$line[0]] = $tmp;
                    $this->dataVillage['mark'][$line[0]]['colour'] =
                            $this->dataPlayer[$line[5]]['colour'];
                    $this->dataVillage['mark'][$line[0]]['marked'] = true;
                    $this->dataVillage['mark'][$line[0]]['highLight'] =
                            $this->dataPlayer[$line[5]]['highLight'];
                    
                    //add village to player weight
                    $this->dataPlayer[$line[5]]['villNum']++;
                    $this->dataPlayer[$line[5]]['villX'] += $line[2];
                    $this->dataPlayer[$line[5]]['villY'] += $line[3];
                } else if($line[5] != 0) {
                    $this->dataVillage['play'][$line[0]] = $tmp;
                } else {
                    $this->dataVillage['barb'][$line[0]] = $tmp;
                }
            }
        }
        gzclose($file);
        
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
