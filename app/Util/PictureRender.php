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
}