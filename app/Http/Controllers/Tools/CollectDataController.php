<?php

namespace App\Http\Controllers\Tools;

use App\World;
use App\BuildTime;
use App\BuildTimeRaw;
use Illuminate\Routing\Controller as BaseController;

class CollectDataController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public $lang = [
        "de" => [
            "title" => "Gebäude.*Bedarf.*Bauen",
            "level" => "Stufe",
            "level0" => "nicht vorhanden",
            "buildings" => [
                "main" => "Hauptgebäude",
                'barracks'=> "Kaserne",
                'stable'=> "Stall",
                'garage'=> "Werkstatt",
                'church'=> "Kirche",
                'watchtower'=> "Wachturm",
                'snob'=> "Adelshof",
                'smith'=> "Schmiede",
                'place'=> "Versammlungsplatz",
                'statue'=> "Statue",
                'market'=> "Marktplatz",
                'wood'=> "Holzfällerlager",
                'stone'=> "Lehmgrube",
                'iron'=> "Eisenmine",
                'farm'=> "Bauernhof",
                'storage'=> "Speicher",
                'hide'=> "Versteck",
                'wall'=> "Wall",
            ]
        ],
        "zz" => [
            "title" => "Buildings.*Requirements.*Construct",
            "level" => "Level",
            "level0" => "not constructed",
            "buildings" => [
                "main" => "Headquarters",
                'barracks'=> "Barracks",
                'stable'=> "Stable",
                'garage'=> "Workshop",
                'church'=> "Church",
                'watchtower'=> "Watchtower", //unchecked translation
                'snob'=> "Academy",
                'smith'=> "Smithy",
                'place'=> "Rally point",
                'statue'=> "Statue",
                'market'=> "Market",
                'wood'=> "Timber camp",
                'stone'=> "Clay pit",
                'iron'=> "Iron mine",
                'farm'=> "Farm",
                'storage'=> "Warehouse",
                'hide'=> "Hiding place",
                'wall'=> "Wall",
            ]
        ],
        "yy" => [
            "title" => "Gebäude.*Bedarf.*Bauen",
            "level" => "Stufe",
            "level0" => "nicht vorhanden",
            "buildings" => [
                "main" => "Hauptgebäude",
                'barracks'=> "Kaserne",
                'stable'=> "Stall",
                'garage'=> "Werkstatt",
                'church'=> "Kirche",
                'watchtower'=> "Wachturm",
                'snob'=> "Adelshof",
                'smith'=> "Schmiede",
                'place'=> "Versammlungsplatz",
                'statue'=> "Statue",
                'market'=> "Marktplatz",
                'wood'=> "Holzfällerlager",
                'stone'=> "Lehmgrube",
                'iron'=> "Eisenmine",
                'farm'=> "Bauernhof",
                'storage'=> "Speicher",
                'hide'=> "Versteck",
                'wall'=> "Wall",
            ]
        ],
    ];
    
    public function index() {
        $latestData = (new BuildTime())->orderBy('created_at', 'DESC')->limit(20)->get();
        $worlds = (new World())->orderBy('server_id', 'ASC')->orderBy('name', 'ASC')->get();
        return view("tools.collectData", compact('latestData', 'worlds'));
    }
    
    public function stats() {
        abort_unless(\Gate::allows('dashboard_access'), 403);
        
        $latestData = (new BuildTime())->orderBy('created_at', 'DESC')->limit(20)->get();
        $worlds = (new World())->orderBy('server_id', 'ASC')->orderBy('name', 'ASC')->get();
        $buildingTimes = $this->getBuildingTimesResultArray();
        return view("tools.collectDataStats", compact('latestData', 'worlds', 'buildingTimes'));
    }
    
    public function post($pServer){
        $origData = $_POST["data"];
        $in = \Illuminate\Support\Facades\Request::input();
        $debug = isset($in['debug']);
        
        $server = substr($pServer, 0, 2);
        $world = substr($pServer, 2);
        World::existWorld($server, $world);
        $worldData = World::getWorld($server, $world);
        
        $loLang = $this->lang[$server];
        
        $data = str_replace("\r", "\n", $origData);
        $matches = null;
        if(preg_match("/{$loLang['title']}/", $data, $matches, PREG_OFFSET_CAPTURE) !== 1) {
            echo "no preg match<br>\n$data<br>\n<br>\n<br>\n";
            return 0;
        }
        
        $data = substr($data, $matches[0][1]);
        $splited = explode("\n", $data);
        
        $curBuilding = null;
        $result = array();
        foreach($splited as $line) {
            if(strlen($line) < 2) continue;
            if($debug) echo "$line<br>\n";
            
            $foundOne = false;
            foreach($loLang['buildings'] as $internal => $build) {
                if(strpos($line, $build) !== false && strpos($line, $build) < 10) {
                    $curBuilding = $internal;
                    if($debug) echo "Found $curBuilding<br><br>\n\n";
                    $foundOne = true;
                }
            }
            
            if($foundOne) continue;
            if($curBuilding == null) {
                continue;
            }
            $parts = explode("\t", $line);
            
            if(strpos($parts[0], $loLang['level']) !== false) {
                $level = trim(str_replace($loLang['level'], "", $parts[0]));
            } else if(strpos($parts[0], $loLang['level0']) !== false) {
                $level = 0;
            } else {
                continue;
            }
            if($debug) echo "Found $curBuilding with level $level<br><br>\n\n";
            if(count($parts) == 2) {
                //fully built building
                $result[$curBuilding] = [
                    "wood" => -1,
                    "clay" => -1,
                    "iron" => -1,
                    "time" => -1,
                    "pop" => -1,
                    "level" => $level,
                ];
            }
            if(count($parts) < 6) continue;
            $wood = trim($parts[1]);
            $clay = trim($parts[2]);
            $iron = trim($parts[3]);
            $time = trim($parts[4]);
            $pop = trim($parts[5]);
            
            $result[$curBuilding] = [
                "wood"=>$wood,
                "clay"=>$clay,
                "iron"=>$iron,
                "time"=>$time,
                "pop"=>$pop,
                "level"=>$level,
            ];
            $curBuilding = null;
        }
        
        if(! isset($result["main"])) return 0;
        $rawModel = new BuildTimeRaw();
        $rawModel->world_id = $worldData->id;
        $rawModel->user_id = \Auth::user()->id;
        $rawModel->rawdata = $origData;
        $rawModel->save();
        
        foreach($result as $name => $toInsert) {
            //Skip if building is fully built
            if($toInsert["wood"] == -1) continue;
            $dbInst = new BuildTime();
            $dbInst->world_id = $worldData->id;
            $dbInst->user_id = \Auth::user()->id;
            $dbInst->building = $name;
            $dbInst->level = $toInsert["level"];
            $dbInst->wood = $toInsert["wood"];
            $dbInst->clay = $toInsert["clay"];
            $dbInst->iron = $toInsert["iron"];
            $dbInst->buildtime = $toInsert["time"];
            $dbInst->pop = $toInsert["pop"];
            $dbInst->mainLevel = $result["main"]["level"];
            $dbInst->rawdata_id = $rawModel->id;
            
            $dbInst->save();
        }
        
        return 1;
    }
    
    public function getBuildingTimesResultArray() {
        $allData = (new BuildTime())->get();
        
        $worldCache = [];
        $results = [];
        foreach($allData as $data) {
            if(!isset($worldCache[$data->world_id])) {
                $worldCache[$data->world_id] = [
                    "b" => simplexml_load_string($data->world->buildings),
                    "c" => simplexml_load_string($data->world->config),
                ];
            }
            $worldConf = $worldCache[$data->world_id]["c"];
            $buildConf = $worldCache[$data->world_id]["b"];
            if($buildConf == null) {
                echo "Skipping entry {$data->id} because of missing b conf<br>\n";
                continue;
            }
            if($worldConf == null) {
                echo "Skipping entry {$data->id} because of missing w conf<br>\n";
                continue;
            }
            if($worldConf->game->buildtime_formula != 2) {
                echo "Skipping entry {$data->id} wrong formula {$worldConf->game->buildtime_formula}<br>\n";
                continue;
            }
            if(!isset($buildConf->{$data->building})) {
                echo "Skipping entry {$data->id} because building not found<br>\n";
                continue;
            }
            
            $thisConf = $buildConf->{$data->building};
            $woodLevel = $this->getBuildingLevel($thisConf->wood, $thisConf->wood_factor, $data->wood);
            $clayLevel = $this->getBuildingLevel($thisConf->stone, $thisConf->stone_factor, $data->clay);
            $ironLevel = $this->getBuildingLevel($thisConf->iron, $thisConf->iron_factor, $data->iron);
            
            if($woodLevel != $clayLevel || $clayLevel != $ironLevel) {
                echo "Skipping entry {$data->id} because level differs ($woodLevel, $clayLevel, $ironLevel)<br>\n";
                continue;
            }
            if($woodLevel == -1) {
                echo "Skipping entry {$data->id} unable to find correct Level<br>\n";
                continue;
            }
            if($woodLevel != $data->level) {
                echo "Changing level from {$data->level} to $woodLevel at {$data->id}<br>\n";
                $data->level = $woodLevel;
                if($data->building == 'main') {
                    $res = (new BuildTime())->where('rawdata_id', $data->rawdata_id)->get();
                    foreach($res as $entry) {
                        if($entry->id != $data->id) {
                            $entry->mainLevel = $data->level;
                            $entry->save();
                        }
                    }
                    $data->mainLevel = $data->level;
                }
                $data->save();
            }
            
            $parts = explode(":", $data->buildtime);
            if(count($parts) != 3) {
                echo "Skipping entry {$data->id} invalid Time<br>\n";
                continue;
            }
            $time = ($parts[0]*60 + $parts[1])*60 + $parts[2];
            $timeMinWithoutConf = ($time - 0.5) * $worldConf->speed * pow(1.05, $data->mainLevel);
            $timeMaxWithoutConf = ($time + 0.5) * $worldConf->speed * pow(1.05, $data->mainLevel);
            
            if(!isset($results[$data->building])) {
                $results[$data->building] = array();
            }
            if(isset($results[$data->building][$data->level])) {
                if($timeMinWithoutConf > $results[$data->building][$data->level]['min']) {
                    $results[$data->building][$data->level]['min'] = $timeMinWithoutConf;
                }
                if($timeMaxWithoutConf < $results[$data->building][$data->level]['max']) {
                    $results[$data->building][$data->level]['max'] = $timeMaxWithoutConf;
                }
            } else {
                $results[$data->building][$data->level] = [
                    "min" => $timeMinWithoutConf,
                    "max" => $timeMaxWithoutConf,
                ];
            }
        }
        
        $all = [];
        $finalRes = array();
        foreach($results as $name => $val) {
            $finalRes[$name] = array();
            foreach($val as $lv => $data) {
                $min = floor($data['min']);
                $max = ceil($data['max']);
                
                if($min > $max) {
                    echo "Warn $min bigger than $max !! @$name, $lv <br>\n";
                }
                
                $finalRes[$name][$lv] = "$min-$max";
                
                $minExtr = $data['min'] / \App\Util\BuildingUtils::$BUILDINGS[$name]['build_time'];
                $maxExtr = $data['max'] / \App\Util\BuildingUtils::$BUILDINGS[$name]['build_time'];
                if(! isset($all[$lv])) $all[$lv] = [];
                if(! isset($all[$lv]['min']) || $all[$lv]['min'] < $minExtr) {
                    $all[$lv]['min'] = $minExtr;
                }
                if(! isset($all[$lv]['max']) || $all[$lv]['max'] > $maxExtr) {
                    $all[$lv]['max'] = $maxExtr;
                }
            }
        }
        $finalRes['all_min'] = [];
        $finalRes['all_max'] = [];
        foreach($all as $lv => $data) {
            $min = $data['min'];
            $max = $data['max'];
            if($min > $max) {
                echo "Warn $min bigger than $max !! @all, $lv <br>\n";
            }
            
            $finalRes['all_min'][$lv] = number_format($min, 7);
            $finalRes['all_max'][$lv] = number_format($max, 7);
            $finalRes['all_delta'][$lv] = number_format($max - $min, 7);
        }
        
        return $finalRes;
    }
    
    function getBuildingLevel($base, $factor, $val) {
        if($base == $val) return 0;
        
        $curVal = $base;
        for($i = 1; $i < 30; $i++) {
            $curVal *= $factor;
            
            if(round($curVal) == $val) {
                return $i;
            }
        }
        return -1;
    }
}
