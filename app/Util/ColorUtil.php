<?php

namespace App\Util;

class ColorUtil {
    
    /**
     * rgb to HSV
     * HSV output from 0 to 1 (float)
     */
    public static function rgbToHSV($rgbCol) {
        if(count($rgbCol) > 3) {
            throw new InvalidArgumentException('alpha in rgb');
        }

        $rFloat = $rgbCol[0] / 255;
        $gFloat = $rgbCol[1] / 255;
        $bFloat = $rgbCol[2] / 255;

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
    
    private static $weightGray_R = 0.299;
    private static $weightGray_G = 0.587;
    private static $weightGray_B = 0.114;
    /**
     * rgb to Grayscale
     * Grayscale output from 0 to 255 (as input)
     */
    public static function rgbToGray($rgbCol) {
        return $rgbCol[0] * static::$weightGray_R +
                $rgbCol[1] * static::$weightGray_G +
                $rgbCol[2] * static::$weightGray_B;
    }
    
    public static function mixColor($col1, $col2, $factor2=0.5) {
        return [
            $col1[0] * (1 - $factor2) + $col2[0] * $factor2,
            $col1[1] * (1 - $factor2) + $col2[1] * $factor2,
            $col1[2] * (1 - $factor2) + $col2[2] * $factor2,
        ];
    }
}
