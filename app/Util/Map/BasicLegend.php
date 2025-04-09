<?php

namespace App\Util\Map;

/**
 * Abstract Class defining what functions a legend must / can implement
 */
class BasicLegend extends AbstractMapLegend {
    private static $legendWidth = 200;

    public function render($expectedHeigth) {
        $this->imgW = static::$legendWidth;
        $this->imgH = $expectedHeigth;
        
        $image = imagecreatetruecolor($this->imgW, $this->imgH);
        imagealphablending($image, true); //needed to work with alpha values
        imagesavealpha($image, true);
        $this->image = $image;
        
        $colour_bg = $this->colAllocate([0, 0, 0]);
        imagefill($this->image, 0, 0, $colour_bg);
        
        foreach(array_keys($this->dataAlly) as $idx => $allyID) {
            $ally = $this->dataAlly[$allyID];
            
            $this->drawSingleEntry(20, 40 + $idx * 50, 21, $ally);
        }
        
        return $this->image;
    }
    
    private function drawSingleEntry($x, $y, $heigth, $allyEntry) {
        $white = imagecolorallocate($this->image, 255, 255, 255);
        $markerCol = $this->colAllocate($allyEntry["colour"]);
        
        imagefilledrectangle($this->image, $x, $y, $x + $heigth - 1, $y + $heigth - 1, $white);
        imagefilledrectangle($this->image, $x + 1, $y + 1, $x + $heigth - 2, $y + $heigth - 2, $markerCol);
        
        $textStart = $x + $heigth + 1 + 10;
        $this->renderAlignedText(AbstractMapGenerator::$ANCHOR_MID_LEFT, $textStart, $y + 1 + $heigth / 2, $heigth / 2, $allyEntry["name"], [255, 255, 255]);
    }
}
