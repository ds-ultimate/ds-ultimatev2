<?php

namespace App;

use App\World;
use App\Util\BasicFunctions;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class Player extends CustomModel
{
    protected $primaryKey = 'playerID';
    protected $fillable = [
        'playerID',
        'name',
        'ally_id',
        'village_count',
        'points',
        'rank',
        'offBash',
        'offBashRank',
        'defBash',
        'defBashRank',
        'supBash',
        'supBashRank',
        'gesBash',
        'gesBashRank',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];
    
    public $timestamps = true;
    
    protected $defaultTableName = "player_latest";

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
    public function allyLatest()
    {
        return $this->mybelongsTo('App\Ally', 'ally_id', 'allyID', $this->getRelativeTable("ally_latest"));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allyChanges()
    {
        return $this->myhasMany('App\AllyChanges', 'player_id', 'playerID', $this->getRelativeTable("ally_changes"));
    }

    /**
     * Gibt die Top 10 Spieler zurück
     *
     * @param World $world
     * @return Collection
     */
    public static function top10Player(World $world){
        $playerModel = new Player($world);
        return $playerModel->orderBy('rank')->limit(10)->get();
    }

    /**
     * @param World $world
     * @param int $player
     * @return $this
     */
    public static function player(World $world, $player){
        $playerModel = new Player($world);
        return $playerModel->find($player);
    }

    public function follows(){
        return $this->morphToMany('App\User', 'followable', 'follows');
    }

    /**
     * @param World $world
     * @param int $playerID
     * @return \Illuminate\Support\Collection
     */
    public static function playerDataChart(World $world, $playerID, $dayDelta = 30){
        $playerID = (int) $playerID;
        $tabelNr = $playerID % $world->hash_player;
        $playerModel = new Player($world, "player_$tabelNr");
        $playerDataArray = $playerModel
                ->where('playerID', $playerID)
                ->orderBy('updated_at', 'ASC')->get();

        $playerDatas = [];
        if(count($playerDataArray) < 1) {
            return $playerDatas;
        }
        
        $earliestDate = $playerDataArray[count($playerDataArray) - 1]->updated_at->subDays($dayDelta);
        foreach ($playerDataArray as $player){
            if($player->updated_at->lt($earliestDate)) continue;
            
            $playerDatas[] = [
                'timestamp' => (int)$player->updated_at->timestamp,
                'points' => $player->points,
                'rank' => $player->rank,
                'village' => $player->village_count,
                'gesBash' => $player->gesBash,
                'offBash' => $player->offBash,
                'defBash' => $player->defBash,
                'supBash' => $player->supBash,
            ];
        }

        return $playerDatas;

    }

    public function link($world) {
        return BasicFunctions::linkPlayer($world, $this->playerID, BasicFunctions::outputName($this->name));
    }

    public function linkIngame(World $world, $guest=false) {
        $guestPart = "game";
        if($guest) {
            $guestPart = "guest";
        }

        return "{$world->url}/$guestPart.php?screen=info_player&id={$this->playerID}";
    }

    private $playerHistCache = [];
    public function playerHistory($days, World $world){
        if(! isset($this->playerHistCache[$days])) {
            $tableNr = $this->playerID % $world->hash_player;
            
            $playerModel = new Player($world, "player_$tableNr");
            $timestamp = Carbon::now()->subDays($days);
            $this->playerHistCache[$days] =  $playerModel->where('playerID', $this->playerID)
                    ->whereDate('updated_at', $timestamp->toDateString())
                    ->orderBy('updated_at', 'DESC')
                    ->first();
        }
        return $this->playerHistCache[$days];
    }

    public function signature() {
        return $this->morphMany('App\Signature', 'element');
    }

    public function getSignature(World $worldData) {
        $sig = $this->morphOne('App\Signature', 'element')->where('world_id', $worldData->id)->first();
        if($sig != null) {
            return $sig;
        }

        $sig = new Signature();
        $sig->world_id = $worldData->id;
        $this->signature()->save($sig);
        return $sig;
    }
}
