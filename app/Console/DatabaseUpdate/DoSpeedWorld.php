<?php

namespace App\Console\DatabaseUpdate;

use App\SpeedWorld;
use App\Server;
use App\World;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DoSpeedWorld
{
    public static function run(){
        $serverArray = Server::getServer();

        foreach ($serverArray as $serverModel){
            if(! $serverModel->speed_active) {
                continue;
            }
            
            $speedFile = file_get_contents($serverModel->url.'/page/speed/rounds/future');
            $speedFile = str_replace("\n", "", $speedFile);
            $speedFile = preg_replace("[<script.*?>.*?</script>]", "", $speedFile);
            
            $speedRounds = static::getAllSpeedRounds($speedFile);
            if(count($speedRounds) < 1) {
                throw new \Exception("nothing found check implementation");
            }
            
            $regex = "[";
            $regex.= "<h3>#(\\d*) Speed</h3>.*";
            $regex.= "Start:</td> <td>(.*?)</td>.*";
            $regex.= "Ende:</td> <td>(.*?)</td>";
            $regex.= "]";
            foreach($speedRounds as $round) {
                //extract data
                preg_match($regex, $round, $matches);
                if(count($matches) < 1) {
                    throw new \Exception("unable to perform regex");
                }
                
                $num = $matches[1];
                $name = "s" . $num;
                $start = Carbon::parse(static::monthTranslate($matches[2]));
                $end = Carbon::parse(static::monthTranslate($matches[3]));
                
                //update existing rounds
                //use number as unique identifier
                $model = null;
                $dups = (new SpeedWorld())->where("server_id", $serverModel->id)->where("name", $name)->get();
                foreach($dups as $duplicate) {
                    if($model == null) {
                        $model = $duplicate;
                        $duplicate->worldCheck_at = Carbon::now();
                        $duplicate->planned_start = $start->timestamp;
                        $duplicate->planned_end = $end->timestamp;
                        $duplicate->update();
                    } else {
                        $duplicate->delete();
                    }
                }
                
                if($model == null) {
                    //create a new one
                    $worldNew = new SpeedWorld();
                    $worldNew->server_id = $serverModel->id;
                    $worldNew->name = $name;
                    $worldNew->worldCheck_at = Carbon::now();
                    $worldNew->planned_start = $start->timestamp;
                    $worldNew->planned_end = $end->timestamp;
                    $worldNew->started = false;
                    if ($worldNew->save() !== true){
                        BasicFunctions::createLog('ERROR_insert[SpeedWorld]', "Welt $world konnte nicht der Tabelle 'speed_worlds' hinzugefügt werden.");
                        continue;
                    }
                }
            }
        }
    }
    
    private static function getAllSpeedRounds($speedFile) {
        //find all div class speed-round -> throw an error if there are none!!
        //need to implement that case later
        $cursor = 0;
        $speedLen = strlen($speedFile);
        $rounds = [];
        
        while($cursor < $speedLen) {
            $speedPos = strpos($speedFile, "speed-round", $cursor);
            if($speedPos === false) {
                break;
            }
            $divPos = strrpos($speedFile, "<div", $speedPos - $speedLen);
            $endPos = static::findMatchingEnd($speedFile, $divPos);
            if(abs($speedPos - $divPos) > 100) {
                throw new \Exception("diff to big " . ($speedPos - $divPos));
            }
            $rounds[] = substr($speedFile, $divPos, $endPos - $divPos + 1);
            
            $cursor = $endPos;
        }
        
        return $rounds;
    }
    
    private static function findMatchingEnd($data, $offset) {
        $cursor = $offset;
        $datLen = strlen($data);
        $depth = [];
        $started = false;
        while($cursor < $datLen && (! $started || count($depth) > 0)) {
            $nextTag = strpos($data, "<", $cursor);
            if($nextTag === false) {
                $cursor = $datLen;
                break;
            }
            $nextEnd = strpos($data, ">", $nextTag + 1);
            if($nextEnd === false) {
                //maybe error here??
                $cursor = $datLen;
                break;
            }
            
            $tag = substr($data, $nextTag, $nextEnd - $nextTag);
            if($tag[1] == "/") {
                //close tag
                $tagName = explode(" ", substr($tag, 2))[0];
                $depthSize = count($depth);
                
                $match = $depthSize;
                for($i = $depthSize-1; $i >= 0; $i--) {
                    if($depth[$i] == $tagName) {
                        $match = $i;
                    }
                }
                
                for($i = $depthSize-1; $i >= $match ; $i--) {
                    unset($depth[$i]);
                }
            } else {
                $tagName = explode(" ", substr($tag, 1))[0];
                $depth[count($depth)] = $tagName;
                $started = true;
            }
            $cursor = $nextEnd;
        }
        
        return $cursor;
    }
    
    public static function monthTranslate($input) {
        $replace = [
            "Jän",
            "Feb",
            "Mär",
            "Apr",
            "Mai",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Okt",
            "Nov",
            "Dez",
        ];
        $replacements = [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
        ];
        return str_replace($replace, $replacements, $input);
    }
}
