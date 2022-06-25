<?php

namespace App\Util\Map;

/**
 * Skin rendering rectangles for the Villages
 */
class SkinFile extends AbstractSkinRenderer {
    private $image;
    private $fieldWidth = 5;
    private $fieldHeight = 5;
    
    public function render() {
        $image = imagecreatetruecolor($this->mapDimension['w'] * $this->fieldWidth, $this->mapDimension['h'] * $this->fieldHeight);
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
            
            $imgX = ($mapX - $this->mapDimension['xs']) * $this->width / $this->mapDimension['w'];
            $imgY = ($mapY - $this->mapDimension['ys']) * $this->height / $this->mapDimension['h'];
            if($ally['colour'][0] + $ally['colour'][1] + $ally['colour'][2] > 600) {
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
            
            $imgX = ($mapX - $this->mapDimension['xs']) * $this->width / $this->mapDimension['w'];
            $imgY = ($mapY - $this->mapDimension['ys']) * $this->height / $this->mapDimension['h'];
            $this->renderShadowedCenteredText($imgX, $imgY, $player['name'], $color, $white);
        }
        
        //TODO add for village Markers
    }
//    
//    private $skinCache = array();
//    private function getSkinImage($name) {
//        if(isset($this->skinCache[$name])) {
//            return $this->skinCache[$name];
//        }
//        
//        $this->skinCache[$name] = array('img' => imagecreatefrompng("images/".Village::getSkinImagePath($this->skin, $name)));
//        $this->skinCache[$name]['x'] = imagesx($this->skinCache[$name]['img']);
//        $this->skinCache[$name]['y'] = imagesy($this->skinCache[$name]['img']);
//        $this->skinCache[$name]['asp'] = $this->skinCache[$name]['x'] / $this->skinCache[$name]['y'];
//        
//        return $this->skinCache[$name];
//    }
}
