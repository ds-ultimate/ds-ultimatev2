<?php

namespace App;

class Village extends CustomModel
{
    protected $primaryKey = 'villageID';
    protected $fillable =[
        'id', 'name', 'x', 'y', 'points', 'owner', 'bonus_id',
    ];
    
    public $timestamps = true;
    
    protected $defaultTableName = "village_latest";

    public function __construct($arg1 = [], $arg2 = null)
    {
        if($arg1 instanceof World && $arg2 == null) {
            //allow calls without table name
            $arg2 = $this->defaultTableName;
        }
        parent::__construct($arg1, $arg2);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function playerLatest()
    {
        $table = str_replace($this->defaultTableName, "player_latest", $this->table);
        return $this->mybelongsTo('App\Player', 'owner', 'playerID', $table);
    }

    /**
     * @param World $world
     * @param int $village
     * @return $this
     */
    public static function village(World $world, $village){
        $villageModel = new Village($world);
        return $villageModel->find($village);
    }

    /**
     * @param World $world
     * @param int $villageID
     * @return \Illuminate\Support\Collection
     */
    public static function villageDataChart(World $world, $villageID){
        $villageID = (int) $villageID;
        $tabelNr = $villageID % $world->hash_village;
        $villageModel = new Village($world, "village_$tabelNr");
        
        $villageDataArray = $villageModel->where('villageID', $villageID)->orderBy('updated_at', 'ASC')->get();
        return static::pureVillageDataChart($villageDataArray);
    }

    /**
     * @param array $villageDataArray
     * @return \Illuminate\Support\Collection
     */
    public static function pureVillageDataChart($villageDataArray){
        $villageDatas = [];
        foreach ($villageDataArray as $village){
            $villageDatas[] = [
                'timestamp' => (int)$village->updated_at->timestamp,
                'points' => $village->points,
            ];
        }

        return $villageDatas;
    }
    
    public function getHistoryData(World $world) {
        $villageID = (int) $this->villageID;
        $tableNr = $villageID % $world->hash_village;
        $villageModel = new Village($world, "village_$tableNr");
        
        $villageDataArray = $villageModel->where('villageID', $villageID)->orderBy('updated_at', 'ASC')->get();
        return $villageDataArray;
    }

    /**
     * @return string
     */
    public function bonusText() {
        return static::bonusTextStat($this->bonus_id);
    }
    
    /**
     * @return string
     */
    public static function bonusTextStat($bonus_id) {
        switch($bonus_id) {
            case 0: return "-";
            case 1: return __("ui.village.bonus.wood", ["amount" => "+100%"]);
            case 2: return __("ui.village.bonus.clay", ["amount" => "+100%"]);
            case 3: return __("ui.village.bonus.iron", ["amount" => "+100%"]);
            case 4: return __("ui.village.bonus.population", ["amount" => "+10%"]);
            case 5: return __("ui.village.bonus.fastBarracks", ["amount" => "+33%"]);
            case 6: return __("ui.village.bonus.fastStable", ["amount" => "+33%"]);
            case 7: return __("ui.village.bonus.fastWorkshop", ["amount" => "+50%"]);
            case 8: return __("ui.village.bonus.allResources", ["amount" => "+33%"]);
            case 9: return __("ui.village.bonus.merchants", ["amount" => "+50%"]);

            case 11: return __("ui.village.bonus.greatSiege", ["amountDef" => "-25%", "points" => 7]);
            case 12: return __("ui.village.bonus.greatSiege", ["amountDef" => "-30%", "points" => 9]);
            case 13: return __("ui.village.bonus.greatSiege", ["amountDef" => "-35%", "points" => 10]);
            case 14: return __("ui.village.bonus.greatSiege", ["amountDef" => "-40%", "points" => 11]);
            case 15: return __("ui.village.bonus.greatSiege", ["amountDef" => "-45%", "points" => 13]);
            case 16: return __("ui.village.bonus.greatSiege", ["amountDef" => "-50%", "points" => 15]);
            case 17: return __("ui.village.bonus.greatSiege", ["amountDef" => "-55%", "points" => 17]);
            case 18: return __("ui.village.bonus.greatSiege", ["amountDef" => "-60%", "points" => 19]);
            case 19: return __("ui.village.bonus.greatSiege", ["amountDef" => "-65%", "points" => 21]);
            case 20: return __("ui.village.bonus.greatSiege", ["amountDef" => "-70%", "points" => 23]);
            case 21: return __("ui.village.bonus.greatSiege", ["amountDef" => "-75%", "points" => 25]);
        }
        return '-';
    }

    /**
     * @return string
     */
    public function continentString() {
        return "K" . intval($this->y / 100) . intval($this->x / 100);
    }

    /**
     * @return string
     */
    public static function continentStringStat($x, $y) {
        return "K" . intval($y / 100) . intval($x / 100);
    }

    /**
     * @return string
     */
    public function coordinates() {
        return $this->x."|".$this->y;
    }

    /**
     * @return string
     */
    public static function coordinatesStat($x, $y) {
        return $x . "|" . $y;
    }

    /**
     * @param $skin
     * @return string|null
     */
    public function getVillageSkinImage($skin) {
        $imgName = Village::getSkinImageName($this->owner, $this->points, $this->bonus_id);
        return Village::getSkinImagePath($skin, $imgName);
    }
    
    public static function getSkinImagePath($skin, $imgName) {
        $skins = array("dark", "default", "old", "symbol", "winter");
        $index = array_search($skin, $skins);
        if($index === false){
            return null;
        }
        
        return "ds_images/skins/{$skins[$index]}/$imgName.png";
    }
    
    public static function getSkinImageName($owner, $points, $bonus_id) {
        $left = "";
        if($owner == 0) {
            $left = "_left";
        }

        if($points < 300) {
            $lv = 1;
        } else if($points < 1000) {
            $lv = 2;
        } else if($points < 3000) {
            $lv = 3;
        } else if($points < 9000) {
            $lv = 4;
        } else if($points < 11000) {
            $lv = 5;
        } else {
            $lv = 6;
        }

        $bonus = "v";
        if($bonus_id != 0) {
            $bonus = "b";
        }
        return "$bonus$lv$left";
    }
    
    public function linkIngame(World $world, $guest=false) {
        $guestPart = "game";
        if($guest) {
            $guestPart = "guest";
        }
            
        return "{$world->url}/$guestPart.php?screen=info_village&id={$this->villageID}";
    }
}
