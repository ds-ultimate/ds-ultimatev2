<?php

namespace App\Util\Map;

use App\Util\BasicFunctions;
use App\Util\ColorUtil;

/**
 * Skin rendering rectangles for the Villages
 */
class SkinSymbols extends AbstractSkinRenderer {

    public function __construct() {
        $this->fieldWidth = 5;
        $this->fieldHeight = 5;
    }
    
    public function render() {
        $this->imgW = $this->mapDimension['w'] * $this->fieldWidth;
        $this->imgH = $this->mapDimension['h'] * $this->fieldHeight;
        
        $image = imagecreatetruecolor($this->imgW, $this->imgH);
        imagealphablending($image, true); //needed to work with alpha values
        imagesavealpha($image, true);
        $this->image = $image;
        
        $colour_bg = $this->colAllocate($this->backgroundColour);
        imagefill($this->image, 0, 0, $colour_bg);
        
        foreach($this->layerOrder as $layer) {
            switch ($layer) {
                case AbstractMapGenerator::$LAYER_MARK:
                    $this->renderMarks('barb');
                    $this->renderMarks('play');
                    $this->renderMarks('mark');
                    break;
                
                case AbstractMapGenerator::$LAYER_PICTURE:
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
        return $this->image;
    }
    
    private function renderMarks($type) {
        foreach($this->dataVillage[$type] as $village) {
            if($village['colour'] == null) {
                continue;
            }
            
            if($type == 'mark' && $village['highLight']) {
                $colM = $this->colAllocate($village['colour'], $this->opaque*0.75);

                $x = $this->fieldWidth * ($village['x'] - $this->mapDimension['xs'] + 0.5);
                $y = $this->fieldHeight * ($village['y'] - $this->mapDimension['ys'] + 0.5);
                imagefilledellipse($this->image, intval($x), intval($y), intval(max($this->fieldWidth*3-1, 0)), intval(max($this->fieldHeight*3-1, 0)), $colM);
            }
            
            $x = $this->fieldWidth * ($village['x'] - $this->mapDimension['xs']);
            $y = $this->fieldHeight * ($village['y'] - $this->mapDimension['ys']);
            
            $factor = $this->markerFactor/2;
            $indentw = $this->fieldWidth*$factor;
            $indenth = $this->fieldHeight*$factor;
            
            $xs = ceil($x + $indentw);
            $xe = ceil($x + $this->fieldWidth - $indentw - 1);
            $ys = ceil($y + $indenth);
            $ye = ceil($y + $this->fieldHeight - $indenth - 1);
            
            //steps 0 - 20 - 40 - 60 - ... for 5x x and y increased resolution
            //full fill
            $colF = $this->colAllocate($village['colour'], $this->opaque);
            imagefilledrectangle($this->image, ceil($xs), ceil($ys), floor($xe), floor($ye), $colF);
        }
    }
    
    private function renderGrid() {
        $gridColBig = $this->colAllocate($this->gridColour, 100);
        $gridColSmall = $this->colAllocate($this->gridColour, 40);
        $gridColText = $this->colAllocate(ColorUtil::mixColor($this->backgroundColour, $this->gridColour, 0.5), 70);
        
        $thick = intval($this->fieldWidth / 10);
        
        //draw vertical grid
        for($i = 1; $i <= 9; $i++) { //draw 9 lines (from 1 to 9)
            $x = intval($this->fieldWidth * ($i*100 - $this->mapDimension['xs']));
            if($x < 0 || $x > $this->imgW) continue; //ignore lines outside border
            
            imagefilledrectangle($this->image, $x-$thick, 0, $x+$thick, $this->imgH, $gridColBig);
        }
        
        //draw horizontal grid
        for($i = 1; $i <= 9; $i++) { //draw 9 lines (from 1 to 9)
            $y = intval($this->fieldHeight * ($i*100 - $this->mapDimension['ys']));
            if($y < 0 || $y > $this->imgH) continue; //ignore lines outside border
            
            imagefilledrectangle($this->image, 0, $y-$thick, $this->imgW, $y+$thick, $gridColBig);
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
        
        
        //draw vertical grid
        for($i = 1; $i <= 99; $i++) { //draw 99 lines (from 1 to 99)
            if($i % 10 == 0) continue;
            $x = intval($this->fieldWidth * ($i*10 - $this->mapDimension['xs']));
            if($x < 0 || $x > $this->imgW) continue; //ignore lines outside border

            imagefilledrectangle($this->image, $x, 0, $x, $this->imgH, $gridColSmall);
        }

        //draw horizontal grid
        for($i = 1; $i <= 99; $i++) { //draw 99 lines (from 1 to 99)
            if($i % 10 == 0) continue;
            $y = intval($this->fieldHeight * ($i*10 - $this->mapDimension['ys']));
            if($y < 0 || $y > $this->imgH) continue; //ignore lines outside border

            imagefilledrectangle($this->image, 0, $y, $this->imgW, $y, $gridColSmall);
        }
    }
    
    private function renderDrawing() {
        try {
            $drawing_img=imagecreatefromstring($this->drawing_img);
            imagealphablending($drawing_img, true);
            imagesavealpha($drawing_img, true);
            imagecopyresampled($this->image, $drawing_img, 0, 0, 0, 0, $this->imgW, $this->imgH, imagesx($drawing_img), imagesy($drawing_img));
        } catch (\ErrorException $ex) {
            $fg = imagecolorallocate($this->image, 255, 255, 255);
            $this->renderCenteredText($this->imgW / 2, $this->imgH / 2, __('tool.map.drawingErr'), $fg, $this->imgW / 45);
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
            
            $imgX = ($x - $this->mapDimension['xs']) * $this->imgW / $this->mapDimension['w'];
            $imgY = ($y - $this->mapDimension['ys']) * $this->imgH / $this->mapDimension['h'];
            
            if(ColorUtil::rgbToGray($ally['colour']) > 125) {
                //use black shadow for bright colors
                $this->renderShadowedCenteredText($imgX, $imgY, $ally['tag'], $color, $black);
            } else {
                $this->renderShadowedCenteredText($imgX, $imgY, $ally['tag'], $color, $white);
            }
        }
        
        foreach($this->dataPlayer as $player) {
            if(!$player['showText']) continue;
            if($player['villNum'] <= 0) continue;
            
            $x = $player['villX'] / $player['villNum'];
            $y = $player['villY'] / $player['villNum'];
            $color = imagecolorallocate($this->image, $player['colour'][0], $player['colour'][1], $player['colour'][2]);
            
            $imgX = ($x - $this->mapDimension['xs']) * $this->imgW / $this->mapDimension['w'];
            $imgY = ($y - $this->mapDimension['ys']) * $this->imgH / $this->mapDimension['h'];
            $this->renderShadowedCenteredText($imgX, $imgY, $player['name'], $color, $white);
        }
        
        //TODO add for village Markers
    }
}
