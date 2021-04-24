<?php

namespace App\Util;

/**
 * OUTDATED -> use C-Code instead
 * this LIB basically takes multiple php images an converts them into an animated gif
 */
class GifCreatorLib
{
    private static $GIF_HEADER = "GIF89a";
    
    //Weights for finding closest color in table
    private static $WEIGHT_H = 0.6;
    private static $WEIGHT_S = 0.2;
    private static $WEIGHT_V = 0.2;
    
    private $width;
    private $height;
    private $colorResolution = 0x70; //shifted 4 Left bercause used in that pos / should be 15
    private $pixelAspectRatio = 0;
    private $delayTime = 20; //time in 10ms
    private $images = [];
    
    private $globColorTable = null;
    private $globColorTableHSV = null;
    private $pictureColorMap = null;
    
    //informations from http://www.matthewflickinger.com/lab/whatsinagif/bits_and_bytes.asp
    public function __construct($width, $height) {
        $this->width = (int) $width;
        $this->height = (int) $height;
    }
    
    /**
     * returns the created Gif
     */
    public function createGif($progressCallback=null) {
        //http://www.matthewflickinger.com/lab/whatsinagif/animation_and_transparency.asp
        //http://www.matthewflickinger.com/lab/whatsinagif/bits_and_bytes.asp
        //http://www.matthewflickinger.com/lab/whatsinagif/gif_explorer.asp
        $gif = static::$GIF_HEADER;
        $gif .= static::littleEndianDump($this->width, 2);
        $gif .= static::littleEndianDump($this->height, 2);
        
        $this->createGlobalColorTable();
        //+1 for transparency
        $globColTblSizeBit = (int) ceil(log(count($this->globColorTable) + 1, 2) - 1);
        $globColTblSize = pow(2, $globColTblSizeBit + 1);
        
        //don't ask
        //global color table; max color Resolution; Thing is sorted
        $gif .= chr(0b10001000 + $globColTblSizeBit + $this->colorResolution);
        //background color index
        $gif .= chr(0);
        //pixel aspect ratio
        $gif .= chr($this->pixelAspectRatio);
        
        $gif .= $this->getGlobalColorTableString($globColTblSize);
        $gif .= $this->loopExtension(0);
        
        for($i = 0; $i < count($this->images); $i++) {
            if($progressCallback != null) {
                $progressCallback($i, count($this->images));
            }
            $gif .= $this->handleImage($i, $globColTblSizeBit);
        }
        
        //Trailer
        $gif .= chr(0x3B);
        
        return $gif;
    }
    
    private function createGlobalColorTable() {
        $colors = [];
        foreach($this->images as $img) {
            for($i = 0; $i < $this->height; $i++) {
                for($j = 0; $j < $this->width; $j++) {
                    $col = imagecolorsforindex($img, imagecolorat($img, $j, $i));
                    $color = ($col['alpha'] << 24) + ($col['red'] << 16) + ($col['green'] << 8) + $col['blue'];
                    
                    if(! isset($colors[$color])) {
                        $colors[$color] = 0;
                    }
                    $colors[$color]++;
                }
            }
        }
        
        //use most occuring 255 colors for color table
        //reserve last one for Transparency
        arsort($colors);
        $keys = array_keys($colors);
        $this->pictureColorMap = [];
        $this->globColorTable = [];
        for($i = 0; $i < 255 && $i < count($keys); $i++) {
            $this->pictureColorMap[$keys[$i]] = $i;
            $this->globColorTable[] = $keys[$i];
        }
        $this->globalColorTableToHSV();
        for(; $i < count($keys); $i++) {
            $this->pictureColorMap[$keys[$i]] = $this->pictureColorMap[$this->findClosestMatching($keys[$i])];
        }
    }
    
    private function getGlobalColorTableString($size) {
        $ret = "";
        for($i = 0; $i < $size; $i++) {
            if($i < count($this->globColorTable)) {
                $ret .= static::bigEndianDump($this->globColorTable[$i], 3);
            } else {
                //fill with 0
                $ret .= static::bigEndianDump(0, 3);
            }
        }
        return $ret;
    }
    
    private function handleImage($id, $globColTblSizeBit) {
        $image = $this->images[$id];
        $prevImage = ($id > 0 ? $this->images[$id-1] : null);
        $transparentIdx = count($this->globColorTable);
        
        $ret = $this->delayHeader($transparentIdx, $id == 0);
        $ret.= chr(0x2C);
        
        if($prevImage == null) {
            $ret.= static::littleEndianDump(0, 2); //left
            $ret.= static::littleEndianDump(0, 2); //top
            $ret.= static::littleEndianDump($this->width, 2); //width
            $ret.= static::littleEndianDump($this->height, 2); //height
            $xs = 0; $ys = 0; $ye = $this->width - 1; $xe = $this->height - 1;
        } else {
            $minMax = $this->getChangedRegion($prevImage, $image);
            $xs = $minMax[0]; $xe = $minMax[1]; $ys = $minMax[2]; $ye = $minMax[3]; 
            $ret.= static::littleEndianDump($ys, 2); //left
            $ret.= static::littleEndianDump($xs, 2); //top
            $ret.= static::littleEndianDump($ye - $ys + 1, 2); //width
            $ret.= static::littleEndianDump($xe - $xs + 1, 2); //height
        }
        $ret.= chr(0x00); //no local color table
        $ret.= $this->lzwCompressImage($image, $prevImage, $transparentIdx, $globColTblSizeBit, $xs, $ys, $xe, $ye);
        return $ret;
    }
    
    private function loopExtension($amount) {
        $ret = chr(0x21);
        $ret .= chr(0xFF);
        $ret .= chr(0x0B); // lenght of NETSCAPE2.0
        $ret .= "NETSCAPE2.0";
        $ret .= chr(0x03);
        $ret .= chr(0x01);
        $ret .= static::littleEndianDump($amount, 2);
        $ret .= chr(0);
        return $ret;
    }
    
    private function delayHeader($transparentIdx, $first) {
        $ret = chr(0x21);
        $ret.= chr(0xF9);
        $ret.= chr(0x04);
        if($first) {
            $ret.= chr(0x06); //draw on bg / wait for user
        } else {
            $ret.= chr(0x05); //use transparency / show on top
        }
        $ret.= static::littleEndianDump($this->delayTime, 2);
        $ret.= chr($transparentIdx); //last color is transparency
        $ret.= chr(0x00);
        return $ret;
    }
    
    private function lzwCompressImage($image, $prevImage, $transparentIdx, $globColTblSizeBit, $xs, $ys, $xe, $ye) {
        $compressor = new LZWCompressor($globColTblSizeBit + 1);
        
        if($prevImage != null) {
            $ret = chr($globColTblSizeBit + 1);
            for($i = $xs; $i <= $xe; $i++) {
                for($j = $ys; $j <= $ye; $j++) {
                    $col = imagecolorsforindex($image, imagecolorat($image, $j, $i));
                    $color = ($col['alpha'] << 24) + ($col['red'] << 16) + ($col['green'] << 8) + $col['blue'];

                    $prevCol = imagecolorsforindex($prevImage, imagecolorat($prevImage, $j, $i));
                    $prevColor = ($prevCol['alpha'] << 24) + ($prevCol['red'] << 16) + ($prevCol['green'] << 8) + $prevCol['blue'];
                    if($prevColor != $color) {
                        $compressor->append($this->pictureColorMap[$color]);
                    } else {
                        $compressor->append($transparentIdx);
                    }
                }
            }
        } else {
            $ret = chr($globColTblSizeBit + 1);
            for($i = $xs; $i <= $xe; $i++) {
                for($j = $ys; $j <= $ye; $j++) {
                    $col = imagecolorsforindex($image, imagecolorat($image, $j, $i));
                    $color = ($col['alpha'] << 24) + ($col['red'] << 16) + ($col['green'] << 8) + $col['blue'];
                    
                    $compressor->append($this->pictureColorMap[$color]);
                }
            }
        }
        $compressor->finish();
        $compressed = $compressor->getCompressed();
        
        while(strlen($compressed) > 255) {
            $part = substr($compressed, 0, 255);
            $compressed = substr($compressed, 255);
            $ret .= chr(0xFF);
            $ret .= $part;
        }
        
        $ret .= chr(strlen($compressed));
        $ret .= $compressed;
        $ret .= chr(0x00);
        return $ret;
    }
    
    private function findClosestMatching($color) {
        $closest = 0;
        $minDiff = 1;
        $hsv = static::rgbToHSV($color);
        for($i = 1; $i < count($this->globColorTableHSV); $i++) {
            $diff = abs($this->globColorTableHSV[$i][0] - $hsv[0]) * static::$WEIGHT_H;
            $diff += abs($this->globColorTableHSV[$i][1] - $hsv[1]) * static::$WEIGHT_S;
            $diff += abs($this->globColorTableHSV[$i][2] - $hsv[2]) * static::$WEIGHT_V;
            
            if($diff < $minDiff) {
                $closest = $i;
                $minDiff = $diff;
            }
        }
        return $this->globColorTable[$closest];
    }
    
    private function globalColorTableToHSV() {
        foreach($this->globColorTable as $rgbCol) {
            $hsvCol = static::rgbToHSV($rgbCol);
            $this->globColorTableHSV[] = $hsvCol;
        }
    }
    
    private function getChangedRegion($prevImage, $image) {
        $xs = 0; $ys = 0; $ye = $this->width - 1; $xe = $this->height - 1;
        
        //xs
        for($i = $xs; $i <= $xe; $i++) {
            for($j = $ys; $j <= $ye; $j++) {
                $col = imagecolorsforindex($image, imagecolorat($image, $j, $i));
                $color = ($col['alpha'] << 24) + ($col['red'] << 16) + ($col['green'] << 8) + $col['blue'];

                $prevCol = imagecolorsforindex($prevImage, imagecolorat($prevImage, $j, $i));
                $prevColor = ($prevCol['alpha'] << 24) + ($prevCol['red'] << 16) + ($prevCol['green'] << 8) + $prevCol['blue'];
                
                if($prevColor != $color) {
                    $xs = $i;
                    break;
                }
            }
            if($xs > 0) break;
        }
        
        //ys
        for($j = $ys; $j <= $ye; $j++) {
            for($i = $xs; $i <= $xe; $i++) {
                $col = imagecolorsforindex($image, imagecolorat($image, $j, $i));
                $color = ($col['alpha'] << 24) + ($col['red'] << 16) + ($col['green'] << 8) + $col['blue'];

                $prevCol = imagecolorsforindex($prevImage, imagecolorat($prevImage, $j, $i));
                $prevColor = ($prevCol['alpha'] << 24) + ($prevCol['red'] << 16) + ($prevCol['green'] << 8) + $prevCol['blue'];
                
                if($prevColor != $color) {
                    $ys = $j;
                    break;
                }
            }
            if($ys > 0) break;
        }
        
        //xe
        for($i = $xe; $i > $xs; $i--) {
            for($j = $ys; $j <= $ye; $j++) {
                $col = imagecolorsforindex($image, imagecolorat($image, $j, $i));
                $color = ($col['alpha'] << 24) + ($col['red'] << 16) + ($col['green'] << 8) + $col['blue'];

                $prevCol = imagecolorsforindex($prevImage, imagecolorat($prevImage, $j, $i));
                $prevColor = ($prevCol['alpha'] << 24) + ($prevCol['red'] << 16) + ($prevCol['green'] << 8) + $prevCol['blue'];
                
                if($prevColor != $color) {
                    $xe = $i;
                    break;
                }
            }
            if($xe < $this->height - 1) break;
        }
        
        //ye
        for($j = $ye; $j > $ys; $j--) {
            for($i = $xs; $i <= $xe; $i++) {
                $col = imagecolorsforindex($image, imagecolorat($image, $j, $i));
                $color = ($col['alpha'] << 24) + ($col['red'] << 16) + ($col['green'] << 8) + $col['blue'];

                $prevCol = imagecolorsforindex($prevImage, imagecolorat($prevImage, $j, $i));
                $prevColor = ($prevCol['alpha'] << 24) + ($prevCol['red'] << 16) + ($prevCol['green'] << 8) + $prevCol['blue'];
                
                if($prevColor != $color) {
                    $ye = $j;
                    break;
                }
            }
            if($ye < $this->width - 1) break;
        }
        
        return [$xs, $xe, $ys, $ye];
    }
    
    /**
     * rgb to HSV
     * HSV output from 0 to 1 (float)
     */
    private static function rgbToHSV($rgbCol) {
        static::check('alpha in global color table', ($rgbCol & 0xFF000000) == 0);

        $rFloat = (($rgbCol & 0x00FF0000) >> 16) / 255;
        $gFloat = (($rgbCol & 0x0000FF00) >> 8) / 255;
        $bFloat = ($rgbCol & 0x000000FF) / 255;

        $maxFloat = max($rFloat, $gFloat, $bFloat);
        $minFloat = min($rFloat, $gFloat, $bFloat);
        $maxDiff = $maxFloat - $minFloat;

        $V = $maxFloat;
        if($maxDiff == 0) {
            $H = 0;
            $S = 0;
        } else {
            $S = $maxDiff / $maxFloat;

            $diffR = ((($maxFloat - $rFloat) / 6) + ($maxDiff / 2)) / $maxDiff;
            $diffG = ((($maxFloat - $gFloat) / 6) + ($maxDiff / 2)) / $maxDiff;
            $diffB = ((($maxFloat - $bFloat) / 6) + ($maxDiff / 2)) / $maxDiff;

            if      ($rFloat == $maxFloat) $H = $diffB - $diffG;
            else if ($gFloat == $maxFloat) $H = (1/3) + $diffR - $diffB;
            else if ($bFloat == $maxFloat) $H = (2/3) + $diffG - $diffR;

            if ($H<0) $H++;
            if ($H>1) $H--;
        }
        
        return [$H, $S, $V];
    }
    
    private static function littleEndianDump($var, $amount) {
        $ret = "";
        for($i = 0; $i < $amount; $i++) {
            $ret .= chr($var % 256);
            $var /= 256;
        }
        return $ret;
    }
    
    private static function bigEndianDump($var, $amount) {
        $ret = "";
        for($i = 0; $i < $amount; $i++) {
            $ret = chr($var % 256) . $ret;
            $var /= 256;
        }
        return $ret;
    }
    
    /**
     * checks that $result ist true error otherwise
     */
    private static function check($name, $result) {
        if(! $result) {
            throw new InvalidArgumentException($name);
        }
    }
    
    public function addPNG($filename) {
        $img = imagecreatefrompng($filename);
        static::check("width", imagesx($img) == $this->width);
        static::check("height", imagesy($img) == $this->height);
        
        $this->images[] = $img;
    }
}