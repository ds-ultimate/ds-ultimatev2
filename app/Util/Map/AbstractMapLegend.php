<?php

namespace App\Util\Map;

/**
 * Abstract Class defining what functions a legend must / can implement
 */
abstract class AbstractMapLegend {
    protected $image;
    protected $imgW;
    protected $imgH;
    
    protected $dataVillage;
    protected $dataAlly;
    protected $dataPlayer;
    protected $font;


    public function render_init(&$datVil, &$datAlly, &$datPlayer, &$font,) {
        $this->dataVillage = $datVil;
        $this->dataAlly = $datAlly;
        $this->dataPlayer = $datPlayer;
        $this->font = $font;
    }
    
    public abstract function render($expectedHeigth);
    
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
    
    /**
     * Fuciton for rendering aligned Text into the map
     * Anchor is used to define ref point of text box
     * 0, 0 will always be displayed top left
     */
    public function renderAlignedText($anchor, $relX, $relY, $size, $text, $color) {
        AbstractMapGenerator::staticRenderAlignedText($this->image, $this->font, $anchor, $relX, $relY, $size, $text, $color);
    }
}
