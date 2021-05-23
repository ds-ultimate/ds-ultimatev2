<?php

namespace App\Util;

class ImageCached extends PictureRender {
    private $width;
    private $height;
    private $show_errs;
    private $basePath;

    public function __construct($basePath, $dim=null, $show_errs=false) {
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

        $this -> basePath = $basePath;
        $this -> image = $image;
        $this -> width= $img_width;
        $this -> height = $img_height;
        $this -> show_errs = $show_errs;
    }

    public function render() {
        $startTime = microtime(true);
        
        $maxDim = max($this->width, $this->height);
        
        $cachedSizes = [2000, 1000, 700, 500, 200];
        $match = -1;
        $diff = -1;
        
        foreach($cachedSizes as $cSize) {
            $curDiff = $this->getMatchDiff($maxDim, $cSize);
            
            if($match == -1 || $diff == -1 || $curDiff < $diff) {
                $match = $cSize;
                $diff = $curDiff;
            }
        }
        
        $cached = imagecreatefrompng("{$this->basePath}-$match.png");
        imagecopyresampled($this->image, $cached, 0, 0, 0, 0, $this->width, $this->height, $match, $match);
        
        $renderTime = round(microtime(true) - $startTime, 3);
        if($this->show_errs) {
            $font = realpath("fonts/NotoMono-Regular.ttf");
            
            $white = imagecolorallocate($this->image, 255, 255, 255);
            $box1 = imagettfbbox(10, 0, $font, "@Debug:");
            $box2 = imagettfbbox(10, 0, $font, "Render time: {$renderTime}s");
            $box3 = imagettfbbox(10, 0, $font, "Cache: {$this->basePath}-$match.png");
            $x = $this->width - max($box1[2], $box2[2], $box3[2]);
            $y3 = $this->height - $box3[1] + $box3[5];
            $y2 = $y3 - $box2[1] + $box2[5];
            $y1 = $y2 - $box1[1] + $box1[5];
            imagettftext($this->image, 10, 0, $x, $y1, $white, $font, "@Debug:");
            imagettftext($this->image, 10, 0, $x, $y2, $white, $font, "Render time: {$renderTime}s");
            imagettftext($this->image, 10, 0, $x, $y3, $white, $font, "Cache: {$this->basePath}-$match.png");
        }
    }
    
    private function getMatchDiff($expectedSize, $cached) {
        if($expectedSize > $cached) {
            return $expectedSize / $cached;
        }
        return $cached / $expectedSize;
    }
}