<?php

namespace App\Util;

use App\Ally;
use App\Player;
use App\Village;
use App\World;

class MapGenerator extends PictureRender {
    private $font;
    private $width;
    private $height;
    private $show_errs;
    private $world;
    
    private $ally;
    private $player;
    private $village;
    private $skin;
    private $opaque;
    private $mapDimension;
    private $playerColour;
    private $barbarianColour;
    
    public static $LAYER_MARK = 0;
    public static $LAYER_PICTURE = 1;
    public static $LAYER_TEXT = 2;
    private $layerOrder;

    /*
     * Variables that are holding Data got from Database
     */
    private $dataAlly;
    private $dataPlayer;
    private $dataVillage;
    
    public function __construct(World $world, $font, $dim=null, $show_errs=false) {
        $std_aspect = 1/1;
        if(isset($dim["height"]) && isset($dim["width"])) {
            $img_height = $dim["height"];
            $img_width = $dim["width"];
        } else if(isset($dim["height"])) {
            $img_height = $dim["height"];
            $img_width = $std_aspect * $img_height;
        } else if(isset($dim["width"])) {
            $img_width = $dim["width"];
            $img_height = $img_width / $std_aspect;
        } else {
            $img_height = 1000;
            $img_width = $std_aspect * $img_height;
        }
        
        $image = imagecreatetruecolor(round($img_width, 0), round($img_height, 0));
        imagealphablending($image, true); //needed to work with alpha values
        
        if($image === false) die("Error");

        $this -> font = realpath($font);
        $this -> image = $image;
        $this -> width= $img_width;
        $this -> height = $img_height;
        $this -> show_errs = $show_errs;
        $this -> world = $world;
        
        $this -> ally = array();
        $this -> player = array();
        $this -> village = array();
        $this -> setSkin('symbol');
        $this ->setLayerOrder(array(
            MapGenerator::$LAYER_MARK,
            MapGenerator::$LAYER_PICTURE,
            MapGenerator::$LAYER_TEXT,
        ));
        $this -> setOpaque(50);
        $this -> setMapDimensions(0, 0, 1000, 1000);
        $this -> setPlayerColour(0, 0, 255);
        $this -> setBarbarianColour(0, 255, 0);
    }

    public function render() {
        $this->grabInformation();
        
        $color_white = imagecolorallocate($this->image, 255, 255, 255); #Background
        imagefill($this->image, 0, 0, $color_white);
        
        foreach($this->layerOrder as $layer) {
            switch ($layer) {
                case MapGenerator::$LAYER_MARK:
                    $this->renderMarks();
                case MapGenerator::$LAYER_PICTURE:
                    $this->renderPictures();
                case MapGenerator::$LAYER_TEXT:
                    $this->renderText();
            }
        }
    }
    
    private function grabInformation() {
        $this->dataAlly = array();
        $this->dataPlayer = array();
        $this->dataVillage = array();
        
        $this->grabAlly();
        $this->grabPlayer();
        $this->grabVillage();
    }
    
    private function grabAlly() {
        //Translate ally marks into player marks + Text
        if(count($this->ally) > 0) {
            $allyModel = new Ally();
            $allyModel->setTable(BasicFunctions::getDatabaseName($this->world->server->code, $this->world->name).'.ally_latest');
            
            foreach($this->ally as $ally) {
                if($ally['showText']) {
                    $allyModel = $allyModel->where('allyID', $ally['id']);
                }
                $this->dataAlly[$ally['id']] = $ally;
            }
            
            foreach($allyModel->get() as $ally) {
                $this->dataAlly[$ally->allyID]['name'] = $ally->name;
                $this->dataAlly[$ally->allyID]['tag'] = $ally->tag;
            }
        }
        
        
        if(count($this->dataAlly) > 0) {
            $playerModel = new Player();
            $playerModel->setTable(BasicFunctions::getDatabaseName($this->world->server->code, $this->world->name).'.player_latest');
            foreach($this->dataAlly as $ally) {
                $playerModel = $playerModel->where('ally_id', $ally['id']);
            }

            foreach($playerModel->get() as $player) {
                $this->dataPlayer[$player->playerID] = array(
                    "id" => (int) $player->playerID,
                    "colour" => $this->dataAlly[$player->ally_id]['colour'],
                    "name" => $player->name,
                    "showText" => false,
                );
            }
        }
    }
    
    private function grabPlayer() {
        //Translate player marks into village marks + Text
        if(count($this->player) > 0) {
            $playerModel = new Player();
            $playerModel->setTable(BasicFunctions::getDatabaseName($this->world->server->code, $this->world->name).'.player_latest');
            
            foreach($this->player as $player) {
                if($player['showText']) {
                    $playerModel = $playerModel->where('allyID', $player['id']);
                }
                $this->dataPlayer[$player['id']] = $player;
            }

            foreach($playerModel->get() as $player) {
                $this->dataPlayer[$player->playerID]['name'] = $player->name;
            }
        }
    }
    
    private function grabVillage() {
        $villageModel = new Village();
        $villageModel->setTable(BasicFunctions::getDatabaseName($this->world->server->code, $this->world->name).'.village_latest');
        $villageModel = $villageModel->where('x', '>=', $this->mapDimension[0])
                ->where('y', '>=', $this->mapDimension[1])
                ->where('x', '<', $this->mapDimension[2])
                ->where('y', '<', $this->mapDimension[3]);

        foreach($this->village as $village) {
            $this->dataVillage[$village['id']] = $village;
        }

        foreach($villageModel->get() as $village) {
            if(isset($this->dataVillage[$village->villageID])) {
                //For real village markers
                $this->dataVillage[$village->villageID]['name'] = $village->name;
                $this->dataVillage[$village->villageID]['x'] = $village->x;
                $this->dataVillage[$village->villageID]['y'] = $village->y;
                $this->dataVillage[$village->villageID]['points'] = $village->points;
                $this->dataVillage[$village->villageID]['bonus_id'] = $village->bonus_id;
            } else {
                $this->dataVillage[$village->villageID] = array(
                    "id" => (int) $village->villageID,
                    "colour" => ($village->owner != 0)?($this->playerColour):($this->barbarianColour),
                    "name" => $village->name,
                    "x" => $village->x,
                    "y" => $village->y,
                    "points" => $village->points,
                    "bonus_id" => $village->bonus_id,
                    "showText" => false,
                );
                
                if (isset($this->dataPlayer[$village->owner])) {
                    //For player / ally markers
                    $this->dataVillage[$village->villageID]['colour'] =
                            $this->dataPlayer[$village->owner]['colour'];
                }
            }
            
            
        }
    }
    
    private function renderMarks() {
        $mapWidth = $this->mapDimension[2] - $this->mapDimension[0];
        $mapHeight = $this->mapDimension[3] - $this->mapDimension[1];
        
        $fieldWidth = $this->width / $mapWidth;
        $fieldHeight = $this->height / $mapHeight;
        
        foreach($this->dataVillage as $village) {
            $col = imagecolorallocatealpha($this->image, $village['colour'][0], $village['colour'][1], $village['colour'][2], 127-$this->opaque*127/100);
            imagesetthickness($this->image, 1); //deactivate to draw Rectangles without stroke
            
            $x = intval($fieldWidth * ($village['x'] - $this->mapDimension[0]));
            $y = intval($fieldHeight * ($village['y'] - $this->mapDimension[1]));
            imagefilledrectangle($this->image, $x, $y, intval($x + $fieldWidth - 1), intval($y + $fieldHeight - 1), $col);
        }
    }
    
    private function renderPictures() {
        return;
        /*
         * https://www.php.net/manual/de/function.imagecreatefrompng.php
         *  imagecopyresampled ( resource $dst_image , resource $src_image , int $dst_x , int $dst_y , int $src_x , int $src_y , int $dst_w , int $dst_h , int $src_w , int $src_h ) : bool
         * https://www.php.net/manual/de/function.imagecopyresampled.php
         *  imagecreatefrompng ( string $filename ) : resource
         * 
         */
        
        
        $this->skin = 'old';
        $skin = array();
        $skin['b1'] = array('img' => imagecreatefrompng("images/ds_images/skins/{$this->skin}/b1.png"));
        $skin['b1']['x'] = imagesx($skin['b1']['img']);
        $skin['b1']['y'] = imagesy($skin['b1']['img']);
        $skin['b1']['asp'] = $skin['b1']['x'] / $skin['b1']['y'];
        
        imagecopyresampled($this->image, $skin['b1']['img'], 10, 10, 0, 0, 100, 100, $skin['b1']['x'], $skin['b1']['y']);
        imagecopyresampled($this->image, $skin['b1']['img'], 10, 200, 0, 0, 100, 100 / $skin['b1']['asp'], $skin['b1']['x'], $skin['b1']['y']);
        
        $blue = imagecolorallocate($this->image, 0, 0, 255);
        imagefilledrectangle($this->image, 200, 10, 200+$skin['b1']['asp']*100, 10+100, $blue);
        imagecopyresampled($this->image, $skin['b1']['img'], 200, 10, 0, 0, 100*$skin['b1']['asp'], 100, $skin['b1']['x'], $skin['b1']['y']);
        
        $yellow = imagecolorallocatealpha($this->image, 255, 255, 0, 127-$this->opaque*127/100);
        imagecopyresampled($this->image, $skin['b1']['img'], 200, 200, 0, 0, 100*$skin['b1']['asp'], 100, $skin['b1']['x'], $skin['b1']['y']);
        imagefilledrectangle($this->image, 200, 200, 200+$skin['b1']['asp']*100, 200+100, $yellow);
    }
    
    private function renderText() {
        //TODO for future
    }
    
    public function markAlly($allyID, $colour, $showText=null) {
        $showText = (boolean) $showText;
        $showText = ($showText != null)?($showText):(false);
        
        $this->ally[] = array(
            "id" => (int) $allyID,
            "colour" => array((int) $colour[0], (int) $colour[1], (int) $colour[2]),
            "showText" => $showText,
        );
        return $this;
    }
    
    public function markPlayer($playerID, $colour, $showText=null) {
        $showText = (boolean) $showText;
        $showText = ($showText != null)?($showText):(false);
        
        $this->player[] = array(
            "id" => (int) $playerID,
            "colour" => array((int) $colour[0], (int) $colour[1], (int) $colour[2]),
            "showText" => $showText,
        );
        return $this;
    }
    
    public function markVillage($villageID, $colour, $showText=null) {
        $showText = (boolean) $showText;
        $showText = ($showText != null)?($showText):(false);
        
        $this->village[] = array(
            "id" => (int) $villageID,
            "colour" => array((int) $colour[0], (int) $colour[1], (int) $colour[2]),
            "showText" => $showText,
        );
        return $this;
    }

    public function setSkin($skin) {
        if(ctype_alnum($skin)) {
            $this->skin = $skin;
            return $this;
        }
        else if($this->show_errs) {
            throw new InvalidArgumentException("Only alphanumeric Characters inside Skin allowed");
        }
        return false;
    }
    
    public function setOpaque($opaque) {
        $opaque = (float) $opaque;
        if($opaque >= 0 && $opaque <= 100) {
            $this->opaque = $opaque;
            return $this;
        }
        else if($this->show_errs) {
            throw new InvalidArgumentException("Opaque needs to be between 0 and 100");
        }
        return false;
    }
    
    public function setLayerOrder($layerOrder) {
        $retval = true;
        $tmp = array();
        foreach($layerOrder as $layer) {
            if($layer == MapGenerator::$LAYER_MARK ||
                    $layer == MapGenerator::$LAYER_PICTURE ||
                    $layer == MapGenerator::$LAYER_TEXT) {
                $tmp[] = $layer;
            }
            else if($this->show_errs) {
                throw new InvalidArgumentException("Each Layer needs to be a valid Layer");
            }
            else {
                $retval = false;
            }
        }
        $this->layerOrder = $tmp;
        
        if($retval == false) return false;
        return $this;
    }
    
    public function setMapDimensions($xStart, $yStart, $xEnd, $yEnd) {
        $this->mapDimension = array(
            (int) $xStart,
            (int) $yStart,
            (int) $xEnd,
            (int) $yEnd,
        );
        return $this;
    }
    
    public function setPlayerColour($r, $g, $b) {
        $this->playerColour = array((int) $r, (int) $g, (int) $b);
        return $this;
    }
    
    public function setBarbarianColour($r, $g, $b) {
        $this->barbarianColour = array((int) $r, (int) $g, (int) $b);
        return $this;
    }
}