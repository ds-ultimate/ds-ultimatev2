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
     * Prüft ob der Server 'de' vorhanden ist, in dem er die Tabelle worlds durchsucht.
     * Falls er keine Welt mit 'de' am Anfang findet gibt er eine Fehlermeldung zurück.
     *
     * @param string $server
     * @return bool
     */
    public static function existServer($server){
        if(Server::getQuery()->where("code", "=", $server)->get()->count() > 0){
            return true;
        }

        abort(404, "Keine Daten über diesen Server '$server' vorhanden.");
    }

    /**
     * Prüft ob die Welt 'de164' vorhanden ist, in dem er die Tabelle worlds durchsucht.
     * Falls er keine Welt mit 'de164' findet gibt er eine Fehlermeldung zurück.
     *
     * @param string $server
     * @param $world
     * @return bool
     */
    public static function existWorld($server, $world){
        World::existServer($server);
        $serverData = Server::getServerByCode($server);
        if(World::where('name', $world)->where('server_id', $serverData->id)->get()->count() > 0){
            return true;
        }

        abort(404, "Keine Daten über diese Welt '$server$world' vorhanden.");
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
     * Sucht alle Welten mit dem entsprechendem ISO.
     *
     * @param $server
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function worldsByServer($server){
        return Server::getWorldsByCode($server);
    }

    /**
     * Gibt ein Collection-Objekt zurück indem sich alle Welten eines Servers befinden.
     *
     * @param string $server
     * @return \Illuminate\Support\Collection
     */
    public static function worldsCollection($server){
        $worldsArray = [];

        foreach (Server::getWorldsByCode($server) as $worldData){
            if (! isset($worldsArray[$worldData->sortType()])) {
                $worldsArray[$worldData->sortType()] = [];
            }
            $worldsArray[$worldData->sortType()][] = $worldData;
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
        return BasicFunctions::getWorldNum($this->name);
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

    public function unitConfig(){
        return simplexml_load_string($this->units);
    }
}
