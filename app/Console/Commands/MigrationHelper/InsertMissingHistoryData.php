<?php

namespace App\Console\Commands\MigrationHelper;

use App\Ally;
use App\Console\DatabaseUpdate\TableGenerator;
use App\HistoryIndex;
use App\Player;
use App\Village;
use App\World;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InsertMissingHistoryData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:insertMissingHistoryData {server} {world}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Füllt die _history tabellen mit werten aus den 30-Tage history Tabellen';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('max_execution_time', 1800);
        ini_set('memory_limit', '10000M');
        
        $server = $this->argument('server');
        $world = $this->argument('world');
        $worldMod = World::getWorld($server, $world);
        
        if($worldMod !== null) {
            $this->insertMissing($worldMod);
        } else {
            echo "Unable to find $server $world";
        }
        
        return 0;
    }
    
    private function insertMissing(World $worldModel) {
        $dbName = BasicFunctions::getDatabaseName($worldModel->server->code, $worldModel->name);
        echo "Doing world $dbName\n";
        if (DB::statement('CREATE DATABASE ' . $dbName . '_history') !== true) {
            echo("DB '$dbName\_history' konnte nicht erstellt werden.");
        }
        TableGenerator::historyIndexTable($dbName . "_history");

        $dates = self::getDates($dbName);
        $progLen = 3;
        $progLen+= 2 * config('dsUltimate.hash_ally');
        $progLen+= 2 * config('dsUltimate.hash_player');
        $progLen+= config('dsUltimate.hash_village');
        $progLen+= 3 * count($dates);
        $bar = $this->output->createProgressBar($progLen);
        
        echo "Doing 30-Ally\n";
        $datesAlly = static::doInserting($dbName, config('dsUltimate.hash_ally'), "ally", new Ally(),
            function($db, $tbl) {
                TableGenerator::allyLatestTable($db, $tbl);
            }, $bar);
        
        echo "Doing 30-Player\n";
        $datesPlayer = static::doInserting($dbName, config('dsUltimate.hash_player'), "player", new Player(),
            function($db, $tbl) {
                TableGenerator::playerLatestTable($db, $tbl);
            }, $bar);
        
        static::specialVillageInsert($dbName, $datesAlly, $datesPlayer, $dates, $bar);
        
        $bar->finish();
        echo "\n";
        
        foreach($dates as $date) {
            $histIdx = new HistoryIndex();
            $histIdx->setTable($dbName . "_history.index");
            $histIdx->date = $date;
            $histIdx->save();
        }
    }
    
    private static function doInserting($dbName, $hashCnt, $type, $model, $tableCreateCallback, $bar) {
        $timestampsUsed = [];
        $datesUsed = [];
        for ($num = 0; $num < $hashCnt; $num++){
            if(!BasicFunctions::existTable($dbName, "{$type}_$num")) {
                continue;
            }
            $res = DB::select("SELECT * FROM $dbName.{$type}_$num ORDER BY 'created_at' ASC");
            
            $toInsert = [];
            foreach($res as $entry) {
                $entryDate = explode(" ", $entry->created_at)[0];
                $use = false;
                if(! in_array($entryDate, $datesUsed)) {
                    $datesUsed[] = $entryDate;
                    $timestampsUsed[] = $entry->created_at;
                    $use = true;
                } else if(in_array($entry->created_at, $timestampsUsed)) {
                    $use = true;
                }
                
                if($use) {
                    if(! isset($toInsert[$entryDate])) {
                        $toInsert[$entryDate] = [];
                    }
                    $toInsert[$entryDate][] = (array) $entry;
                }
            }
            
            $runningData = [];
            foreach($toInsert as $key => $value) {
                if(! BasicFunctions::existTable("{$dbName}_history", "{$type}_$key")) {
                    $tableCreateCallback("{$dbName}_history", $key);
                }
                $model->setTable("{$dbName}_history.{$type}_$key");
                if($type == "village") {
                    $value = static::customMerge($runningData, $value);
                    $runningData = $value;
                }
                foreach(array_chunk($value, 3000) as $data) {
                    $model->insert($data);
                }
            }
            $bar->advance();
        }
        
        return $datesUsed;
    }
    
    private static function specialVillageInsert($dbName, $datesAlly, $datesPlayer, $dates, $bar) {
        foreach(array_merge($datesAlly, $datesPlayer) as $date) {
            if(!in_array($date, $dates)) {
                echo "Player / Ally has date $date, but not in found dates\n";
            }
        }
        $bar->advance();
        
        //fetch player names
        echo "Reading Player\n";
        $playerNames = [];
        for($num = 0; $num < config('dsUltimate.hash_player'); $num++) {
            if(!BasicFunctions::existTable($dbName, "player_$num")) {
                continue;
            }
            
            $res = DB::select("SELECT * FROM $dbName.player_$num ORDER BY 'created_at' ASC");
            foreach($res as $entry) {
                $playerNames[$entry->playerID] = $entry->name;
            }
            $bar->advance();
        }
        
        //fetch ally names
        echo "Reading Ally\n";
        $allyNames = [];
        for($num = 0; $num < config('dsUltimate.hash_ally'); $num++) {
            if(! BasicFunctions::existTable($dbName, "ally_$num")) {
                continue;
            }
            
            $res = DB::select("SELECT * FROM $dbName.ally_$num ORDER BY 'created_at' ASC");
            foreach($res as $entry) {
                $allyNames[$entry->allyID] = [
                    $entry->name,
                    $entry->tag,
                ];
            }
            $bar->advance();
        }
        
        //fill this information with conquers
        $playerAlly = [];
        foreach($dates as $date) {
            $playerAlly[$date] = [];
        }
        
        echo "Reading Conquer\n";
        $res = DB::select("SELECT * FROM $dbName.conquer ORDER BY 'timestamp' ASC");
        foreach($res as $entry) {
            $date = Carbon::createFromTimestamp($entry->timestamp)->format("Y-m-d");
            if(!in_array($date, $dates)) {
                echo "Conquer has date $date, but not in found dates\n";
                print_r($entry);
                continue;
            }
            $playerAlly[$date][$entry->old_owner] = $entry->old_ally;
            $playerAlly[$date][$entry->new_owner] = $entry->new_ally;
            
            $playerNames[$entry->old_owner] = $entry->old_owner_name;
            $playerNames[$entry->new_owner] = $entry->new_owner_name;
            $allyNames[$entry->old_ally] = [
                $entry->old_ally_name,
                $entry->old_ally_tag,
            ];
            $allyNames[$entry->new_ally] = [
                $entry->new_ally_name,
                $entry->new_ally_tag,
            ];
        }
        $bar->advance();
        
        echo "Reading Ally Changes\n";
        $res = DB::select("SELECT * FROM $dbName.ally_changes ORDER BY 'timestamp' ASC");
        foreach($res as $entry) {
            $date = explode(" ", $entry->created_at)[0];
            $playerAlly[$date][$entry->player_id] = $entry->new_ally_id;
        }
        $bar->advance();
        
        $keys = array_keys($playerAlly);
        sort($keys);
        $running = [];
        foreach($keys as $key) {
            foreach($playerAlly[$key] as $id => $ally) {
                $running[$id] = $ally;
            }
            $playerAlly[$key] = $running;
        }
        unset($keys);
        unset($running);
        
        $villages = [];
        $timestampsUsed = [];
        foreach($dates as $date) {
            $villages[$date] = [];
        }
        
        echo "Reading Village\n";
        for ($num = 0; $num < config('dsUltimate.hash_village'); $num++){
            if(!BasicFunctions::existTable($dbName, "village_$num")) {
                continue;
            }
            
            $res = DB::select("SELECT * FROM $dbName.village_$num ORDER BY 'created_at' ASC");
            foreach($res as $entry) {
                $entryDate = explode(" ", $entry->created_at)[0];
                
                if(! isset($timestampsUsed[$entryDate])) {
                    $timestampsUsed[$entryDate] = $entry->created_at;
                }
                if($timestampsUsed[$entryDate] == $entry->created_at) {
                    $villages[$entryDate][$entry->villageID] = (array) $entry;
                } else if(! isset($villages[$entryDate][$entry->villageID])) {
                    $villages[$entryDate][$entry->villageID] = (array) $entry;
                }
            }
            $bar->advance();
        }
        unset($res);
        
        $keys = array_keys($villages);
        sort($keys);
        $running = [];
        foreach($keys as $key) {
            foreach($villages[$key] as $id => $vill) {
                $running[$id] = $vill;
            }
            $villages[$key] = $running;
        }
	//dd($keys, $dates);
        unset($running);
        
        $finalPlayerData = [];
        $finalAllyData = [];
        foreach($dates as $date) {
            $finalPlayerData[$date] = [];
            $finalAllyData[$date] = [];
        }
        $insertTime = Carbon::now();
        
        echo "Writing Village\n";
        foreach($keys as $key) {
            foreach($villages[$key] as $id => $vill) {
                if($vill['owner'] == 0) continue;
                
                if(! isset($finalPlayerData[$key][$vill['owner']])) {
                    $finalPlayerData[$key][$vill['owner']] = [
                        'playerID' => $vill['owner'],
                        'name' => $playerNames[$vill['owner']] ?? "Unknown " . $vill['owner'],
                        'ally_id' => $playerAlly[$key][$vill['owner']] ?? 0,
                        'village_count' => 0,
                        'points' => 0,
                        'rank' => null,
                        'offBash' => 0,
                        'offBashRank' => 0,
                        'defBash' => 0,
                        'defBashRank' => 0,
                        'supBash' => 0,
                        'supBashRank' => 0,
                        'gesBash' => 0,
                        'gesBashRank' => 0,
                        'created_at' => $insertTime,
                        'updated_at' => $insertTime,
                    ];
                }
                
                $finalPlayerData[$key][$vill['owner']]['village_count']++;
                $finalPlayerData[$key][$vill['owner']]['points'] += $vill['points'];
            }
            
            usort($finalPlayerData[$key], function($a, $b) {
                if($a['points'] == $b['points']) return 0;
                return ($a['points'] > $b['points']) ? -1 : 1;
            });
            
            TableGenerator::villageLatestTable($dbName."_history", $key);
            $model = new Village();
            $model->setTable("{$dbName}_history.village_$key");
            foreach(array_chunk($villages[$key], 3000) as $chunk) {
                $model->insert($chunk);
            }
            $bar->advance();
        }
        unset($villages);
        
        echo "Writing Player\n";
        foreach($keys as $key) {
            if(in_array($key, $datesPlayer)) continue;
            
            foreach($finalPlayerData[$key] as $idx => $player) {
                $finalPlayerData[$key][$idx]['rank'] = $idx;
                if($player['ally_id'] == 0) continue;
                
                if(! isset($finalAllyData[$key][$player['ally_id']])) {
                    $nameArr = $allyNames[$player['ally_id']] ?? [
                        "Unknown " . $player['ally_id'],
                        "Unkn",
                    ];
                    
                    $finalAllyData[$key][$player['ally_id']] = [
                        'allyID' => $player['ally_id'],
                        'name' => $nameArr[0],
                        'tag' => $nameArr[1],
                        'member_count' => 0,
                        'points' => 0,
                        'village_count' => 0,
                        'rank' => null,
                        'offBash' => 0,
                        'offBashRank' => 0,
                        'defBash' => 0,
                        'defBashRank' => 0,
                        'gesBash' => 0,
                        'gesBashRank' => 0,
                        'created_at' => $insertTime,
                        'updated_at' => $insertTime,
                    ];
                }
                
                $finalAllyData[$key][$player['ally_id']]['member_count']++;
                $finalAllyData[$key][$player['ally_id']]['village_count'] += $player['village_count'];
                $finalAllyData[$key][$player['ally_id']]['points'] += $player['points'];
            }
            
            usort($finalAllyData[$key], function($a, $b) {
                if($a['points'] == $b['points']) return 0;
                return ($a['points'] > $b['points']) ? -1 : 1;
            });
            
            TableGenerator::playerLatestTable($dbName."_history", $key);
            $model = new Player();
            $model->setTable("{$dbName}_history.player_$key");
            foreach(array_chunk($finalPlayerData[$key], 3000) as $chunk) {
                $model->insert($chunk);
            }
            $bar->advance();
        }
        unset($finalPlayerData);
        
        echo "Writing Ally\n";
        foreach($keys as $key) {
            if(in_array($key, $datesAlly)) continue;
            
            foreach($finalAllyData[$key] as $idx => $ally) {
                $finalAllyData[$key][$idx]['rank'] = $idx;
            }
            
            TableGenerator::allyLatestTable($dbName."_history", $key);
            $model = new Ally();
            $model->setTable("{$dbName}_history.ally_$key");
            foreach(array_chunk($finalAllyData[$key], 3000) as $chunk) {
                $model->insert($chunk);
            }
            $bar->advance();
        }
        
        return $dates;
    }
    
    private static function customMerge($arr1, $arr2) {
        //for better runtime complexity / performance we should change both arrays to dicts
        //merge them
        //change back to arrays
        
        $dict1 = static::toVillageDict($arr1);
        $dict2 = static::toVillageDict($arr2);
        $dictRes = array_merge($dict1, $dict2);
        
        $res = [];
        foreach($dictRes as $key => $value) {
            $res[] = $value;
        }
        return $res;
    }
    
    private static function toVillageDict($input) {
        $output = [];
        foreach($input as $in) {
            $output[" " . $in['villageID']] = $in;
        }
        return $output;
    }
    
    private static function getDates($dbName) {
        $res = (new Village())->setTable("$dbName.village_1")->orderBy('created_at', 'ASC')->first();
        $dates = [];
        $tempCarb = Carbon::parse($res->created_at);
        $target = Carbon::now()->format("Y-m-d");
        $tempStr = "";
        while($target !== $tempStr) {
            $tempStr = $tempCarb->format("Y-m-d");
            $dates[] = $tempStr;
            $tempCarb = $tempCarb->addDay();
        }
        return $dates;
    }
}
