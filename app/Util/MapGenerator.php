<?php

namespace App\Util;

use App\Ally;
use App\Player;
use App\Village;
use App\World;
use Illuminate\Support\Facades\DB;

class MapGenerator extends PictureRender {
    private $font;
    private $show_errs;
    private $world;
    
    private $ally;
    private $player;
    private $village;
    private $skin;
    private $opaque;
    private $playerColour;
    private $barbarianColour;
    private $backgroundColour;
    
    private $width;
    private $height;
    private $mapDimension;
    private $fieldWidth;
    private $fieldHeight;
        
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
        $this -> setOpaque(100);
        $this -> setMapDimensions(0, 0, 1000, 1000);
        $this -> setPlayerColour(51, 23, 4);
        $this -> setBarbarianColour(179, 174, 167);
        $this -> setBackgroundColour(34, 46, 10);
    }

    public function render() {
        $startTime = microtime(true);
        $this->grabInformation();
        
        $this->fieldWidth = $this->width / $this->mapDimension['w'];
        $this->fieldHeight = $this->height / $this->mapDimension['h'];
        
        $colour_bg = imagecolorallocate($this->image, $this->backgroundColour[0], $this->backgroundColour[1], $this->backgroundColour[2]);
        imagefill($this->image, 0, 0, $colour_bg);
        
        foreach($this->layerOrder as $layer) {
            switch ($layer) {
                case MapGenerator::$LAYER_MARK:
                    $this->renderMarks();
                    break;
                case MapGenerator::$LAYER_PICTURE:
                    $this->renderPictures();
                    break;
                case MapGenerator::$LAYER_TEXT:
                    $this->renderText();
                    break;
            }
        }
        
        $renderTime = microtime(true) - $startTime;
        $white = imagecolorallocate($this->image, 255, 255, 255);
        imagettftext($this->image, 10, 0, $this->width-300, $this->height-100, $white, $this->font, "@Debug:");
        imagettftext($this->image, 10, 0, $this->width-300, $this->height-80, $white, $this->font, "Render time: " . $renderTime);
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
                    $this->dataAlly[$ally->allyID]['name'] = $ally->name;
                    $this->dataAlly[$ally->allyID]['tag'] = $ally->tag;
                }
            }
        }
        
        
        if(count($this->dataAlly) > 0) {
            $playerModel = new Player();
            $playerModel->setTable(BasicFunctions::getDatabaseName($this->world->server->code, $this->world->name).'.player_latest');
            foreach($this->dataAlly as $ally) {
                $playerModel = $playerModel->orWhere('ally_id', $ally['id']);
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
            $evaluateModel = false;
            
            foreach($this->player as $player) {
                if($player['showText']) {
                    $playerModel = $playerModel->orWhere('allyID', $player['id']);
                    $evaluateModel = true;
                }
                $this->dataPlayer[$player['id']] = $player;
            }

            if($evaluateModel) {
                foreach($playerModel->get() as $player) {
                    $this->dataPlayer[$player->playerID]['name'] = $player->name;
                }
            }
        }
    }
    
    private function grabVillage() {
        //DO NOT use Laravel functions here as they use way more memory than this code
        $tableName = "`".BasicFunctions::getDatabaseName($this->world->server->code, $this->world->name).'`.`village_latest`';
        $res = DB::select("SELECT name, x, y, points, bonus_id, villageID, owner FROM $tableName WHERE".
                " x >= ".intval($this->mapDimension['xs']).
                " AND y >= ".intval($this->mapDimension['ys']).
                " AND x < ".intval($this->mapDimension['xe']).
                " AND y < ".intval($this->mapDimension['ye']));
        
        foreach($this->village as $village) {
            $this->dataVillage[$village['id']] = $village;
        }

        foreach($res as $village) {
            if(isset($this->dataVillage[$village->villageID])) {
                //For real village markers
                $this->dataVillage[$village->villageID]['name'] = $village->name;
                $this->dataVillage[$village->villageID]['x'] = $village->x;
                $this->dataVillage[$village->villageID]['y'] = $village->y;
                $this->dataVillage[$village->villageID]['owner'] = $village->owner;
                $this->dataVillage[$village->villageID]['points'] = $village->points;
                $this->dataVillage[$village->villageID]['bonus_id'] = $village->bonus_id;
            } else {
                $this->dataVillage[$village->villageID] = array(
                    "id" => (int) $village->villageID,
                    "colour" => ($village->owner != 0)?($this->playerColour):($this->barbarianColour),
                    "name" => $village->name,
                    "x" => $village->x,
                    "y" => $village->y,
                    'owner' => $village->owner,
                    "points" => $village->points,
                    "bonus_id" => $village->bonus_id,
                    "showText" => false,
                );
                
                if (isset($this->dataPlayer[$village->owner]) && $village->owner != 0) {
                    //For player / ally markers
                    $this->dataVillage[$village->villageID]['colour'] =
                            $this->dataPlayer[$village->owner]['colour'];
                }
            }
            
            
        }
    }
    
    private function renderMarks() {
        foreach($this->dataVillage as $village) {
            if($village['colour'] == null) {
                continue;
            }
            $col = imagecolorallocatealpha($this->image, $village['colour'][0], $village['colour'][1], $village['colour'][2], 127-$this->opaque*127/100);
            
            $x = intval($this->fieldWidth * ($village['x'] - $this->mapDimension['xs']));
            $y = intval($this->fieldHeight * ($village['y'] - $this->mapDimension['ys']));
            imagefilledrectangle($this->image, $x, $y, intval($x + $this->fieldWidth - 1), intval($y + $this->fieldHeight - 1), $col);
        }
    }
    
    private function renderPictures() {
        /*
         * https://www.php.net/manual/de/function.imagecreatefrompng.php
         *  imagecopyresampled ( resource $dst_image , resource $src_image , int $dst_x , int $dst_y , int $src_x , int $src_y , int $dst_w , int $dst_h , int $src_w , int $src_h ) : bool
         * https://www.php.net/manual/de/function.imagecopyresampled.php
         *  imagecreatefrompng ( string $filename ) : resource
         * 
         */
        
        foreach($this->dataVillage as $village) {
            $skinImg = $this->getSkinImage(Village::getSkinImageName($village['owner'], $village['points'], $village['bonus_id']));
            
            $x = intval($this->fieldWidth * ($village['x'] - $this->mapDimension['xs']));
            $y = intval($this->fieldHeight * ($village['y'] - $this->mapDimension['ys']));
            imagecopyresampled($this->image, $skinImg['img'], $x, $y, 0, 0, $this->fieldWidth, $this->fieldHeight, $skinImg['x'], $skinImg['y']);
        }
    }
    
    private function renderText() {
        //TODO for future
    }
    
    private $skinCache = array();
    private function getSkinImage($name) {
        if(isset($this->skinCache[$name])) {
            return $this->skinCache[$name];
        }
        
        $this->skinCache[$name] = array('img' => imagecreatefrompng("images/".Village::getSkinImagePath($this->skin, $name)));
        $this->skinCache[$name]['x'] = imagesx($this->skinCache[$name]['img']);
        $this->skinCache[$name]['y'] = imagesy($this->skinCache[$name]['img']);
        $this->skinCache[$name]['asp'] = $this->skinCache[$name]['x'] / $this->skinCache[$name]['y'];
        
        return $this->skinCache[$name];
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
        $tmp = array(
            'xs' => (int) $xStart,
            'ys' => (int) $yStart,
            'xe' => (int) $xEnd,
            'ye' => (int) $yEnd,
        );
        
        $tmp['w'] = $tmp['xe'] - $tmp['xs'];
        $tmp['h'] = $tmp['ye'] - $tmp['ys'];
        if($tmp['w'] <= 0 || $tmp['h'] <= 0) {
            if($this->show_errs) {
                throw new \InvalidArgumentException("start / end need correct sorting or with / height is zero");
            } else {
                return false;
            }
        }
        
        $this->mapDimension = $tmp;
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
    
    public function setBackgroundColour($r, $g, $b) {
        $this->backgroundColour = array((int) $r, (int) $g, (int) $b);
        return $this;
    }
}