<?php

namespace App\Util;

/**
 * Abstract Class defining what functions a skin must / can implement
 */
class TextImageRenderer extends PictureRender {
    public static $ANCHOR_TOP_LEFT = 1;
    public static $ANCHOR_TOP_RIGHT = 2;
    public static $ANCHOR_BOTTOM_LEFT = 3;
    public static $ANCHOR_BOTTOM_RIGHT = 4;
    public static $ANCHOR_MID_LEFT = 5;
    public static $ANCHOR_MID_RIGHT = 6;
    public static $ANCHOR_TOP_MID = 7;
    public static $ANCHOR_BOTTOM_MID = 8;
    public static $ANCHOR_MID_MID = 9;
    
    private $font;
    private $width;
    private $height;
    private $show_errs;

    public function __construct($font, $dim=null, $show_errs=false, $srcPng=null) {
        if($srcPng != null) {
            $image = imagecreatefrompng($srcPng);
            $img_height = imagesy($image);
            $img_width = imagesx($image);
        } else {
            if(isset($dim["height"]) && isset($dim["width"])) {
                $img_height = $dim["height"];
                $img_width = $dim["width"];
            } else {
                throw new InvalidArgumentException("dimensions not set");
            }

            $image = imagecreatetruecolor(round($img_width, 0), round($img_height, 0));
            if($image === false) die("Error");
        }
        if(!imageistruecolor($image)) {
            imagepalettetotruecolor($image);
        }
        imagealphablending($image, true);

        $this -> font = realpath($font);
        $this -> image = $image;
        $this -> width= $img_width;
        $this -> height = $img_height;
        $this -> show_errs = $show_errs;
    }
    
    public function colAllocate($color, $opacity=null) {
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
    
    public function renderShadowedCenteredText($x, $y, $text, $col, $shadowCol, $indent=2) {
        $this->renderCenteredText($x - $indent, $y - $indent, $text, $shadowCol);
        $this->renderCenteredText($x - $indent, $y + $indent, $text, $shadowCol);
        $this->renderCenteredText($x + $indent, $y - $indent, $text, $shadowCol);
        $this->renderCenteredText($x + $indent, $y + $indent, $text, $shadowCol);
        $this->renderCenteredText($x, $y, $text, $col);
    }
    
    protected function renderCenteredText($x, $y, $text, $color, $size) {
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
    public function renderAlignedText($anchor, $relX, $relY, $size, $text, $color, $angle=0) {
        if($color[0] > 255 || $color[0] < 0 ||
                $color[1] > 255 || $color[1] < 0 ||
                $color[2] > 255 || $color[2] < 0) {
            throw new \InvalidArgumentException("Invalid color given");
        }
        
        $col = imagecolorallocate($this->image, (int) $color[0], (int) $color[1], (int) $color[2]);
        $box = imagettfbbox($size, $angle, $this->font, $text);
        $xMin = min($box[0], $box[2], $box[4], $box[6]);
        $yMin = min($box[1], $box[3], $box[5], $box[7]);
        $xMax = max($box[0], $box[2], $box[4], $box[6]);
        $yMax = max($box[1], $box[3], $box[5], $box[7]);
        
        switch ($anchor) {
            case TextImageRenderer::$ANCHOR_BOTTOM_LEFT:
                $x = $relX - $xMin;
                $y = $relY - $yMax;
                break;
            case TextImageRenderer::$ANCHOR_BOTTOM_RIGHT:
                $x = $relX - $xMax;
                $y = $relY - $yMax;
                break;
            case TextImageRenderer::$ANCHOR_TOP_RIGHT:
                $x = $relX - $xMax;
                $y = $relY - $yMin;
                break;
            case TextImageRenderer::$ANCHOR_TOP_LEFT:
                $x = $relX - $xMin;
                $y = $relY - $yMin;
                break;
            case TextImageRenderer::$ANCHOR_MID_LEFT:
                $x = $relX - $xMin;
                $y = $relY - ($yMin + $yMax) / 2;
                break;
            case TextImageRenderer::$ANCHOR_MID_RIGHT:
                $x = $relX - $xMax;
                $y = $relY - ($yMin + $yMax) / 2;
                break;
            case TextImageRenderer::$ANCHOR_TOP_MID:
                $x = $relX - ($xMin + $xMax) / 2;
                $y = $relY - $yMin;
                break;
            case TextImageRenderer::$ANCHOR_BOTTOM_MID:
                $x = $relX - ($xMin + $xMax) / 2;
                $y = $relY - $yMax;
                break;
            case TextImageRenderer::$ANCHOR_MID_MID:
                $x = $relX - ($xMin + $xMax) / 2;
                $y = $relY - ($yMin + $yMax) / 2;
                break;
            default:
                throw new \InvalidArgumentException("Invalid anchor given");
        }
        
        imagettftext($this->image, $size, $angle, $x, $y, $col, $this->font, $text);
    }
    
    public function insertPubImage($path, $x, $y, $w, $h, $srcX=0, $srcY=0, $srcW=null, $srcH=null) {
        $srcImg = imagecreatefrompng(public_path($path));
        $srcW = $srcW ?? imagesx($srcImg);
        $srcH = $srcH ?? imagesy($srcImg);
        imagecopyresampled($this->image, $srcImg, $x, $y, $srcX, $srcY, $w, $h, $srcW, $srcH);
    }
    
    public function h() {
        return $this->height;
    }
    
    public function w() {
        return $this->width;
    }
    
    public function setFont($newFont) {
        $this->font = realpath($newFont);
    }
}
