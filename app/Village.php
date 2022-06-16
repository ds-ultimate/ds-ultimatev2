<?php

namespace App;

class Village extends CustomModel
{
    private $hash = 109;
    protected $primaryKey = 'villageID';
    protected $fillable =[
            'id', 'name', 'x', 'y', 'points', 'owner', 'bonus_id',
    ];
    
    public $timestamps = true;

    public function __construct($arg1 = [], $arg2 = null)
    {
        if($arg1 instanceof World && $arg2 == null) {
            //allow calls without table name
            $arg2 = "village_latest";
        }
        parent::__construct($arg1, $arg2);

        $this->hash = config('dsUltimate.hash_village');
    }

    /**
     *@return int
     */
    public function getHash(){
        return $this->hash;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function playerLatest()
    {
        $table = explode('.', $this->table);
        return $this->mybelongsTo('App\Player', 'owner', 'playerID', $table[0].'.player_latest');
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
        $tabelNr = $villageID % config('dsUltimate.hash_village');
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
    
    public function getHistoryData() {
        $villageID = (int) $this->villageID;
        $tableNr = $villageID % config('dsUltimate.hash_village');
        $villageModel = new Village();
        $villageModel->setTable(str_replace("latest", $tableNr, $this->getTable()));
        
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
            case 1: return "+100% Holz";
            case 2: return "+100% Lehm";
            case 3: return "+100% Eisen";
            case 4: return "+10% Bevölkerung";
            case 5: return "+33% schnellere Kaserne";
            case 6: return "+33% schnellerer Stall";
            case 7: return "+50% schnellere Werkstatt";
            case 8: return "+30% auf alle Rohstoffe";
            case 9: return "+50% Händler & Speicher";
                
            case 11: return "-25% Verteidigungsstärke. 7 Einflusspunkte täglich.";
            case 12: return "-30% Verteidigungsstärke. 9 Einflusspunkte täglich.";
            case 13: return "-35% Verteidigungsstärke. 10 Einflusspunkte täglich.";
            case 14: return "-40% Verteidigungsstärke. 11 Einflusspunkte täglich.";
            case 15: return "-45% Verteidigungsstärke. 13 Einflusspunkte täglich.";
            case 16: return "-50% Verteidigungsstärke. 15 Einflusspunkte täglich.";
            case 17: return "-55% Verteidigungsstärke. 17 Einflusspunkte täglich.";
            case 18: return "-60% Verteidigungsstärke. 19 Einflusspunkte täglich.";
            case 19: return "-65% Verteidigungsstärke. 21 Einflusspunkte täglich.";
            case 20: return "-70% Verteidigungsstärke. 23 Einflusspunkte täglich.";
            case 21: return "-75% Verteidigungsstärke. 25 Einflusspunkte täglich.";
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
