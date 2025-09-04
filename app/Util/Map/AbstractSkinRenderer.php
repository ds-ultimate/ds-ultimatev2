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
        return imagecolorallocatealpha($this->image, (int) $color[0], (int) $color[1], (int) $color[2], (int) (127-($opacity)*127/100));
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
            $size = (int) ($this->imgW / 50 + $this->fieldWidth * 4);
        }
        $box = imagettfbbox($size, 0, $this->font, $text);

        $drawX = (int) ($x - ($box[6] + $box[2]) / 2);
        $drawY = (int) ($y - ($box[7] + $box[3]) / 2);
        imagettftext($this->image, $size, 0, $drawX, $drawY, $color, $this->font, $text);
    }
    
    
    /**
     * Fuciton for rendering aligned Text into the map
     * Anchor is used to define ref point of text box
     * 0, 0 will always be displayed top left
     */
    public function renderAlignedText($anchor, $relX, $relY, $size, $text, $color) {
        AbstractMapGenerator::staticRenderAlignedText($this->image, $this->font, $anchor, $relX, $relY, $size, $text, $color);
    }
}
