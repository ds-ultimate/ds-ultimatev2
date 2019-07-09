<?php

namespace App;

use App\Util\BasicFunctions;
use Illuminate\Database\Eloquent\Model;

class World extends Model
{
    public $connection = 'main';
    protected $table = 'worlds';

    /*
     * Verbindet die world Tabelle mit der server Tabelle
     */
    public function server()
    {
        return $this->belongsTo('App\Server','server_id');
    }

    /*
     * Prüft ob der Server 'de' vorhanden ist, in dem er die Tabelle worlds durchsucht.
     * Falls er keine Welt mit 'de' am Anfang findet gibt er eine Fehlermeldung zurück.
     */
    public static function existServer($server){
        if(Server::getQuery()->where("code", "=", $server)->get()->count() > 0){
            return true;
        }

        //TODO: View ergänzen für Fehlermeldungen
        echo "Keine Daten über diesen Server '$server' vorhanden.";
        exit;
    }

    /*
     * Prüft ob die Welt 'de164' vorhanden ist, in dem er die Tabelle worlds durchsucht.
     * Falls er keine Welt mit 'de164' findet gibt er eine Fehlermeldung zurück.
     */
    public static function existWorld($server, $world){
        World::existServer($server);
        if(World::where('name', $world)->get()->count() > 0){
            return true;
        }
        //TODO: View ergänzen für Fehlermeldungen
        echo "Keine Daten über diese Welt '$server$world' vorhanden.";
        exit;
    }

    public static function getWorld($server, $world){
        $serverData = Server::getServerByCode($server);
        return World::where('name', $world)->where('server_id', $serverData->id)->first();
    }

    /*
     * Sucht alle Welten mit dem entsprechendem ISO.
     * */
    public static function worldsByServer($server){
        return Server::getWorldsByCode($server);
    }

    /*
     * Gibt ein Collection-Objekt zurück.
     * */
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
    
    public function displayName()
    {
        return $this->type() . " " . $this->num();
    }
    
    public function num()
    {
        return BasicFunctions::getWorldNum($this->name);
    }
    
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
}
