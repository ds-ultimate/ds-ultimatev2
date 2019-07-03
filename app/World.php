<?php

namespace App;

use App\Util\BasicFunctions;
use Illuminate\Database\Eloquent\Model;

class World extends Model
{
    public $connection = 'main';

    /*
     * Prüft ob der Server 'de' vorhanden ist, in dem er die Tabelle worlds durchsucht.
     * Falls er keine Welt mit 'de' am Anfang findet gibt er eine Fehlermeldung zurück.
     */
    public static function existServer($server){
        if(World::where('name', 'LIKE', $server.'%')->get()->count() > 0){
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
        if(World::where('name', $server.$world)->get()->count() > 0){
            return true;
        }
        //TODO: View ergänzen für Fehlermeldungen
        echo "Keine Daten über diese Welt '$server$world' vorhanden.";
        exit;
    }

    public static function getWorld($server, $world){
        return World::where('name', $server.$world)->first();
    }

    /*
     * Sucht alle Welten mit dem entsprechendem ISO.
     * */
    public static function worldsByServer($server){
        return World::where('name', 'LIKE', $server.'%')->orderBy('name')->get();
    }

    /*
     * Welteninformationen als Collection-Objekt
     * */
    public static function getWorldCollection($server, $world){
        $worldData = World::getWorld($server, $world);
        return World::buildWorldCollect($worldData, $server)[1];
    }

    /*
     * Gibt ein Collection-Objekt zurück.
     * */
    public static function worldsCollection($server){
        $worldsArray = collect();

        foreach (World::worldsByServer($server) as $worldData){
            $worldCollect = World::buildWorldCollect($worldData, $server);
            
            if (! $worldsArray->has($worldCollect[0])) {
                $worldsArray[$worldCollect[0]] = collect();
            }
            $worldsArray[$worldCollect[0]]->push($worldCollect[1]);
        }
        return $worldsArray;
    }
     
    private static function buildWorldCollect($worldRaw, $server) {
        $typeArray = World::determineType($worldRaw->name, $server);
        $world = collect();
        $world->put('type', $typeArray[1]);
        $world->put('name', $worldRaw->name);
        $world->put('server', $server);
        $world->put('worldID', BasicFunctions::getWorldID($worldRaw->name, $server));
        $world->put('world', BasicFunctions::getWorldNum($worldRaw->name));
        $world->put('display_name', $world->get('type').' '.$world->get('world'));
        $world->put('ally_count', $worldRaw->ally_count);
        $world->put('player_count', $worldRaw->player_count);
        $world->put('village_count', $worldRaw->village_count);
        
        return [$typeArray[0], $world];
    }
     
    private static function determineType($worldName, $server) {
        /*
         * Setzt den Welten Type:
         * dep => Casual
         * des => Speed
         * dec => Classic
         * de => Welt
         */
        if(strpos($worldName, $server.'p') !== false){
            return ["casual", __('main.Casual')];
        }elseif(strpos($worldName, $server.'s') !== false){
            return ["speed", __('main.Speed')];
        }elseif(strpos($worldName, $server.'c') !== false){
            return ["classic", __('main.Classic')];
        }else{
            return ["world", __('main.Welt')];
        }
    }
}
