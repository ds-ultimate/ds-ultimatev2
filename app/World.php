<?php

namespace App;

use App\Util\BasicFunctions;
use Illuminate\Database\Eloquent\SoftDeletes;

class World extends CustomModel
{
    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'worlds';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'worldTop_at',
        'worldUpdated_at',
        'worldCleaned_at',
    ];

    protected $fillable = [
        'id',
        'server_id',
        'name',
        'ally_count',
        'player_count',
        'village_count',
        'url',
        'config',
        'units',
        'buildings',
        'active',
        'display_name',
        'maintananceMode',
        'win_condition',
        'hash_ally',
        'hash_player',
        'hash_village',
    ];
    
    protected $cache = [
        'server',
    ];

    /**
     * Verbindet die world Tabelle mit der server Tabelle
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function server(){
        return $this->belongsTo('App\Server','server_id');
    }

    /**
     * Gibt eine bestimmte Welt zurück.
     *
     * @param string $server
     * @param $world
     * @return World
     */
    public static function getWorld($server, $world){
        $serverData = Server::getServerByCode($server);
        return World::where('name', $world)->where('server_id', $serverData->id)->first();
    }

    /**
     * Schaut ob die Welt existiert und falls ja gibt diese zurück,
     * sonst wird ein Error 404 zurück gegeben
     *
     * @param string $server
     * @param $world
     * @return World
     */
    public static function getAndCheckWorld($server, $world){
        if($server instanceof Server) {
            $serverData = $server;
        } else {
            $serverData = Server::getAndCheckServerByCode($server);
        }
        $worldData = World::where('name', $world)->where('server_id', $serverData->id)->first();
        abort_if($worldData == null, 404, __("ui.errors.404.noWorld", ["world" => "{$serverData->code}$world"]));
        abort_if($worldData->maintananceMode, 503);
        return $worldData;
    }

    /**
     * Gibt ein Collection-Objekt zurück indem sich alle Welten eines Servers befinden.
     *
     * @param string $server
     * @return \Illuminate\Support\Collection
     */
    public static function worldsCollection($server, $mapping=[]){
        $worldsArray = [];

        foreach (Server::getWorlds($server) as $worldData){
            $type = $worldData->sortType();
            if(isset($mapping[$type])) {
                $type = $mapping[$type];
            }
            
            if (! isset($worldsArray[$type])) {
                $worldsArray[$type] = [];
            }
            $worldsArray[$type][] = $worldData;
        }
        return $worldsArray;
    }

    public static function worldsCollectionActiveSorter($worldTypes){
        $collect = [];
        $active = [];
        $inactive = [];
        foreach ($worldTypes as $key => $worldType){
            foreach ($worldType as $world){
                if ($world->active){
                    if (! isset($active[$key])) {
                        $active[$key] = [];
                    }
                    $active[$key][] = $world;
                }else{
                    if (! isset($inactive[$key])) {
                        $inactive[$key] = [];
                    }
                    $inactive[$key][] = $world;
                }
            }
        }
        $collect['active'] = $active;
        $collect['inactive'] = $inactive;
        return $collect;
    }

    /**
     * Gibt den Welten-Typ zurück.
     *
     * @return string
     */
    public function sortType()
    {
        /*
         * Setzt den Welten Type:
         * dep => Casual
         * des => Speed
         * dec => Classic
         * de => Welt
         */
        if($this->isSpeed()){
            return "speed";
        } elseif($this->isCasual()){
            return "casual";
        } elseif($this->isNormalServer()){
            return "world";
        } else{
            return "classic";
        }
    }

    /**
     * Estellt den anzuzeigenden Namen.
     * z.B. Welt 164 || Casual 11
     *
     * @return string
     */
    public function generateDisplayName() 
    {
        return $this->type() . " " . $this->num();
    }
    
    public function shortName() {
        if($this->isSpeed()) {
            return $this->generateDisplayName();
        }
        return $this->display_name;
    }

    /**
     * @return int
     */
    public function num()
    {
        return (int)preg_replace("/[^0-9]+/", '', $this->name);
    }

    /**
     * @return string
     */
    public function type()
    {
        /*
         * Setzt den Welten Type:
         * dep => Casual
         * des => Speed
         * dec => Classic
         * de => Welt
         */
        if($this->isSpeed()){
            return ucfirst(__('ui.world.speed'));
        } elseif($this->isCasual()){
            return ucfirst(__('ui.world.casual'));
        } elseif($this->isNormalServer()){
            return ucfirst(__('ui.world.normal'));
        } else{
            return ucfirst(__('ui.world.classic'));
        }
    }
    
    public function isSpecialServer() {
        return static::isSpecialServerName($this->name);
    }
    
    public static function isSpecialServerName($name) {
        return static::isSpeedName($name) || static::isClassicServerName($name);
    }
    
    public function isSpeed() {
        return static::isSpeedName($this->name);
    }
    
    public static function isSpeedName($name) {
        return BasicFunctions::startsWith($name, 's');
    }
    
    public function isClassicServer() {
        return static::isClassicServerName($this->name);
    }
    
    public static function isClassicServerName($name) {
        return !static::isNormalServerName($name) && !static::isCasualName($name) && !static::isSpeedName($name);
    }
    
    public function isCasual() {
        return static::isCasualName($this->name);
    }
    
    public static function isCasualName($name) {
        return BasicFunctions::startsWith($name, 'p');
    }
    
    public function isNormalServer() {
        return static::isNormalServerName($this->name);
    }
    
    public static function isNormalServerName($name) {
        return preg_match("/^\d+$/", $name);
    }

    private $unitConfCache = null;
    public function unitConfig(){
        if($this->unitConfCache == null) {
            $this->unitConfCache = simplexml_load_string($this->units);
        }
        return $this->unitConfCache;
    }

    public function save(array $options = []) {
        if($this->config == null) {
            $this->win_condition = -1;
        } else {
            $this->win_condition = simplexml_load_string($this->config)->win->check;
        }
        return parent::save($options);
    }

    public function touch($attribute = null) {
        if($this->config == null) {
            $this->win_condition = -1;
        } else {
            $this->win_condition = simplexml_load_string($this->config)->win->check;
        }
        return parent::touch($attribute);
    }
    
    public function serName() {
        return $this->server->code . $this->name;
    }
}
