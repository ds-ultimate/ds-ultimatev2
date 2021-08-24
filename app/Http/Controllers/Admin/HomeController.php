<?php

namespace App\Http\Controllers\Admin;

use App\CacheStat;
use App\User;
use App\Util\CacheLogger;
use App\Tool\AccMgrDB\AccountManagerTemplate;
use App\Tool\AttackPlanner\AttackList;
use App\Tool\AttackPlanner\AttackListItem;
use App\Tool\Map\Map;

class HomeController
{
    public function index()
    {
        $users = User::all();
        $counter['maps'] = (new Map())->count();
        $counter['attackplaner'] = (new AttackList())->count();
        $counter['attacks'] = (new AttackListItem())->count();
        $counter['accMgrDB'] = (new AccountManagerTemplate())->count();
        $counter['users'] = $users->count();
        $twitter = 0;
        $facebook = 0;
        $google = 0;
        $github = 0;
        $discord = 0;

        foreach ($users as $user){
            if(isset($user->profile->twitter_id)) $twitter++;
            if(isset($user->profile->facebook_id)) $facebook++;
            if(isset($user->profile->google_id)) $google++;
            if(isset($user->profile->github_id)) $github++;
            if(isset($user->profile->discord_id)) $discord++;
        }

        $counter['twitter'] = $twitter;
        $counter['facebook'] = $facebook;
        $counter['google'] = $google;
        $counter['github'] = $github;
        $counter['discord'] = $discord;

        return view('admin.home', compact('counter'));
    }
    
    public function cacheStats() {
        $cacheStatistics = [
            'signatures' => $this->generateReturn("signatures", CacheLogger::$SIGNATURE_TYPE, config("tools.signature.cacheDir")),
            'maps' => $this->generateReturn("maps", CacheLogger::$MAP_TYPE, config("tools.map.cacheDir")),
            'pictures' => $this->generateReturn("pictures", CacheLogger::$PICTURE_TYPE, config("tools.chart.cacheDir")),
        ];
        
        return view('admin.cacheStats', compact('cacheStatistics'));
    }
    
    private function generateReturn($name, $type, $conf) {
        $space = 0;
        $num = 0;
        $dir = storage_path($conf);
        
        if(file_exists($dir)) {
            $files = scandir($dir);
            foreach($files as $file) {
                if($file == "." || $file == "..") continue;
                $space += filesize("$dir/$file");
                $num++;
            }
        }
        
        if($space > 1024 * 1024 * 1024) {
            $space = round($space /(1024 * 1024 * 1024), 3) . "G";
        } else if($space > 1024 * 1024) {
            $space = round($space /(1024 * 1024), 3) . "M";
        } else if($space > 1024) {
            $space = round($space / 1024, 3) . "k";
        }
        
        $hitrate = [];
        $ges = [];
        $statRaw = (new CacheStat())->where("type", $type)->get();
        foreach($statRaw as $raw) {
            $hitrate[] = [
                "date" => $raw->date,
                "val" => [
                    round(100 * $raw->hits / ($raw->hits + $raw->misses)),
                ],
            ];
            
            $ges[] = [
                "date" => $raw->date,
                "val" => [
                    $raw->hits,
                    $raw->misses,
                ],
            ];
        }
        
        $charts = $this->generateChart($name . "Hit", "Hitrate", ["%"], $hitrate, 100);
        $charts .= $this->generateChart($name . "Ges", "", ["Hits", "Miss"], $ges);
        
        return [
            'size' => $space,
            'num' => $num,
            'charts' => $charts,
        ];
    }
    
    private function generateChart($name, $chartTitle, $chartTypes, $rawData, $max=null) {
        $chart = \Lava::DataTable();

        $chart->addDateColumn('Tag');
        foreach($chartTypes as $chartType) {
            $chart->addNumberColumn($chartType);
        }

        $i = 0;
        foreach ($rawData as $data){
            $chart->addRow(array_merge([$data["date"]], $data["val"]));
        }

        \Lava::LineChart($name, $chart, [
            'legend' => 'none',
            'hAxis' => [
                'format' => 'dd/MM'
            ],
            'vAxis' => [
                'direction' => 1,
                'format' => '',
                'minValue' => 0,
                'maxValue' => $max,
            ]
        ]);

        return \Lava::render('LineChart', $name, $name);
    }
}
