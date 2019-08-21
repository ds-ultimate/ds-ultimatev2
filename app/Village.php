<?php

namespace App;

use App\Util\BasicFunctions;

class Village extends CustomModel
{
    private $hash = 109;
    protected $primaryKey = 'villageID';
    protected $fillable =[
            'id', 'name', 'x', 'y', 'points', 'owner', 'bonus_id',
    ];
    
    public $timestamps = true;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

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
     * @param string $server
     * @param $world
     * @param int $village
     * @return $this
     */
    public static function village($server, $world, $village){
        $villageModel = new Village();
        $villageModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.village_latest');

        return $villageModel->find($village);
    }

    /**
     * @param string $server
     * @param $world
     * @param int $villageID
     * @return \Illuminate\Support\Collection
     */
    public static function villageDataChart($server, $world, $villageID){
        $tabelNr = $villageID % config('dsUltimate.hash_village');
        $villageModel = new Village();
        $villageModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.village_'.$tabelNr);
        
        $villageDataArray = $villageModel->where('villageID', $villageID)->orderBy('updated_at', 'ASC')->get();
        $villageDatas = collect();

        foreach ($villageDataArray as $village){
            $villageData = collect();
            $villageData->put('timestamp', (int)$village->updated_at->timestamp);
            $villageData->put('points', $village->points);
            $villageDatas->push($villageData);
        }

        return $villageDatas;
    }

    /**
     * @return string
     */
    public function bonusText() {
        switch($this->bonus_id) {
            case 0:
                return "-";
            case 1:
                return "+100% Holz";
            case 2:
                return "+100% Lehm";
            case 3:
                return "+100% Eisen";
            case 4:
                return "+10% Bevölkerung";
            case 5:
                return "+33% schnellere Kaserne";
            case 6:
                return "+33% schnellerer Stall";
            case 7:
                return "+50% schnellere Werkstatt";
            case 8:
                return "+30% auf alle Rohstoffe";
            case 9:
                return "+50% Händler & Speicher";
        }
        return '-';
    }

    /**
     * @return string
     */
    public function continentString() {
        return "K" . intval($this->x / 100) . intval($this->y / 100);
    }

    /**
     * @return string
     */
    public function coordinates() {
        return $this->x."|".$this->y;
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
}
