<?php

namespace App;

use App\Util\BasicFunctions;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class Player extends CustomModel
{
    private $hash = 59;
    protected $primaryKey = 'playerID';
    protected $fillable =[
            'id', 'name', 'ally', 'village', 'points', 'rank', 'off', 'offR', 'def', 'defR', 'tot', 'totR',
    ];

    public $timestamps = true;

    /**
     * Player constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->hash = config('dsUltimate.hash_player');

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
    public function allyLatest()
    {
        $table = explode('.', $this->table);
        return $this->mybelongsTo('App\Ally', 'ally_id', 'allyID', $table[0].'.ally_latest');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allyChanges()
    {
        $table = explode('.', $this->table);
        return $this->myhasMany('App\AllyChanges', 'player_id', 'playerID', $table[0].'.ally_changes');
    }

    /**
     * @param string $server
     * @param $world
     * @return Collection
     */
    public static function getAllPlayer($server, $world){
        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_latest');

        return $playerModel->orderBy('rank')->get();
    }

    /**
     * @param string $server
     * @param $world
     * @param string $order
     * @param int $page
     * @return Collection
     */
    public static function getAllPlayer20($server, $world, $order, $page){
        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_latest');

        return $playerModel->where($order, '>', $page*20-20)->orderBy($order)->limit(20)->get();
    }

    /**
     * Gibt die Top 10 Spieler zurück
     *
     * @param string $server
     * @param $world
     * @return Collection
     */
    public static function top10Player($server, $world){
        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_latest');

        return $playerModel->orderBy('rank')->limit(10)->get();
    }

    /**
     * @param string $server
     * @param $world
     * @param int $player
     * @return $this
     */
    public static function player($server, $world, $player){
        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_latest');

        return $playerModel->find($player);
    }

    /**
     * @param string $server
     * @param $world
     * @param int $playerID
     * @return \Illuminate\Support\Collection
     */
    public static function playerDataChart($server, $world, $playerID, $dayDelta = 30){
        $playerID = (int) $playerID;
        $tabelNr = $playerID % config('dsUltimate.hash_player');
        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_'.$tabelNr);
        $time = Carbon::createFromTimestamp(time());
        $playerDataArray = $playerModel->where('playerID', $playerID)->where('created_at', '>', $time->subDays($dayDelta+1))->orderBy('updated_at', 'ASC')->get();
        if ($dayDelta){

        }
        $playerDatas = collect();

        foreach ($playerDataArray as $player){
            $playerData = collect();
            $playerData->put('timestamp', (int)$player->updated_at->timestamp);
            $playerData->put('points', $player->points);
            $playerData->put('rank', $player->rank);
            $playerData->put('village', $player->village_count);
            $playerData->put('gesBash', $player->gesBash);
            $playerData->put('offBash', $player->offBash);
            $playerData->put('defBash', $player->defBash);
            $playerData->put('utBash', $player->gesBash-$player->offBash-$player->defBash);
            $playerDatas->push($playerData);
        }

        return $playerDatas;

    }

    /**
     * @return string
     */
    public function nameWithAlly() {
        if ($this->ally_id != 0) {
            return $this->name . " [" . $this->allyLatest->tag . "]";
        }
        else {
            return $this->name . " [-]";
        }
    }
    
    public function linkIngame(World $world, $guest=false) {
        $guestPart = "game";
        if($guest) {
            $guestPart = "guest";
        }
            
        return "{$world->url}/$guestPart.php?screen=info_player&id={$this->playerID}";
    }

    public function playerHistory($days){
        $tableNr = $this->playerID % config('dsUltimate.hash_player');
        $dbName = explode('.', $this->getTable());

        $playerModel = new Player();
        $playerModel->setTable($dbName[0].'.player_'.$tableNr);
        $timestamp = Carbon::now()->subDays($days);
        return $playerModel->where('playerID', $this->playerID)->whereDate('updated_at', $timestamp->toDateString())->orderBy('updated_at', 'DESC')->first();
    }

}
