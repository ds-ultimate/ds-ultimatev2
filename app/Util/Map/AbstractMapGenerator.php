<?php

namespace App\Util\Map;

use App\Util\PictureRender;

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
    public static $DEFAULT_GRID_COLOUR = [0, 0, 0, 90];
    
    protected $mapDimension;
    public static $DEFAULT_DIMENSIONS = [
        'xs' => 0,
        'xe' => 1000,
        'ys' => 0,
        'ye' => 1000,
    ];
    
    private $width;
    private $height;
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
    
    public function __construct(AbstractSkinRenderer $skin, $dim=null, $show_errs=false) {
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

        $this->setFont(public_path("/fonts/NotoMono-Regular.ttf"));
        $this->image = $image;
        $this->width= $img_width;
        $this->height = $img_height;
        $this->show_errs = $show_errs;
        $this->skin = $skin;
        
        $this->ally = array();
        $this->player = array();
        $this->village = array();
        $this->setLayerOrder(array(
            static::$LAYER_MARK,
            static::$LAYER_PICTURE,
            static::$LAYER_TEXT,
            static::$LAYER_GRID,
        ));
        $this->setOpaque(100);
        $this->setMapDimensions(static::$DEFAULT_DIMENSIONS);
        $this->setPlayerColour(static::$DEFAULT_PLAYER_COLOUR);
        $this->setBarbarianColour(static::$DEFAULT_BARBARIAN_COLOUR);
        $this->setBackgroundColour(static::$DEFAULT_BACKGROUND_COLOUR);
        $this->setGridColour(static::$DEFAULT_GRID_COLOUR);
        $this->setAutoResize(false);
        $this->setMarkerFactor(0.2);
    }

    public function render() {
        $startTime = microtime(true);
        $this->grabInformation();
        
        $this->skin->render_init(
            mapDim: $this->mapDimension,
            bgCol: $this->backgroundColour,
            layerOrd: $this->layerOrder,
            datVil: $this->dataVillage,
            datAlly: $this->dataAlly,
            datPlayer: $this->dataPlayer,
            drawing: $this->drawing_img,
            showCNumb: $this->showContinentNumbers,
            font: $this->font,
            markFact: $this->markerFactor,
            opaque: $this->opaque,
            gCol: $this->gridColour,
        );
        $skinImage = $this->skin->render();
        
        imagefill($this->image, 0, 0, imagecolorallocatealpha($this->image, 0, 0, 0, 127));
        imagecopyresampled($this->image, $skinImage, 0, 0, 0, 0, $this->width, $this->height, imagesx($skinImage), imagesy($skinImage));
        
        $renderTime = round(microtime(true) - $startTime, 3);
        if($this->show_errs) {
            $white = imagecolorallocate($this->image, 255, 255, 255);
            $box1 = imagettfbbox(10, 0, $this->font, "@Debug:");
            $box2 = imagettfbbox(10, 0, $this->font, "Render time: {$renderTime}s");
            $x = $this->width - max($box1[2], $box2[2]) - 10;
            $y2 = $this->height - $box2[1] + $box2[5];
            $y1 = $y2 - $box1[1] + $box1[5];
            imagettftext($this->image, 10, 0, $x, $y1, $white, $this->font, "@Debug:");
            imagettftext($this->image, 10, 0, $x, $y2, $white, $this->font, "Render time: {$renderTime}s");
        }
    }
    
    public function renderAtNative() {
        $this->grabInformation();
        
        $this->skin->render_init(
            mapDim: $this->mapDimension,
            bgCol: $this->backgroundColour,
            layerOrd: $this->layerOrder,
            datVil: $this->dataVillage,
            datAlly: $this->dataAlly,
            datPlayer: $this->dataPlayer,
            drawing: $this->drawing_img,
            showCNumb: $this->showContinentNumbers,
            font: $this->font,
            markFact: $this->markerFactor,
            opaque: $this->opaque,
            gCol: $this->gridColour,
        );
        $this->image = $this->skin->render();
        $this->width= imagesx($this->image);
        $this->height = imagesy($this->image);
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
            case AbstractMapGenerator::$ANCHOR_BOTTOM_LEFT:
                $x = ((int) $relX) - $box[0];
                $y = ((int) $relY) - $box[1];
                break;
            case AbstractMapGenerator::$ANCHOR_BOTTOM_RIGHT:
                $x = ((int) $relX) - $box[2];
                $y = ((int) $relY) - $box[3];
                break;
            case AbstractMapGenerator::$ANCHOR_TOP_RIGHT:
                $x = ((int) $relX) - $box[4];
                $y = ((int) $relY) - $box[5];
                break;
            case AbstractMapGenerator::$ANCHOR_TOP_LEFT:
                $x = ((int) $relX) - $box[6];
                $y = ((int) $relY) - $box[7];
                break;
            case AbstractMapGenerator::$ANCHOR_MID_MID:
                $x = ((int) $relX) - ($box[0] + $box[4]) / 2;
                $y = ((int) $relY) - ($box[1] + $box[5]) / 2;
                break;
            default:
                throw new \InvalidArgumentException("Invalid anchor given");
        }
        
        imagettftext($this->image, $size, 0, $x, $y, $col, $this->font, $text);
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
            if($layer == static::$LAYER_MARK ||
                    $layer == static::$LAYER_PICTURE ||
                    $layer == static::$LAYER_TEXT ||
                    $layer == static::$LAYER_GRID ||
                    ($layer == static::$LAYER_DRAWING && $this->drawing_img != null && $this->drawing_img != "")) {
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
    
    public function setGridColour($col) {
        if($col == null) {
            return false;
        } else {
            $this->gridColour = array((int) $col[0], (int) $col[1], (int) $col[2]);
            if(count($col) > 3)
                $this->gridColour[] = (int) $col[3];
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
    
    private function colAllocate($color, $opacity=null) {
        if($opacity == null) {
            return imagecolorallocate($this->image, $color[0], $color[1], $color[2]);
        }
        return imagecolorallocatealpha($this->image, $color[0], $color[1], $color[2], 127-($opacity)*127/100);
    }
}
