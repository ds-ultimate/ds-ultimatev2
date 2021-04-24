<?php

namespace App\Util;

class PictureRender {
    protected $image;
    
    public function output($ext) {
        switch ($ext) {
            case "png":
                return $this->outputPNG();
            case "jpg":
            case "jpeg":
                return $this->outputJPEG();
            case "gif":
                return $this->outputGIF();
            case "base64":
                return $this->outputBase64();
            default:
                return "Unknown extension";
        }
    }

    private function outputPNG() {
        ob_start();
        imagepng($this->image);
        $imagedata = ob_get_clean();
        imagedestroy($this->image);
        return response($imagedata, 200)
                ->header('Content-Type', 'image/png');
    }

    private function outputJPEG() {
        ob_start();
        imagejpeg($this->image);
        $imagedata = ob_get_clean();
        imagedestroy($this->image);
        return response($imagedata, 200)
                ->header('Content-Type', 'image/jpeg');
    }

    private function outputGIF() {
        ob_start();
        imagegif($this->image);
        $imagedata = ob_get_clean();
        imagedestroy($this->image);
        
        return response($imagedata, 200)
                ->header('Content-Type', 'image/gif');
    }

    private function outputBase64() {
        ob_start();
        imagepng($this->image);
        $imagedata = ob_get_clean();
        imagedestroy($this->image);
        
        return response(PictureRender::pngToBase64($imagedata), 200)
                ->header('Content-Type', 'image/base64');
    }
    
    public function getRawImage() {
        return $this->image;
    }
    
    public function saveTo($path, $ext) {
        switch ($ext) {
            case "png":
                return imagepng($this->image, "$path.$ext");
            case "jpg":
            case "jpeg":
                return imagejpeg($this->image, "$path.$ext");
            default:
                echo "Unknown extension cannot save file";
                return false;
        }
    }
    
    public static function pngToBase64($png) {
        return "data:image/png;base64,".base64_encode($png);
    }
    
    public static function base64ToPng($base64) {
        if($base64 == "") return "";
        $partial_raw = explode(",", $base64, 2)[1];
        return base64_decode($partial_raw);
    }
}