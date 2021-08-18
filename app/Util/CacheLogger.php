<?php

namespace App\Util;

use Carbon\Carbon;

class CacheLogger {
    public static $MAP_TYPE = 1;
    public static $PICTURE_TYPE = 2;
    public static $SIGNATURE_TYPE = 3;
    
    public static function logHit($type, $fName) {
        static::logData("H|" . static::typeConv($type) . "|$fName");
    }
    
    public static function logMiss($type, $fName) {
        static::logData("M|" . static::typeConv($type) . "|$fName");
    }
    
    private static function logData($data) {
        $path = storage_path(config("tools.logs.cacheRates"));
        if(!file_exists($path)) mkdir($path, 0777, true);
        $target = $path . Carbon::now()->format("Y-m-d") . ".log";
        file_put_contents($target, $data . "\n", FILE_APPEND | LOCK_EX);
    }
    
    private static function typeConv($type) {
        switch($type) {
            case static::$MAP_TYPE:
                return "m";
            case static::$PICTURE_TYPE:
                return "p";
            case static::$SIGNATURE_TYPE:
                return "s";
            default:
                return "U";
        }
    }
}
