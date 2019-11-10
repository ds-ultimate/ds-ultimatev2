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
    private $highlight;
    
    
    private $playerColour;
    public static $DEFAULT_PLAYER_COLOUR = [130, 60, 10];
    private $barbarianColour;
    public static $DEFAULT_BARBARIAN_COLOUR = [150, 150, 150];
    private $backgroundColour;
    public static $DEFAULT_BACKGROUND_COLOUR = [88, 118, 27];
    private $gridColour;
    public static $DEFAULT_GRID_COLOUR = [0, 0, 0, 30]; //ca 30% deckkraft
    
    private $mapDimension;
    public static $DEFAULT_DIMENSIONS = [
        'xs' => 0,
        'xe' => 1000,
        'ys' => 0,
        'ye' => 1000,
    ];
    
    private $width;
    private $height;
    private $fieldWidth;
    private $fieldHeight;
    private $autoResize;
        
    public static $LAYER_MARK = 0;
    public static $LAYER_PICTURE = 1;
    public static $LAYER_TEXT = 2;
    public static $LAYER_GRID = 3;
    public static $LAYER_DRAWING = 4;
    private $layerOrder;

    private $drawing_img = null;
    private $drawing_dimensions = null;
    
    /*
     * Variables that are holding Data got from Database
     */
    private $dataAlly;
    private $dataPlayer;
    private $dataVillage;
    
    public function __construct(World $world, $dim=null, $show_errs=false) {
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
        imagesavealpha($image, true);
        
        if($image === false) die("Error");

        $this -> setFont("fonts/NotoMono-Regular.ttf");
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
            MapGenerator::$LAYER_GRID,
        ));
        $this -> setOpaque(100);
        $this -> setHighlight(false);
        $this -> setMapDimensions(MapGenerator::$DEFAULT_DIMENSIONS);
        $this -> setPlayerColour(MapGenerator::$DEFAULT_PLAYER_COLOUR);
        $this -> setBarbarianColour(MapGenerator::$DEFAULT_BARBARIAN_COLOUR);
        $this -> setBackgroundColour(MapGenerator::$DEFAULT_BACKGROUND_COLOUR);
        $this -> setAutoResize(false);
    }

    public function render() {
        $startTime = microtime(true);
        $this->grabInformation();
        
        $this->fieldWidth = $this->width / $this->mapDimension['w'];
        $this->fieldHeight = $this->height / $this->mapDimension['h'];
        
        if(count($this->backgroundColour) > 3)
            $colour_bg = imagecolorallocatealpha($this->image, $this->backgroundColour[0], $this->backgroundColour[1], $this->backgroundColour[2], $this->backgroundColour[3]);
        else
            $colour_bg = imagecolorallocate($this->image, $this->backgroundColour[0], $this->backgroundColour[1], $this->backgroundColour[2]);
        
        imagefill($this->image, 0, 0, $colour_bg);
        
        foreach($this->layerOrder as $layer) {
            switch ($layer) {
                case MapGenerator::$LAYER_MARK:
                    $this->renderMarks('barb');
                    $this->renderMarks('play');
                    $this->renderMarks('mark');
                    break;
                
                case MapGenerator::$LAYER_PICTURE:
                    $this->renderPictures('barb');
                    $this->renderPictures('play');
                    $this->renderPictures('mark');
                    break;
                
                case MapGenerator::$LAYER_TEXT:
                    $this->renderText();
                    break;
                
                case MapGenerator::$LAYER_GRID:
                    $this->renderGrid();
                    break;
                    
                case MapGenerator::$LAYER_DRAWING:
                    $this->renderDrawing();
                    break;
            }
        }
        
        $renderTime = round(microtime(true) - $startTime, 3);
        if($this->show_errs) {
            $white = imagecolorallocate($this->image, 255, 255, 255);
            $box1 = imagettfbbox(10, 0, $this->font, "@Debug:");
            $box2 = imagettfbbox(10, 0, $this->font, "Render time: " . $renderTime . "s");
            $x = $this->width - max($box1[2], $box2[2]);
            $y2 = $this->height - $box2[1] + $box2[5];
            $y1 = $y2 - $box1[1] + $box1[5];
            imagettftext($this->image, 10, 0, $x, $y1, $white, $this->font, "@Debug:");
            imagettftext($this->image, 10, 0, $x, $y2, $white, $this->font, "Render time: " . $renderTime . "s");
        }
    }
    
    private function grabInformation() {
        $this->dataAlly = array();
        $this->dataPlayer = array();
        $this->dataVillage = array('barb' => [], 'play' => [], 'mark' => []);
        
        $this->grabAlly();
        $this->grabPlayer();
        $this->grabVillage();
        
        //Finalize Ally grabbing
        foreach($this->dataPlayer as $player) {
            if(!isset($player['allyID'])) continue;
            
            $this->dataAlly[$player['allyID']]['villNum'] += $player['villNum'];
            $this->dataAlly[$player['allyID']]['villX'] += $player['villX'];
            $this->dataAlly[$player['allyID']]['villY'] += $player['villY'];
        }
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
                    $this->dataAlly[$ally->allyID]['name'] = BasicFunctions::decodeName($ally->name);
                    $this->dataAlly[$ally->allyID]['tag'] = BasicFunctions::decodeName($ally->tag);
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
                    "name" => BasicFunctions::decodeName($player->name),
                    "showText" => false,
                    "allyID" => $player->ally_id,
                    "villNum" => 0,
                    "villX" => 0,
                    "villY" => 0,
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
                $this->dataPlayer[$player['id']] = $player;
                
                if($player['showText']) {
                    $playerModel = $playerModel->orWhere('allyID', $player['id']);
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
    
    private function grabVillage() {
        //DO NOT use Laravel functions here as they use way more memory than this code
        $tableName = "`".BasicFunctions::getDatabaseName($this->world->server->code, $this->world->name).'`.`village_latest`';
        $res = DB::select("SELECT name, x, y, points, bonus_id, villageID, owner FROM $tableName WHERE".
                " x >= ".intval($this->mapDimension['xs']).
                " AND y >= ".intval($this->mapDimension['ys']).
                " AND x < ".intval($this->mapDimension['xe']).
                " AND y < ".intval($this->mapDimension['ye']));
        
        $tmpVillage = array();
        foreach($this->village as $village) {
            $tmpVillage[$village['id']] = $village;
        }
        
        $realSize = null;
        foreach($res as $village) {
            if($realSize == null) {
                $realSize = [
                    'xs' => $village->x,
                    'ys' => $village->y,
                    'xe' => $village->x,
                    'ye' => $village->y,
                ];
            } else {
                if($village->x < $realSize['xs']) $realSize['xs'] = $village->x;
                if($village->x > $realSize['xe']) $realSize['xe'] = $village->x;
                if($village->y < $realSize['ys']) $realSize['ys'] = $village->y;
                if($village->y > $realSize['ye']) $realSize['ye'] = $village->y;
            }
            
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
                    "marked" => false,
                );
                
                if (isset($this->dataPlayer[$village->owner]) && $village->owner != 0) {
                    //For player / ally markers
                    $this->dataVillage['mark'][$village->villageID] = $tmp;
                    $this->dataVillage['mark'][$village->villageID]['colour'] =
                            $this->dataPlayer[$village->owner]['colour'];
                    $this->dataVillage['mark'][$village->villageID]['marked'] = true;
                    
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
    
    private function renderMarks($type) {
        foreach($this->dataVillage[$type] as $village) {
            if($village['colour'] == null) {
                continue;
            }
            $col = imagecolorallocatealpha($this->image, $village['colour'][0], $village['colour'][1], $village['colour'][2], 127-$this->opaque*127/100);
            
            if($type == 'mark' && $this->highlight) {
                $col2 = imagecolorallocatealpha($this->image, $village['colour'][0], $village['colour'][1], $village['colour'][2], 127-($this->opaque*0.75)*127/100);

                $x = $this->fieldWidth * ($village['x'] - $this->mapDimension['xs'] + 0.5);
                $y = $this->fieldHeight * ($village['y'] - $this->mapDimension['ys'] + 0.5);
                imagefilledellipse($this->image, intval($x), intval($y), intval(max($this->fieldWidth*3-1, 0)), intval(max($this->fieldHeight*3-1, 0)), $col2);
            }
            
            $x = $this->fieldWidth * ($village['x'] - $this->mapDimension['xs']);
            $y = $this->fieldHeight * ($village['y'] - $this->mapDimension['ys']);
            imagefilledrectangle($this->image, intval($x), intval($y), intval($x + max($this->fieldWidth-1, 0)), intval($y + max($this->fieldHeight-1, 0)), $col);
        }
    }
    
    private function renderPictures($type) {
        /*
         * https://www.php.net/manual/de/function.imagecreatefrompng.php
         *  imagecopyresampled ( resource $dst_image , resource $src_image , int $dst_x , int $dst_y , int $src_x , int $src_y , int $dst_w , int $dst_h , int $src_w , int $src_h ) : bool
         * https://www.php.net/manual/de/function.imagecopyresampled.php
         *  imagecreatefrompng ( string $filename ) : resource
         * 
         */
        
        foreach($this->dataVillage[$type] as $village) {
            if($village['colour'] == null) {
                continue;
            }
            $skinImg = $this->getSkinImage(Village::getSkinImageName($village['owner'], $village['points'], $village['bonus_id']));
            
            $x = $this->fieldWidth * ($village['x'] - $this->mapDimension['xs']);
            $y = $this->fieldHeight * ($village['y'] - $this->mapDimension['ys']);
            imagecopyresampled($this->image, $skinImg['img'], intval($x), intval($y), 0, 0, max(intval($this->fieldWidth),1), max(intval($this->fieldHeight),1), $skinImg['x'], $skinImg['y']);
        }
    }
    
    private function renderGrid() {
        $gridCol = imagecolorallocatealpha($this->image, $this->gridColour[0],
                $this->gridColour[1], $this->gridColour[2], 90); //ca 30% deckkraft
        
        //draw vertical grid
        for($i = 1; $i <= 9; $i++) { //draw 9 lines (from 1 to 9)
            $x = intval($this->fieldWidth * ($i*100 - $this->mapDimension['xs']));
            if($x < 0 || $x > $this->width) continue; //ignore lines outside border
            
            imageline($this->image, $x, 0, $x, $this->height, $gridCol);
        }
        
        //draw horizontal grid
        for($i = 1; $i <= 9; $i++) { //draw 9 lines (from 1 to 9)
            $y = intval($this->fieldHeight * ($i*100 - $this->mapDimension['ys']));
            if($y < 0 || $y > $this->height) continue; //ignore lines outside border
            
            imageline($this->image, 0, $y, $this->width, $y, $gridCol);
        }
        
        for($i = 0; $i <= 9; $i++) {
            for($j = 0; $j <= 9; $j++) {
                $txt = "K$j$i";
                $size = $this->fieldWidth * 10;
                $box = imagettfbbox($size, 0, $this->font, $txt);
                
                $xwanted = intval($this->fieldWidth * (($i+1)*100 - $this->mapDimension['xs']));
                $ywanted = intval($this->fieldHeight * (($j+1)*100 - $this->mapDimension['ys']));
                
                $x = $xwanted - $box[2];
                $y = $ywanted - $box[1];
                imagettftext($this->image, $size, 0, $x, $y, $gridCol, $this->font, $txt);
            }
        }
    }
    
    private function renderDrawing() {
        $drawing_img=imagecreatefromstring($this->drawing_img);
        
        imagecopyresampled($this->image, $drawing_img, 0, 0, 0, 0, $this->width, $this->height, imagesx($drawing_img), imagesy($drawing_img));
    }
    
    private function renderText() {
        $white = imagecolorallocate($this->image, 255, 255, 255);
        foreach($this->dataAlly as $ally) {
            if(!$ally['showText']) continue;
            if($ally['villNum'] <= 0) continue;
            
            $x = $ally['villX'] / $ally['villNum'];
            $y = $ally['villY'] / $ally['villNum'];
            $color = imagecolorallocate($this->image, $ally['colour'][0], $ally['colour'][1], $ally['colour'][2]);
            $this->renderShadowedCenteredText($x, $y, $ally['name'], $color, $white);
        }
        
        foreach($this->dataPlayer as $player) {
            if(!$player['showText']) continue;
            if($player['villNum'] <= 0) continue;
            
            $x = $player['villX'] / $player['villNum'];
            $y = $player['villY'] / $player['villNum'];
            $this->renderCenteredText($x, $y, $player['name'], $player['colour']);
        }
        
        //TODO add for village Markers
    }
    
    /*
     * This function uses Map Coordinates not Picture!!
     */
    private function renderShadowedCenteredText($mapX, $mapY, $text, $col, $shadowCol) {
        $x = ($mapX - $this->mapDimension['xs']) * $this->width / $this->mapDimension['w'];
        $y = ($mapY - $this->mapDimension['ys']) * $this->height / $this->mapDimension['h'];
        
        $this->renderCenteredText($x - 1, $y - 1, $text, $shadowCol);
        $this->renderCenteredText($x - 1, $y + 1, $text, $shadowCol);
        $this->renderCenteredText($x + 1, $y - 1, $text, $shadowCol);
        $this->renderCenteredText($x + 1, $y + 1, $text, $shadowCol);
        $this->renderCenteredText($x, $y, $text, $col);
    }
    
    /*
     * This function uses Picture coordinates
     */
    private function renderCenteredText($x, $y, $text, $color) {
        $size = $this->fieldWidth * 5;
        $box = imagettfbbox($size, 0, $this->font, $text);

        $drawX = $x - ($box[6] + $box[2]) / 2;
        $drawY = $y - ($box[7] + $box[3]) / 2;
        imagettftext($this->image, $size, 0, $drawX, $drawY, $color, $this->font, $text);
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
            "villNum" => 0,
            "villX" => 0,
            "villY" => 0,
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
            "villNum" => 0,
            "villX" => 0,
            "villY" => 0,
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
            throw new \InvalidArgumentException("Only alphanumeric Characters inside Skin allowed");
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
            throw new \InvalidArgumentException("Opaque needs to be between 0 and 100");
        }
        return false;
    }
    
    public function setHighlight($highlight) {
        $this->highlight = ($highlight === true)?(true):(false);
        return $this;
    }
    
    public function setLayerOrder($layerOrder) {
        $retval = true;
        $tmp = array();
        foreach($layerOrder as $layer) {
            if($layer == MapGenerator::$LAYER_MARK ||
                    $layer == MapGenerator::$LAYER_PICTURE ||
                    $layer == MapGenerator::$LAYER_TEXT ||
                    $layer == MapGenerator::$LAYER_GRID ||
                    ($layer == MapGenerator::$LAYER_DRAWING && $this->drawing_img != null && $this->drawing_img != "")) {
                $tmp[] = $layer;
            }
            else if($this->show_errs) {
                throw new \InvalidArgumentException("Each Layer needs to be a valid Layer");
            }
            else {
                $retval = false;
            }
        }
        $this->layerOrder = $tmp;
        
        if($retval == false) return false;
        return $this;
    }
    
    /**
     * Set shown koordinate range of the map
     * 
     * @param type $dimensions
     * Requires an array containing:
     * xs: First Column (x Position) to be shown
     * ys: First Row (y Position) to be shown
     * xe: Column (x Position) where the map should end (exclusive)
     * ye: Row (y Position) where the map should end (exclusive)
     */
    public function setMapDimensions($dimensions) {
        $tmp = $this->convertToInternalDimensions($dimensions);
        if($tmp === false) return false;
        
        $this->mapDimension = $tmp;
        return $this;
    }
    
    public function setPlayerColour($col) {
        if($col == null) {
            $this->playerColour = null;
        } else {
            $this->playerColour = array((int) $col[0], (int) $col[1], (int) $col[2]);
        }
        return $this;
    }
    
    public function setBarbarianColour($col) {
        if($col == null) {
            $this->barbarianColour = null;
        } else {
            $this->barbarianColour = array((int) $col[0], (int) $col[1], (int) $col[2]);
        }
        return $this;
    }
    
    public function setBackgroundColour($col) {
        if($col == null) {
            return false;
        } else {
            $this->backgroundColour = array((int) $col[0], (int) $col[1], (int) $col[2]);
            if(count($col) > 3)
                $this->backgroundColour[] = (int) $col[3];
        }
        return $this;
    }
    
    public function setFont($font) {
        $this->font = realpath($font);
        return $this;
    }
    
    public function setAutoResize($value) {
        $this->autoResize = (boolean) $value;
        return $this;
    }
    
    public function setDrawings($drawing_img, $drawing_dim) {
        $this->drawing_img = $drawing_img;
        
        $tmp = $this->convertToInternalDimensions($drawing_dim);
        if($tmp === false) return false;
        
        $this->drawing_dimensions = $tmp;
        return $this;
    }
    
    private function convertToInternalDimensions($dimensions) {
        $tmp = array(
            'xs' => (int) $dimensions['xs'],
            'ys' => (int) $dimensions['ys'],
            'xe' => (int) $dimensions['xe'],
            'ye' => (int) $dimensions['ye'],
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
        
        return $tmp;
    }
}