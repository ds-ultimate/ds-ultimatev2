<?php

namespace App\Util;

use App\CacheStat;
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
        $target = $path . static::getTodayFile();
        file_put_contents($target, $data . "\n", FILE_APPEND | LOCK_EX);
    }
    
    private static function getTodayFile() {
        return Carbon::now()->format("Y-m-d") . ".log";
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
    
    public static function generateStatistics(){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '600M');
        
        $dir = storage_path(config("tools.logs.cacheRates"));
        $files = scandir($dir);
        foreach($files as $file) {
            if($file == "." || $file == "..") continue;
            if($file == static::getTodayFile()) continue;
            $fileN = substr($file, 0, strlen($file) - 4);
            static::generateTyped($dir, $file, $fileN, static::$MAP_TYPE);
            static::generateTyped($dir, $file, $fileN, static::$PICTURE_TYPE);
            static::generateTyped($dir, $file, $fileN, static::$SIGNATURE_TYPE);
        }
    }
    
    private static function generateTyped($dir, $file, $fileN, $type) {
        $res = (new CacheStat())->where("type", $type)->where("date", $fileN)->first();
        if($res !== null) return;
        
        $miss = 0;
        $hit = 0;
        
        foreach(explode("\n", file_get_contents("$dir/$file")) as $line) {
            if($line == "") continue;
            $expl = explode("|", $line);
            if($expl[1] != static::typeConv($type)) continue;
            
            if($expl[0] == "M") $miss++;
            if($expl[0] == "H") $hit++;
        }
        
        $stat = new CacheStat();
        $stat->date = $fileN;
        $stat->type = $type;
        $stat->misses = $miss;
        $stat->hits = $hit;
        $stat->save();
    }
}
