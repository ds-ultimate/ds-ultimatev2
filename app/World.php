<?php

namespace App;

use App\Util\BasicFunctions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class World extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'worlds';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
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
        if(World::where('name', $world)->get()->count() > 0){
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
        $worldsArray = collect();

        foreach (Server::getWorldsByCode($server) as $worldData){
            if (! $worldsArray->has($worldData->sortType())) {
                $worldsArray[$worldData->sortType()] = collect();
            }
            $worldsArray[$worldData->sortType()]->push($worldData);
        }
        return $worldsArray;
    }

    public static function worldsCollectionActiveSorter(Collection $worldTypes){
        $collect = collect();
        $active = collect();
        $inactive = collect();
        foreach ($worldTypes as $key => $worldType){
            foreach ($worldType as $world){
                if ($world->active){
                    if (! $active->has($key)) {
                        $active[$key] = collect();
                    }
                    $active[$key]->push($world);
                }else{
                    if (! $inactive->has($key)) {
                        $inactive[$key] = collect();
                    }
                    $inactive[$key]->push($world);
                }
            }
        }
        $collect->put('active', $active);
        $collect->put('inactive', $inactive);
        return $collect;
    }

    /**
     * Giebt den Welten-Typ zurück.
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
        if(strpos($this->name, 'p') !== false){
            return "casual";
        }elseif(strpos($this->name, 's') !== false){
            return "speed";
        }elseif(strpos($this->name, 'c') !== false){
            return "classic";
        }else{
            return "world";
        }
    }

    /**
     * Estellt den anzuzeigenden Namen.
     * z.B. Welt 164 || Casual 11
     *
     * @return string
     */
    public function displayName()
    {
        return $this->type() . " " . $this->num();
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
        if(strpos($this->name, 'p') !== false){
            return ucfirst(__('ui.world.casual'));
        }elseif(strpos($this->name, 's') !== false){
            return ucfirst(__('ui.world.speed'));
        }elseif(strpos($this->name, 'c') !== false){
            return ucfirst(__('ui.world.classic'));
        }else{
            return ucfirst(__('ui.world.normal'));
        }
    }

    public function unitConfig(){
        return simplexml_load_string($this->units);
    }
}
