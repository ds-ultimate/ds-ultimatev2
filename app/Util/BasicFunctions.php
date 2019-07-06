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
use App\Server;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BasicFunctions
{
    public static function numberConv($num, $round_to = 0){
        return number_format($num, $round_to, ',', '.');
    }

    public static function getWorldNum($world) {
        return (int)preg_replace("/[^0-9]+/", '', $world);
    }

    public static function getWorldID($world, $server) {
        return str_replace($server, '', $world);
    }

    public static function getServer($world) {
        foreach(Server::getServer() as $server) {
            if(BasicFunctions::startsWith($world, $server->code)) {
                return $server->code;
            }
        }
        return '';
    }

    public static function linkWorld(World $world, $text, $class = null, $id = null){
        return '<a class="'.$class.'" id="'.$id.'" href="'.route('world',[$world->server->code, $world->name]).'">'.$text.'</a>';
    }

    public static function linkWorldAlly(World $world, $text, $class = null, $id = null){
        return '<a class="'.$class.'" id="'.$id.'" href="'.route('worldAlly',[$world->server->code, $world->name]).'">'.$text.'</a>';
    }

    public static function linkWorldPlayer(World $world, $text, $class = null, $id = null){
        return '<a class="'.$class.'" id="'.$id.'" href="'.route('worldPlayer',[$world->server->code, $world->name]).'">'.$text.'</a>';
    }

    public static function linkPlayer(World $world, $playerID, $text, $class = null, $id = null){
        return '<a class="'.$class.'" id="'.$id.'" href="'.route('player',[$world->server->code, $world->name, $playerID]).'">'.$text.'</a>';
    }

    public static function linkAlly(World $world, $allyID, $text, $class = null, $id = null){
        return '<a class="'.$class.'" id="'.$id.'" href="'.route('ally',[$world->server->code, $world->name, $allyID]).'">'.$text.'</a>';
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

    public static function getContinentString(Village $village) {
        return "K" . intval($village->x / 100) . intval($village->y / 100);
    }

    function getVillageSkinImage($village, $skin) {
        $skins = array("dark", "default", "old", "symbol", "winter");
        $index = array_search($skin, $skins);
        if($index === false){
            return null;
        }

        $left = "";
        if($village->owner == 0) {
            $left = "_left";
        }

        if($village->points < 300) {
            $lv = 1;
        } else if($village->points < 1000) {
            $lv = 2;
        } else if($village->points < 3000) {
            $lv = 3;
        } else if($village->points < 9000) {
            $lv = 4;
        } else if($village->points < 11000) {
            $lv = 5;
        } else {
            $lv = 6;
        }

        $bonus = "v";
        if($village->bonus_id != 0) {
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

    /*
     * This function only decodes the Data
     * the output must be escaped properly afterwards
     */
    public static function decodeName($name) {
        return urldecode($name);
    }

    public static function outputName($name) {
        return nl2br(htmlentities(urldecode($name)));
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
        return $world->where('name', '=', '164')
                ->orWhere('name', '=', '165')
                ->orWhere('name', '=', '166')
                ->orWhere('name', '=', '167')
                ->orWhere('name', '=', '163')
                ->orWhere('name', '=', '162')
                ->orWhere('name', '=', '161')
                ->orWhere('name', '=', '160')
                ->orWhere('name', '=', '159')
                ->orWhere('name', '=', '158')
                ->get();
        // FIXME: nur für den live Server
        return $world->get();
    }
    
    public static function getDatabaseName($server, $world) {
        $replaceArray = array(
            '{server}' => $server,
            '{world}' => $world
        );
        return str_replace(array_keys($replaceArray),
                array_values($replaceArray),
                env('DB_DATABASE_WORLD'));
    }
    
    public static function startsWith($haystack, $needle) {
        return $needle === "" || strpos($haystack, $needle) === 0;
    }
    
    public static function endsWith($haystack, $needle) {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }
    
    public static function ignoreErrs() {
        set_error_handler(function($severity, $message, $file, $line) {
            echo "We got an errror[$severity] at $file:$line:\n$message\nBut we are ignoring it\n";
            return;
        });
    }
    
    public static function likeSaveEscape($toEscape) {
        $search = array( "\\"  , "%"  , "_"  , "["  , "]"  , "'"  , "\""  );
        $replace = array("\\\\", "\\%", "\\_", "[[]", "[]]", "\\'", "\\\"");
        return str_replace($search, $replace, $toEscape);
    }
}
