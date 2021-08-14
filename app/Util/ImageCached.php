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
        
        $cached = imagecreatefrompng("{$this->basePath}.png");
        imagecopyresampled($this->image, $cached, 0, 0, 0, 0, $this->width, $this->height, imagesx($cached), imagesy($cached));
        
        $renderTime = round(microtime(true) - $startTime, 3);
        if($this->show_errs) {
            $font = realpath("fonts/NotoMono-Regular.ttf");
            
            $white = imagecolorallocate($this->image, 255, 255, 255);
            $box1 = imagettfbbox(10, 0, $font, "@Debug:");
            $box2 = imagettfbbox(10, 0, $font, "Render time: {$renderTime}s");
            $x = $this->width - max($box1[2], $box2[2]);
            $y2 = $this->height - $box2[1] + $box2[5];
            $y1 = $y2 - $box1[1] + $box1[5];
            imagettftext($this->image, 10, 0, $x, $y1, $white, $font, "@Debug:");
            imagettftext($this->image, 10, 0, $x, $y2, $white, $font, "Render time: {$renderTime}s");
        }
    }
}