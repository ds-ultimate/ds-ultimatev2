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

    protected $dates = [
        'updated_at',
        'created_at',
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

    public function follows(){
        return $this->morphToMany('App\User', 'followable', 'follows');
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
        $playerDataArray = $playerModel
                ->where('playerID', $playerID)
                ->orderBy('updated_at', 'ASC')->get();

        $playerDatas = [];
        foreach ($playerDataArray as $player){
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

    public function playerHistory($days){
        $tableNr = $this->playerID % config('dsUltimate.hash_player');
        $dbName = explode('.', $this->getTable());

        $playerModel = new Player();
        $playerModel->setTable($dbName[0].'.player_'.$tableNr);
        $timestamp = Carbon::now()->subDays($days);
        return $playerModel->where('playerID', $this->playerID)->whereDate('updated_at', $timestamp->toDateString())->orderBy('updated_at', 'DESC')->first();
    }

    public function signature() {
        return $this->morphMany('App\Signature', 'element');
    }

    public function getSignature(World $worldData) {
        $sig = $this->morphOne('App\Signature', 'element')->where('worlds_id', $worldData->id)->first();
        if($sig != null) {
            return $sig;
        }

        $sig = new Signature();
        $sig->worlds_id = $worldData->id;
        $this->signature()->save($sig);
        return $sig;
    }
}
