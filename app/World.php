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
        // FIXME: 90% of code useless in this function without for loop
        // FIXME: worldsCollection does almost exactly the same can't this be merged?
        $worldData = World::getWorld($server, $world);

        $casual = collect();
        $speed = collect();
        $classic = collect();
        $other = collect();
        /*
         * Setzt den Welten Type:
         * dep => Casual
         * des => Speed
         * dec => Classic
         * de => Welt
         */
        if(strpos($worldData->name, $server.'p') !== false){
            $casual->put('type', __('main.Casual'));
            $casual->put('name', $worldData->name);
            $casual->put('server', BasicFunctions::getServer($worldData->name));
            $casual->put('world', BasicFunctions::getWorldID($worldData->name));
            $casual->put('ally_count', $worldData->ally_count);
            $casual->put('player_count', $worldData->player_count);
            $casual->put('village_count', $worldData->village_count);

            return $casual;
        }elseif(strpos($worldData->name, $server.'s') !== false){
            $speed->put('type', __('main.Speed'));
            $speed->put('name', $worldData->name);
            $speed->put('world', BasicFunctions::getWorldID($worldData->name));
            $speed->put('ally_count', $worldData->ally_count);
            $speed->put('player_count', $worldData->player_count);
            $speed->put('village_count', $worldData->village_count);

            return $speed;
        }elseif(strpos($worldData->name, $server.'c') !== false){
            $classic->put('type', __('main.Classic'));
            $classic->put('name', $worldData->name);
            $classic->put('server', BasicFunctions::getServer($worldData->name));
            $classic->put('world', BasicFunctions::getWorldID($worldData->name));
            $classic->put('ally_count', $worldData->ally_count);
            $classic->put('player_count', $worldData->player_count);
            $classic->put('village_count', $worldData->village_count);

            return $classic;
        }else{
            $other->put('type', trans_choice('main.Welten', 2));
            $other->put('name', $worldData->name);
            $other->put('server', BasicFunctions::getServer($worldData->name));
            $other->put('world', BasicFunctions::getWorldID($worldData->name));
            $other->put('ally_count', $worldData->ally_count);
            $other->put('player_count', $worldData->player_count);
            $other->put('village_count', $worldData->village_count);

            return $other;
        }
    }

    /*
     * Gibt ein Collection-Objekt zurück.
     * */
    public static function worldsCollection($server){
        $casuals = collect();
        $speeds = collect();
        $classics = collect();
        $worlds = collect();
        $worldsArray = collect();

        foreach (World::worldsByServer($server) as $worldData){
            // FIXME: big amount of redundant code
            $casual = collect();
            $speed = collect();
            $classic = collect();
            $world = collect();
            /*
             * Setzt den Welten Type:
             * dep => Casual
             * des => Speed
             * dec => Classic
             * de => Welt
             */
            if(strpos($worldData->name, $server.'p') !== false){
                $casual->put('type', __('Casual'));
                $casual->put('name', $worldData->name);
                $casual->put('world', BasicFunctions::getWorldID($worldData->name));
                $casual->put('ally_count', $worldData->ally_count);
                $casual->put('player_count', $worldData->player_count);
                $casual->put('village_count', $worldData->village_count);

                $casuals->push($casual);
            }elseif(strpos($worldData->name, $server.'s') !== false){
                $speed->put('type', __('Speed'));
                $speed->put('name', $worldData->name);
                $speed->put('world', BasicFunctions::getWorldID($worldData->name));
                $speed->put('ally_count', $worldData->ally_count);
                $speed->put('player_count', $worldData->player_count);
                $speed->put('village_count', $worldData->village_count);

                $speeds->push($speed);
            }elseif(strpos($worldData->name, $server.'c') !== false){
                $classic->put('type', __('Classic'));
                $classic->put('name', $worldData->name);
                $classic->put('world', BasicFunctions::getWorldID($worldData->name));
                $classic->put('ally_count', $worldData->ally_count);
                $classic->put('player_count', $worldData->player_count);
                $classic->put('village_count', $worldData->village_count);

                $classics->push($classic);
            }else{
                $world->put('type', __('Normale Welten'));
                $world->put('name', $worldData->name);
                $world->put('world', BasicFunctions::getWorldID($worldData->name));
                $world->put('ally_count', $worldData->ally_count);
                $world->put('player_count', $worldData->player_count);
                $world->put('village_count', $worldData->village_count);

                $worlds->push($world);
            }

        }

        ($worlds->count() > 0)? $worldsArray->put('world', $worlds) : null;
        ($casuals->count() > 0)? $worldsArray->put('casual', $casuals) : null;
        ($speeds->count() > 0)? $worldsArray->put('speed', $speeds) :  null;
        ($classics->count() > 0)? $worldsArray->put('classic', $classics) : null;
        return $worldsArray;
    }

}
