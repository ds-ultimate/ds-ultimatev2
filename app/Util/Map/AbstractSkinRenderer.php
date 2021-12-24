<?php

namespace App\Util\Map;

/**
 * Abstract Class defining what functions a skin must / can implement
 */
abstract class AbstractSkinRenderer {
    protected $image;
    protected $imgW;
    protected $imgH;
    
    protected $mapDimension;
    protected $backgroundColour;
    protected $gridColour;
    protected $layerOrder;
    protected $dataVillage;
    protected $dataAlly;
    protected $dataPlayer;
    protected $drawing_img;
    protected $showContinentNumbers;
    protected $font;
    protected $markerFactor;
    protected $opaque;
    
    protected $fieldWidth;
    protected $fieldHeight;


    public function render_init(&$mapDim, &$bgCol, &$layerOrd, &$datVil, &$datAlly,
            &$datPlayer, &$drawing, &$showCNumb, &$font, &$markFact, &$opaque, &$gCol) {
        $this->mapDimension = $mapDim;
        $this->backgroundColour = $bgCol;
        $this->layerOrder = $layerOrd;
        $this->dataVillage = $datVil;
        $this->dataAlly = $datAlly;
        $this->dataPlayer = $datPlayer;
        $this->drawing_img = $drawing;
        $this->showContinentNumbers = $showCNumb;
        $this->font = $font;
        $this->markerFactor = $markFact;
        $this->opaque = $opaque;
        $this->gridColour = $gCol;
    }
    
    public abstract function render();
    
    protected function colAllocate($color, $opacity=null) {
        if(count($color) > 3) {
            if($opacity == null) {
                $opacity = $color[3];
            } else {
                $opacity *= $color[3] / 100;
            }
        }
        
        if($opacity === null) {
            return imagecolorallocate($this->image, $color[0], $color[1], $color[2]);
        }
        return imagecolorallocatealpha($this->image, $color[0], $color[1], $color[2], 127-($opacity)*127/100);
    }
    
    /*
     * This function uses Map Coordinates not Picture!!
     */
    protected function renderShadowedCenteredText($x, $y, $text, $col, $shadowCol, $indent=2) {
        $this->renderCenteredText($x - $indent, $y - $indent, $text, $shadowCol);
        $this->renderCenteredText($x - $indent, $y + $indent, $text, $shadowCol);
        $this->renderCenteredText($x + $indent, $y - $indent, $text, $shadowCol);
        $this->renderCenteredText($x + $indent, $y + $indent, $text, $shadowCol);
        $this->renderCenteredText($x, $y, $text, $col);
    }
    
    /*
     * This function uses Picture coordinates
     */
    protected function renderCenteredText($x, $y, $text, $color, $size=null) {
        if($size == null) {
            $size = $this->imgW / 50 + $this->fieldWidth * 4;
        }
        $box = imagettfbbox($size, 0, $this->font, $text);

        $drawX = $x - ($box[6] + $box[2]) / 2;
        $drawY = $y - ($box[7] + $box[3]) / 2;
        imagettftext($this->image, $size, 0, $drawX, $drawY, $color, $this->font, $text);
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
}
