<?php
/**
 * Created by IntelliJ IDEA.
 * User: crams
 * Date: 23.03.2019
 * Time: 13:08
 */

namespace App\Util;


use App;
use App\World;
use App\Server;
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

    public static function linkVillage(World $world, $villageID, $text, $class = null, $id = null){
        return '<a class="'.$class.'" id="'.$id.'" href="'.route('village',[$world->server->code, $world->name, $villageID]).'">'.$text.'</a>';
    }

    public static function linkPlayerAllyChanges(World $world, $playerID, $text, $class = null, $id = null){
        return '<a class="'.$class.'" id="'.$id.'" href="'.route('playerAllyChanges',[$world->server->code, $world->name, 'all', $playerID]).'">'.$text.'</a>';
    }

    public static function linkAllyAllyChanges(World $world, $allyID, $allyChanges, $class = null){
        return '<a class="'.$class.'" href="'.route('allyAllyChanges',[$world->server->code, $world->name, 'all', $allyID]).'">'.
                    BasicFunctions::numberConv($allyChanges->get('total')).
                '</a>'.
                '(<i class="text-success">'.
                    '<a class="'.$class.'" href="'.route('allyAllyChanges',[$world->server->code, $world->name, 'new', $allyID]).'">'.
                        BasicFunctions::numberConv($allyChanges->get('new')).
                    '</a>'.
                '</i>-'.
                '<i class="text-danger">'.
                    '<a class="'.$class.'" href="'.route('allyAllyChanges',[$world->server->code, $world->name, 'old', $allyID]).'">'.
                        BasicFunctions::numberConv($allyChanges->get('old')).
                    '</a>'.
                '</i>)';
    }

    public static function linkPlayerConquer (World $world, $playerID, $conquer, $class = null){
        return '<a class="'.$class.'" href="'.route('playerConquer',[$world->server->code, $world->name, 'all', $playerID]).'">'.
                    BasicFunctions::numberConv($conquer->get('total')).
                '</a>'.
                '(<i class="text-success">'.
                    '<a class="'.$class.'" href="'.route('playerConquer',[$world->server->code, $world->name, 'new', $playerID]).'">'.
                        BasicFunctions::numberConv($conquer->get('new')).
                    '</a>'.
                '</i>-'.
                '<i class="text-danger">'.
                    '<a class="'.$class.'" href="'.route('playerConquer',[$world->server->code, $world->name, 'old', $playerID]).'">'.
                        BasicFunctions::numberConv($conquer->get('old')).
                    '</a>'.
                '</i>)';
    }

    public static function linkAllyConquer (World $world, $allyID, $conquer, $class = null){
        return '<a class="'.$class.'" href="'.route('allyConquer',[$world->server->code, $world->name, 'all', $allyID]).'">'.
                    BasicFunctions::numberConv($conquer->get('total')).
                '</a>'.
                '(<i class="text-success">'.
                    '<a class="'.$class.'" href="'.route('allyConquer',[$world->server->code, $world->name, 'new', $allyID]).'">'.
                        BasicFunctions::numberConv($conquer->get('new')).
                    '</a>'.
                '</i>-'.
                '<i class="text-danger">'.
                    '<a class="'.$class.'" href="'.route('allyConquer',[$world->server->code, $world->name, 'old', $allyID]).'">'.
                        BasicFunctions::numberConv($conquer->get('old')).
                    '</a>'.
                '</i>)';
    }

    public static function existTable($dbName, $table){
        try{
            $result = DB::statement("SELECT 1 FROM `$dbName`.`$table` LIMIT 1");
        } catch (\Exception $e){
            return false;
        }
        return $result !== false;
    }

    public static function existDatabase($dbName){
        try{
            $result = DB::select("SHOW DATABASES LIKE '".BasicFunctions::likeSaveEscape($dbName)."'");
        } catch (\Exception $e){
            return false;
        }
        return $result !== false && count($result) > 0;
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
        //return $world->get();
        return $world->where('name', '=', '164')
                ->orWhere('name', '=', '165')
                ->orWhere('name', '=', '166')
                ->orWhere('name', '=', '167')
                ->orWhere('name', '=', '163')
                ->orWhere('name', '=', '162')
                ->orWhere('name', '=', '161')
                ->orWhere('name', '=', '160')
                ->orWhere('name', '=', '169')
                ->orWhere('name', '=', '168')
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
