<?php
/**
 * Created by IntelliJ IDEA.
 * User: crams
 * Date: 23.03.2019
 * Time: 13:08
 */

namespace App\Util;


use App;
use App\Village;
use App\World;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BasicFunctions
{
    public static function numberConv($num, $round_to = 0){
        return number_format($num, $round_to, ',', '.');
    }

    public static function getWorldID($world) {
        return (int)preg_replace("/[^0-9]+/", '', $world);
    }

    public static function getServer($world) {
        return preg_replace('/[0-9]+/', '', $world);
    }

    public static function linkWorld(Collection $world, $text, $class = null, $id = null){
        return '<a class="'.$class.'" id="'.$id.'" href="'.route('world',[\App\Util\BasicFunctions::getServer($world->get('name')), $world->get('world')]).'">'.$text.'</a>';
    }

    public static function linkWorldAlly(Collection $world, $text, $class = null, $id = null){
        return '<a class="'.$class.'" id="'.$id.'" href="'.route('worldAlly',[\App\Util\BasicFunctions::getServer($world->get('name')), $world->get('world'), 1]).'">'.$text.'</a>';
    }

    public static function linkWorldPlayer(Collection $world, $text, $class = null, $id = null){
        return '<a class="'.$class.'" id="'.$id.'" href="'.route('worldPlayer',[\App\Util\BasicFunctions::getServer($world->get('name')), $world->get('world')]).'">'.$text.'</a>';
    }

    public static function linkPlayer(Collection $world, $playerID, $text, $class = null, $id = null){
        return '<a class="'.$class.'" id="'.$id.'" href="'.route('player',[\App\Util\BasicFunctions::getServer($world->get('name')), $world->get('world'), $playerID]).'">'.$text.'</a>';
    }

    public static function linkAlly(Collection $world, $allyID, $text, $class = null, $id = null){
        return '<a class="'.$class.'" id="'.$id.'" href="'.route('ally',[\App\Util\BasicFunctions::getServer($world->get('name')), $world->get('world'), $allyID]).'">'.$text.'</a>';
    }

    public static function existTable($dbName, $table){
        try{
            $result = DB::statement("SELECT 1 FROM `$dbName`.`$table` LIMIT 1");
        } catch (\Exception $e){
            return false;
        }
        return $result !== false;
    }

    public static function bonusIDtoHTML($bonus_id) {
        switch($bonus_id) {
            case 0:
                return "-";
            case 1:
                return "+100% Holz";
            case 2:
                return "+100% Lehm";
            case 3:
                return "+100% Eisen";
            case 4:
                return "+10% Bev&ouml;lkerung";
            case 5:
                return "+33% schnellere Kaserne";
            case 6:
                return "+33% schnellerer Stall";
            case 7:
                return "+50% schnellere Werkstatt";
            case 8:
                return "+30% auf alle Rohstoffe";
            case 9:
                return "+50% H&auml;ndler &amp; Speicher";
        }
        return null;
    }

    // FIXME: check accouracy since we are now using Laravel
    public static function getContinentString(Village $village) {
        return "K" . intval($village->x % 10) . intval($village->y % 10);
    }

    // FIXME: check accouracy since we are now using Laravel
    function getVillageSkinImage($village, $skin) {
        $skins = array("dark", "default", "old", "symbol", "winter");
        $index = array_search($skin, $skins);
        if($index === false){
            return null;
        }

        $left = "";
        if($village['player_id'] == 0) {
            $left = "_left";
        }

        if($village['points'] < 300) {
            $lv = 1;
        } else if($village['points'] < 1000) {
            $lv = 2;
        } else if($village['points'] < 3000) {
            $lv = 3;
        } else if($village['points'] < 9000) {
            $lv = 4;
        } else if($village['points'] < 11000) {
            $lv = 5;
        } else {
            $lv = 6;
        }

        $bonus = "v";
        if($village['bonus_id'] != 0) {
            $bonus = "b";
        }

        return "img/skins/{$skins[$index]}/$bonus$lv$left.png";
    }

    public static function hash($input ,$type){
        switch($type) {
            case 'p': //Player
                return $input % env('HASH_PLAYER');
            case 'a': //Ally
                return $input % env('HASH_ALLY');
            case 'v': //Village
                return $input % env('HASH_VILLAGE');
        }
        return false;
    }

    public static function local(){
        App::setLocale(\Session::get('locale', 'de'));
    }

    // FIXME: really addslashes?  would think of nl2br + htmlentities
    public static function outputName($name){
        return addslashes(urldecode($name));
    }

    public static function createLog($type, $msg){
        // FIXME: would like to get E-Mails from log messages
        $log = new App\Log();
        $log->setTable(env('DB_DATABASE_MAIN').'.log');
        $log->type = $type;
        $log->msg = $msg;
        $log->save();
    }

    public static function getWorld(){
        $world = new World();
        $world->setTable(env('DB_DATABASE_MAIN').'.worlds');
        // FIXME: für den Testserver
        return $world->where('name', '=', 'de164')
                ->orWhere('name', '=', 'de165')
                ->orWhere('name', '=', 'de166')
                ->orWhere('name', '=', 'de167')
                ->orWhere('name', '=', 'de163')
                ->orWhere('name', '=', 'de162')
                ->orWhere('name', '=', 'de161')
                ->orWhere('name', '=', 'de160')
                ->orWhere('name', '=', 'de159')
                ->orWhere('name', '=', 'de158')
                ->get();
        // FIXME: nur für den live Server
        return $world->get();
    }
    
    public static function getDatabaseName($server, $world) {
        // FIXME: Default value really necessary since DB_DATABASE_MAIN don't has one in most cases
        $replaceArray = array(
            '{server}' => $server,
            '{world}' => $world
        );
        return str_replace(array_keys($replaceArray),
                array_values($replaceArray),
                env('DB_DATABASE_WORLD', 'c1welt_{server}{world}'));
    }
}
