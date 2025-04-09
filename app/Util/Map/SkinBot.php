<?php

namespace App\Util\Map;

use App\Util\BasicFunctions;
use App\Util\ColorUtil;

/**
 * Skin rendering rectangles for the Villages
 */
class SkinBot extends AbstractSkinRenderer {
    protected $highlightGroups;

    public function __construct($highlightGroups) {
        $this->fieldWidth = 5;
        $this->fieldHeight = 5;
        $this->highlightGroups = $highlightGroups;
    }
    
    public function render() {
        $this->imgW = $this->mapDimension['w'] * $this->fieldWidth;
        $this->imgH = $this->mapDimension['h'] * $this->fieldWidth;
        
        $image = imagecreatetruecolor($this->imgW, $this->imgH);
        imagealphablending($image, true); //needed to work with alpha values
        imagesavealpha($image, true);
        $this->image = $image;
        
        $colour_bg = $this->colAllocate([0, 0, 0]);
        imagefill($this->image, 0, 0, $colour_bg);
        
        $raw_base = imagecreatefrompng(public_path("images/ds_images/basemap.png"));
        imagecopyresized($this->image, $raw_base, 0, 0, $this->mapDimension['xs'], $this->mapDimension['ys'],
                $this->imgW, $this->imgH, $this->mapDimension['w'], $this->imgH / $this->fieldHeight);
        imagedestroy($raw_base);
        
        foreach($this->layerOrder as $layer) {
            switch ($layer) {
                case AbstractMapGenerator::$LAYER_MARK:
                    $this->renderMarks('barb');
                    $this->renderMarks('play');
                    $this->renderMarks('mark');
                    $this->renderHighlights(['mark']);
                    break;
                
                case AbstractMapGenerator::$LAYER_PICTURE:
                    break;
                
                case AbstractMapGenerator::$LAYER_TEXT:
                    break;
                
                case AbstractMapGenerator::$LAYER_GRID:
                    $this->renderGrid();
                    break;
                    
                case AbstractMapGenerator::$LAYER_DRAWING:
                    break;
            }
        }
        return $this->image;
    }
    
    private function renderHighlights($types) {
        $highlightImage = imagecreatetruecolor($this->imgW, $this->imgH);
        imagecopy($highlightImage, $this->image, 0, 0, 0, 0, $this->imgW, $this->imgH);
        
        foreach($types as $type) {
            $colGrpMap = [];
            $groups = [];
            for($i = 0; $i < count($this->highlightGroups); $i++) {
                $groupColor = $this->highlightGroups[$i];
                $asCol = ($groupColor[0] << 16) + ($groupColor[1] << 8) + $groupColor[2];
                $colGrpMap[$asCol] = count($this->highlightGroups) - 1 - $i;
                $groups[$i] = [];
            }
            
            foreach($this->dataVillage[$type] as $village) {
                if($village['colour'] == null || ! $village['highLight']) {
                    continue;
                }
                
                $tmpCol = $village['colour'];
                $asCol = ($tmpCol[0] << 16) + ($tmpCol[1] << 8) + $tmpCol[2];
                $groups[$colGrpMap[$asCol]][] = $village;
            }
            
            for($i = 0; $i < count($groups); $i++) {
                if(count($groups[$i]) == 0) {
                    continue;
                }
                
                $firstColor = $groups[$i][0]['colour'];
                $colM = imagecolorallocate($highlightImage, $firstColor[0], $firstColor[1], $firstColor[2]);

                foreach($groups[$i] as $village) {
                    $x = $this->fieldWidth * ($village['x'] - $this->mapDimension['xs']);
                    $y = $this->fieldHeight * ($village['y'] - $this->mapDimension['ys']);

                    imagefilledrectangle($highlightImage, $x - 1 - $this->fieldWidth, $y - 1 - $this->fieldHeight,
                            $x - 1 + 2 * $this->fieldWidth, $y - 1 + 2 * $this->fieldHeight, $colM);
                }
            }
        }
        
        imagecopymerge($this->image, $highlightImage, 0, 0, 0, 0, $this->imgW, $this->imgH, 25);
    }
    
    private function renderMarks($type) {
        foreach($this->dataVillage[$type] as $village) {
            if($village['colour'] == null) {
                continue;
            }
            
            $x = $this->fieldWidth * ($village['x'] - $this->mapDimension['xs']);
            $y = $this->fieldHeight * ($village['y'] - $this->mapDimension['ys']);
            
            $colF = $this->colAllocate($village['colour']);
            imagefilledrectangle($this->image, $x, $y, $x + $this->fieldWidth - 2, $y + $this->fieldHeight - 2, $colF);
        }
    }
    
    private function renderGrid() {
        $gridColBig = $this->colAllocate([0, 0, 0]);
        $gridColSmall = $this->colAllocate([48, 73, 14]);
        $gridColSingle = $this->colAllocate([67, 98, 19]);
        
        $drawEndX = $this->mapDimension["w"] * $this->fieldWidth;
        $this->renderGridInternal($gridColSingle, 4, $drawEndX, 4, $this->imgH, $this->fieldWidth);
        
        $offsetMinorX = 5 - ($this->mapDimension["xs"] % 5);
        $offsetMinorY = 5 - ($this->mapDimension["ys"] % 5);
        $this->renderGridInternal($gridColSmall, 4 + 5 * $offsetMinorX, $drawEndX,
                4 + 5 * $offsetMinorY, $this->imgH, $this->fieldWidth * 5);
        
        $middleX = (500 - $this->mapDimension["xs"]) * 5 + 4;
        $middleY = (500 - $this->mapDimension["ys"]) * 5 + 4;
        imagefilledrectangle($this->image, $middleX, 0, $middleX, $this->imgH, $gridColBig);
        imagefilledrectangle($this->image, 0, $middleY, $drawEndX, $middleY, $gridColBig);
    }
    
    private function renderGridInternal($color, $startX, $endX, $startY, $endY, $spacing) {
        //draw vertical grid
        for($i = $startX; $i <= $endX; $i += $spacing) {
            imagefilledrectangle($this->image, $i, 0, $i, $endY, $color);
        }
        
        //draw horizontal grid
        for($i = $startY; $i <= $endY; $i += $spacing) {
            imagefilledrectangle($this->image, 0, $i, $endX, $i, $color);
        }
    }
}
