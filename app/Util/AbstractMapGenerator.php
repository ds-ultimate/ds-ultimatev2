<?php

namespace App\Util;

use App\Village;

abstract class AbstractMapGenerator extends PictureRender {
    private $font;
    private $show_errs;
    
    protected $ally;
    protected $player;
    protected $village;
    private $skin;
    private $opaque;
    
    
    protected $playerColour;
    public static $DEFAULT_PLAYER_COLOUR = [130, 60, 10];
    protected $barbarianColour;
    public static $DEFAULT_BARBARIAN_COLOUR = [150, 150, 150];
    private $backgroundColour;
    public static $DEFAULT_BACKGROUND_COLOUR = [88, 118, 27];
    private $gridColour;
    public static $DEFAULT_GRID_COLOUR = [0, 0, 0, 30]; //ca 30% deckkraft
    
    protected $mapDimension;
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
    protected $autoResize;
    private $markerFactor;
        
    public static $LAYER_MARK = 0;
    public static $LAYER_PICTURE = 1;
    public static $LAYER_TEXT = 2;
    public static $LAYER_GRID = 3;
    public static $LAYER_DRAWING = 4;
    private $layerOrder;

    private $drawing_img = null;
    private $drawing_dimensions = null;
    
    private $showContinentNumbers = true;
    
    public static $ANCHOR_TOP_LEFT = 1;
    public static $ANCHOR_TOP_RIGHT = 2;
    public static $ANCHOR_BOTTOM_LEFT = 3;
    public static $ANCHOR_BOTTOM_RIGHT = 4;
    public static $ANCHOR_MID_MID = 5;
    
    /*
     * Variables that are holding Data got from Database
     */
    protected $dataAlly;
    protected $dataPlayer;
    protected $dataVillage;
    
    public function __construct($dim=null, $show_errs=false) {
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

        $this -> setFont(public_path("/fonts/NotoMono-Regular.ttf"));
        $this -> image = $image;
        $this -> width= $img_width;
        $this -> height = $img_height;
        $this -> show_errs = $show_errs;
        
        $this -> ally = array();
        $this -> player = array();
        $this -> village = array();
        $this -> setSkin('symbol');
        $this ->setLayerOrder(array(
            AbstractMapGenerator::$LAYER_MARK,
            AbstractMapGenerator::$LAYER_PICTURE,
            AbstractMapGenerator::$LAYER_TEXT,
            AbstractMapGenerator::$LAYER_GRID,
        ));
        $this -> setOpaque(100);
        $this -> setMapDimensions(AbstractMapGenerator::$DEFAULT_DIMENSIONS);
        $this -> setPlayerColour(AbstractMapGenerator::$DEFAULT_PLAYER_COLOUR);
        $this -> setBarbarianColour(AbstractMapGenerator::$DEFAULT_BARBARIAN_COLOUR);
        $this -> setBackgroundColour(AbstractMapGenerator::$DEFAULT_BACKGROUND_COLOUR);
        $this -> setAutoResize(false);
        $this -> setMarkerFactor(0.2);
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
                case AbstractMapGenerator::$LAYER_MARK:
                    $this->renderMarks('barb');
                    $this->renderMarks('play');
                    $this->renderMarks('mark');
                    break;
                
                case AbstractMapGenerator::$LAYER_PICTURE:
                    $this->renderPictures('barb');
                    $this->renderPictures('play');
                    $this->renderPictures('mark');
                    break;
                
                case AbstractMapGenerator::$LAYER_TEXT:
                    $this->renderText();
                    break;
                
                case AbstractMapGenerator::$LAYER_GRID:
                    $this->renderGrid();
                    break;
                    
                case AbstractMapGenerator::$LAYER_DRAWING:
                    $this->renderDrawing();
                    break;
            }
        }
        
        $renderTime = round(microtime(true) - $startTime, 3);
        if($this->show_errs) {
            $white = imagecolorallocate($this->image, 255, 255, 255);
            $box1 = imagettfbbox(10, 0, $this->font, "@Debug:");
            $box2 = imagettfbbox(10, 0, $this->font, "Render time: {$renderTime}s");
            $x = $this->width - max($box1[2], $box2[2]);
            $y2 = $this->height - $box2[1] + $box2[5];
            $y1 = $y2 - $box1[1] + $box1[5];
            imagettftext($this->image, 10, 0, $x, $y1, $white, $this->font, "@Debug:");
            imagettftext($this->image, 10, 0, $x, $y2, $white, $this->font, "Render time: {$renderTime}s");
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
    
    protected abstract function grabAlly();
    
    protected abstract function grabPlayer();
    
    protected abstract function grabVillage();
    
    private function renderMarks($type) {
        foreach($this->dataVillage[$type] as $village) {
            if($village['colour'] == null) {
                continue;
            }
            $col = imagecolorallocatealpha($this->image, $village['colour'][0], $village['colour'][1], $village['colour'][2], 127-$this->opaque*127/100);
            
            if($type == 'mark' && $village['highLight']) {
                $col2 = imagecolorallocatealpha($this->image, $village['colour'][0], $village['colour'][1], $village['colour'][2], 127-($this->opaque*0.75)*127/100);

                $x = $this->fieldWidth * ($village['x'] - $this->mapDimension['xs'] + 0.5);
                $y = $this->fieldHeight * ($village['y'] - $this->mapDimension['ys'] + 0.5);
                imagefilledellipse($this->image, intval($x), intval($y), intval(max($this->fieldWidth*3-1, 0)), intval(max($this->fieldHeight*3-1, 0)), $col2);
            }
            
            $x = $this->fieldWidth * ($village['x'] - $this->mapDimension['xs']);
            $y = $this->fieldHeight * ($village['y'] - $this->mapDimension['ys']);
            
            $factor = $this->markerFactor/2;
            $indentWs = intval($this->fieldWidth*$factor + 0.5);
            $indentWe = intval($this->fieldWidth*$factor);
            $indentHs = intval($this->fieldHeight*$factor + 0.5);
            $indentHe = intval($this->fieldHeight*$factor);
            
            $xs = intval($x + $indentWs);
            $xe = intval($x + max($this->fieldWidth-$indentWe-1, $indentWs));
            $ys = intval($y + $indentHs);
            $ye = intval($y + max($this->fieldHeight-$indentHe-1, $indentHs));
            imagefilledrectangle($this->image, $xs, $ys, $xe, $ye, $col);
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
        $gridColBig = imagecolorallocatealpha($this->image, $this->gridColour[0],
                $this->gridColour[1], $this->gridColour[2], 0); //ca 100% deckkraft
        $gridColSmall = imagecolorallocatealpha($this->image, $this->gridColour[0],
                $this->gridColour[1], $this->gridColour[2], 90); //ca 20% deckkraft
        $gridColText = imagecolorallocatealpha($this->image, $this->gridColour[0],
                $this->gridColour[1], $this->gridColour[2], 90); //ca 30% deckkraft
        
        $thick = intval($this->fieldWidth / 10);
        
        //draw vertical grid
        for($i = 1; $i <= 9; $i++) { //draw 9 lines (from 1 to 9)
            $x = intval($this->fieldWidth * ($i*100 - $this->mapDimension['xs']));
            if($x < 0 || $x > $this->width) continue; //ignore lines outside border
            
            imagefilledrectangle($this->image, $x-$thick, 0, $x+$thick, $this->height, $gridColBig);
        }
        
        //draw horizontal grid
        for($i = 1; $i <= 9; $i++) { //draw 9 lines (from 1 to 9)
            $y = intval($this->fieldHeight * ($i*100 - $this->mapDimension['ys']));
            if($y < 0 || $y > $this->height) continue; //ignore lines outside border
            
            imagefilledrectangle($this->image, 0, $y-$thick, $this->width, $y+$thick, $gridColBig);
        }
        
        if($this->showContinentNumbers) {
            for($i = 0; $i <= 9; $i++) {
                for($j = 0; $j <= 9; $j++) {
                    $txt = "K$j$i";
                    $size = $this->fieldWidth * 10;
                    $box = imagettfbbox($size, 0, $this->font, $txt);

                    $xwanted = intval($this->fieldWidth * (($i+1)*100 - $this->mapDimension['xs']));
                    $ywanted = intval($this->fieldHeight * (($j+1)*100 - $this->mapDimension['ys']));

                    $x = $xwanted - $box[2];
                    $y = $ywanted - $box[1];
                    imagettftext($this->image, $size, 0, $x, $y, $gridColText, $this->font, $txt);
                }
            }
        }
        
        if($this->fieldWidth / 4 >= 1) {
            
            //draw vertical grid
            for($i = 1; $i <= 99; $i++) { //draw 99 lines (from 1 to 99)
                if($i % 10 == 0) continue;
                $x = intval($this->fieldWidth * ($i*10 - $this->mapDimension['xs']));
                if($x < 0 || $x > $this->width) continue; //ignore lines outside border

                imagefilledrectangle($this->image, $x, 0, $x, $this->height, $gridColSmall);
            }

            //draw horizontal grid
            for($i = 1; $i <= 99; $i++) { //draw 99 lines (from 1 to 99)
                if($i % 10 == 0) continue;
                $y = intval($this->fieldHeight * ($i*10 - $this->mapDimension['ys']));
                if($y < 0 || $y > $this->height) continue; //ignore lines outside border

                imagefilledrectangle($this->image, 0, $y, $this->width, $y, $gridColSmall);
            }
        }
    }
    
    private function renderDrawing() {
        try {
            $drawing_img=imagecreatefromstring($this->drawing_img);
            
            imagecopyresampled($this->image, $drawing_img, 0, 0, 0, 0, $this->width, $this->height, imagesx($drawing_img), imagesy($drawing_img));
        } catch (\ErrorException $ex) {
            BasicFunctions::local();
            $fg = imagecolorallocate($this->image, 255, 255, 255);
            $this->renderCenteredText($this->width / 2, $this->height / 2, __('tool.map.drawingErr'), $fg);
        }
    }
    
    private function renderText() {
        $white = imagecolorallocate($this->image, 255, 255, 255);
        $black = imagecolorallocate($this->image, 0, 0, 0);
        foreach($this->dataAlly as $ally) {
            if(!$ally['showText']) continue;
            if($ally['villNum'] <= 0) continue;
            
            $x = $ally['villX'] / $ally['villNum'];
            $y = $ally['villY'] / $ally['villNum'];
            $color = imagecolorallocate($this->image, $ally['colour'][0], $ally['colour'][1], $ally['colour'][2]);
            if($ally['colour'][0] + $ally['colour'][1] + $ally['colour'][2] > 600) {
                //use black shadow for bright colors
                $this->renderShadowedCenteredText($x, $y, $ally['tag'], $color, $black);
            } else {
                $this->renderShadowedCenteredText($x, $y, $ally['tag'], $color, $white);
            }
        }
        
        foreach($this->dataPlayer as $player) {
            if(!$player['showText']) continue;
            if($player['villNum'] <= 0) continue;
            
            $x = $player['villX'] / $player['villNum'];
            $y = $player['villY'] / $player['villNum'];
            $color = imagecolorallocate($this->image, $player['colour'][0], $player['colour'][1], $player['colour'][2]);
            $this->renderShadowedCenteredText($x, $y, $player['name'], $color, $white);
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
        $size = $this->width / 50 + $this->fieldWidth * 4;
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
    
    
    /**
     * Fuciton for rendering aligned Text into the map
     * Anchor is used to define ref point of text box
     * 0, 0 will always be displayed top left
     */
    public function renderAlignedText($anchor, $relX, $relY, $size, $text, $color) {
        if($color[0] > 255 || $color[0] < 0 ||
                $color[1] > 255 || $color[1] < 0 ||
                $color[2] > 255 || $color[2] < 0) {
            throw new \InvalidArgumentException("Invalid color given");
        }
        
        $col = imagecolorallocate($this->image, (int) $color[0], (int) $color[1], (int) $color[2]);
        $box = imagettfbbox($size, 0, $this->font, $text);
        
        switch ($anchor) {
            case self::$ANCHOR_BOTTOM_LEFT:
                $x = ((int) $relX) - $box[0];
                $y = ((int) $relY) - $box[1];
                break;
            case self::$ANCHOR_BOTTOM_RIGHT:
                $x = ((int) $relX) - $box[2];
                $y = ((int) $relY) - $box[3];
                break;
            case self::$ANCHOR_TOP_RIGHT:
                $x = ((int) $relX) - $box[4];
                $y = ((int) $relY) - $box[5];
                break;
            case self::$ANCHOR_TOP_LEFT:
                $x = ((int) $relX) - $box[6];
                $y = ((int) $relY) - $box[7];
                break;
            case self::$ANCHOR_MID_MID:
                $x = ((int) $relX) - ($box[0] + $box[4]) / 2;
                $y = ((int) $relY) - ($box[1] + $box[5]) / 2;
                break;
            default:
                throw new \InvalidArgumentException("Invalid anchor given");
        }
        
        imagettftext($this->image, $size, 0, $x, $y, $col, $this->font, $text);
    }
    
    public function markAlly($allyID, $colour, $showText=null, $highLight=null) {
        $showText = (boolean) $showText;
        $showText = ($showText != null)?($showText):(false);
        $highLight = (boolean) $highLight;
        $highLight = ($highLight != null)?($highLight):(false);
        
        $this->ally[] = array(
            "id" => (int) $allyID,
            "colour" => array((int) $colour[0], (int) $colour[1], (int) $colour[2]),
            "showText" => $showText,
            "highLight" => $highLight,
            "villNum" => 0,
            "villX" => 0,
            "villY" => 0,
        );
        return $this;
    }
    
    public function markPlayer($playerID, $colour, $showText=null, $highLight=null) {
        $showText = (boolean) $showText;
        $showText = ($showText != null)?($showText):(false);
        
        $this->player[] = array(
            "id" => (int) $playerID,
            "colour" => array((int) $colour[0], (int) $colour[1], (int) $colour[2]),
            "showText" => $showText,
            "highLight" => $highLight,
            "villNum" => 0,
            "villX" => 0,
            "villY" => 0,
        );
        return $this;
    }
    
    public function markVillage($villageID, $colour, $showText=null, $highLight=null) {
        $showText = (boolean) $showText;
        $showText = ($showText != null)?($showText):(false);
        $highLight = (boolean) $highLight;
        $highLight = ($highLight != null)?($highLight):(false);
        
        $this->village[] = array(
            "id" => (int) $villageID,
            "colour" => array((int) $colour[0], (int) $colour[1], (int) $colour[2]),
            "showText" => $showText,
            "highLight" => $highLight,
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
    
    public function setLayerOrder($layerOrder) {
        $retval = true;
        $tmp = array();
        foreach($layerOrder as $layer) {
            if($layer == AbstractMapGenerator::$LAYER_MARK ||
                    $layer == AbstractMapGenerator::$LAYER_PICTURE ||
                    $layer == AbstractMapGenerator::$LAYER_TEXT ||
                    $layer == AbstractMapGenerator::$LAYER_GRID ||
                    ($layer == AbstractMapGenerator::$LAYER_DRAWING && $this->drawing_img != null && $this->drawing_img != "")) {
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
    public function getMapDimensions() {
        return $this->mapDimension;
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
    
    public function setMarkerFactor($factor) {
        if($factor > 1 || $factor < 0) {
            throw new \InvalidArgumentException("Factor can only be between 0 and 1");
        }
        
        $this->markerFactor = $factor;
        return $this;
    }
    
    public function setShowContinentNumbers($showContinentNumbers) {
        $this->showContinentNumbers = $showContinentNumbers;
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