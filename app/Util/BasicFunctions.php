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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BasicFunctions
{
    /**
     * @param int $num
     * @param int $round_to
     * @return string
     */
    public static function numberConv($num, $round_to = 0){
        return number_format($num, $round_to, ',', '.');
    }
    /**
     * @param $world
     * @return int
     */
    public static function getWorldNum($world) {
        return (int)preg_replace("/[^0-9]+/", '', $world);
    }
    /**
     * @param $world
     * @return string
     */
    public static function getServer($world) {
        foreach(Server::getServer() as $server) {
            if(BasicFunctions::startsWith($world, $server->code)) {
                return $server->code;
            }
        }
        return '';
    }
    /**
     * @param World $world
     * @param string $text
     * @param string|null $class
     * @param string|null $id
     * @return string
     */
    public static function linkWorld(World $world, $text, $class = null, $id = null, $blank=false){
        return '<a class="'.$class.'" id="'.$id.'" '.($blank?'target="_blank "':'').'href="'.route('world',[$world->server->code, $world->name]).'">'.$text.'</a>';
    }
    /**
     * @param World $world
     * @param string $text
     * @param string|null $class
     * @param string|null $id
     * @return string
     */
    public static function linkWorldAlly(World $world, $text, $class = null, $id = null, $blank=false){
        return '<a class="'.$class.'" id="'.$id.'" '.($blank?'target="_blank "':'').'href="'.route('worldAlly',[$world->server->code, $world->name]).'">'.$text.'</a>';
    }
    /**
     * @param World $world
     * @param string $text
     * @param string|null $class
     * @param string|null $id
     * @return string
     */
    public static function linkWorldPlayer(World $world, $text, $class = null, $id = null, $blank=false){
        return '<a class="'.$class.'" id="'.$id.'" '.($blank?'target="_blank "':'').'href="'.route('worldPlayer',[$world->server->code, $world->name]).'">'.$text.'</a>';
    }
    /**
     * @param World $world
     * @param int $playerID
     * @param string $text
     * @param string|null $class
     * @param string|null $id
     * @return string
     */
    public static function linkPlayer(World $world, $playerID, $text, $class = null, $id = null, $blank=false){
        return '<a class="'.$class.'" id="'.$id.'" '.($blank?'target="_blank "':'').'href="'.route('player',[$world->server->code, $world->name, $playerID]).'">'.$text.'</a>';
    }
    /**
     * @param World $world
     * @param int $allyID
     * @param string $text
     * @param string|null $class
     * @param string|null $id
     * @return string
     */
    public static function linkAlly(World $world, $allyID, $text, $class = null, $id = null, $blank=false){
        return '<a class="'.$class.'" id="'.$id.'" '.($blank?'target="_blank "':'').'href="'.route('ally',[$world->server->code, $world->name, $allyID]).'">'.$text.'</a>';
    }
    /**
     * @param World $world
     * @param int $villageID
     * @param string $text
     * @param string|null $class
     * @param string|null $id
     * @return string
     */
    public static function linkVillage(World $world, $villageID, $text, $class = null, $id = null, $blank=false){
        return '<a class="'.$class.'" id="'.$id.'" '.($blank?'target="_blank "':'').'href="'.route('village',[$world->server->code, $world->name, $villageID]).'">'.$text.'</a>';
    }
    /**
     * @param World $world
     * @param int $allyID
     * @param \Illuminate\Support\Collection $conquer
     * @param string|null $class
     * @return string
     */
    public static function linkWinLoose (World $world, $itemID, \Illuminate\Support\Collection $conquer, $route, $class = null, $blank=false){
        $data = '<a class="'.$class.'" '.($blank?'target="_blank "':'').'href="'.route($route,[$world->server->code, $world->name, 'all', $itemID]).'">'.
                BasicFunctions::numberConv($conquer->get('total')).
                '</a>';
        
        if($conquer->has('new') && $conquer->has('old')) {
            //assume that there will be always gain an loose
            $data .= ' ( ';
            $data .= '<a class="'.$class.'" '.($blank?'target="_blank "':'').'href="'.route($route,[$world->server->code, $world->name, 'new', $itemID]).'"><i class="text-success">'.
                    BasicFunctions::numberConv($conquer->get('new')).
                    '</i></a> - ';
            
            if($conquer->has('own')) {
                $data .= '<a class="'.$class.'" '.($blank?'target="_blank "':'').'href="'.route($route,[$world->server->code, $world->name, 'own', $itemID]).'"><i class="text-info">'.
                        BasicFunctions::numberConv($conquer->get('own')).
                        '</i></a> - ';
            }
            
            $data .= '<a class="'.$class.'" '.($blank?'target="_blank "':'').'href="'.route($route,[$world->server->code, $world->name, 'old', $itemID]).'"><i class="text-danger">'.
                    BasicFunctions::numberConv($conquer->get('old')).
                    '</i></a> )';
        }
        return $data;
    }
    /**
     * @param string $dbName
     * @param string $table
     * @return bool
     */
    public static function existTable($dbName, $table){
        try{
            $result = DB::statement("SELECT 1 FROM " . (($dbName!=null)?("`$dbName`."):("")) . "`$table` LIMIT 1");
        } catch (\Exception $e){
            return false;
        }
        return $result !== false;
    }
    /**
     * @param string $dbName
     * @return bool
     */
    public static function existDatabase($dbName){
        try{
            $result = DB::select("SHOW DATABASES LIKE '".BasicFunctions::likeSaveEscape($dbName)."'");
        } catch (\Exception $e){
            return false;
        }
        return $result !== false && count($result) > 0;
    }
    /**
     * @param $input
     * @param $type
     * @return bool|int
     */
    public static function hash($input ,$type){
        switch($type) {
            case 'p': //Player
                return $input % config('dsUltimate.hash_player');
            case 'a': //Ally
                return $input % config('dsUltimate.hash_ally');
            case 'v': //Village
                return $input % config('dsUltimate.hash_village');
        }
        return false;
    }
    public static function local(){
        App::setLocale(\Session::get('locale', 'de'));
    }
    /**
     * This function only decodes the Data
     * the output must be escaped properly afterwards
     *
     * @param string $name
     * @return string
     */
    public static function decodeName($name) {
        return urldecode($name);
    }
    /**
     * @param string $name
     * @return string
     */
    public static function outputName($name) {
        return nl2br(htmlentities(urldecode($name)));
    }
    public static function createLog($type, $msg){
        // FIXME: would like to get E-Mails from log messages
        $log = new App\Log();
        $log->setTable('log');
        $log->type = $type;
        $log->msg = $msg;
        $log->save();
    }
    /**
     * @return Collection
     */
    public static function getWorldQuery(){
        $world = new World();
        return $world->where('active', '=', '1');
    }
    /**
     * @param string $server
     * @param $world
     * @return mixed
     */
    public static function getDatabaseName($server, $world) {
        $replaceArray = array(
            '{server}' => $server,
            '{world}' => $world
        );
        return str_replace(array_keys($replaceArray),
            array_values($replaceArray),
            config('dsUltimate.db_database_world'));
    }
    /**
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function startsWith($haystack, $needle) {
        return $needle === "" || strpos($haystack, $needle) === 0;
    }
    /**
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function endsWith($haystack, $needle) {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }
    public static function ignoreErrs() {
        set_error_handler(function($severity, $message, $file, $line) {
            echo "We got an errror[$severity] at $file:$line:\n$message\nBut we are ignoring it\n";
            return;
        });
    }
    /**
     * @param $toEscape
     * @return mixed
     */
    public static function likeSaveEscape($toEscape) {
        $search = array( "\\"  , "%"  , "_"  , "["  , "]"  , "'"  , "\""  );
        $replace = array("\\\\", "\\%", "\\_", "[[]", "[]]", "\\'", "\\\"");
        return str_replace($search, $replace, $toEscape);
    }

    public static function worldStatus($active){
        switch ($active){
            case null:
                return '<span class="fas fa-ban" style="color: red"></span>';
            case 1:
                return '<span class="fas fa-check" style="color: green"></span>';
            case 0:
                return '<span class="fas fa-times" style="color: red"></span>';
        }
    }

    public static function convertTime($input){
        $input = floor($input / 1000);

        $seconds = $input % 60;
        $input = floor($input / 60);

        $minutes = $input % 60;
        $input = floor($input / 60);

        $hour = $input % 24;
        $day = floor($input / 24);

        return $day.' '.__('ui.tool.distCalc.days').' '.str_pad($hour, 2, "0", STR_PAD_LEFT).':'.str_pad($minutes, 2, "0", STR_PAD_LEFT).':'.str_pad($seconds, 2, "0", STR_PAD_LEFT);
    }
    
    public static function sign( $number ) {
        return ( $number > 0 ) ? 1 : (( $number < 0 ) ? -1 : 0 );
    }

    public static function modelHistoryCalc($newModel, $oldModel, $type, $invert = false){
        if ($newModel->$type != $oldModel->$type){
            if ($newModel->$type > $oldModel->$type){
                $result = ($invert == false)?'up':'down';
            }else{
                $result = ($invert == true)?'up':'down';
            }
        }else{
            $result = 'equals';
        }
        $icon = Icon::historyIconsTextColor($result);
        return "<span class=\"text-".$icon['color']."\" data-toggle=\"popover\" data-trigger=\"hover\" data-placement=\"top\" data-content=\"".__('ui.old.'.$type).": <b>".self::thousandsCurrencyFormat($oldModel->$type)."</b>\"><i class=\"fas fa-".$icon['icon']."\"></i> ".self::thousandsCurrencyFormat($newModel->$type)."</span>";
    }

    public static function historyCalc($new, $old, $type, $invert = false){
        if ($new != $old){
            if ($new > $old){
                $result = ($invert == false)?'up':'down';
            }else{
                $result = ($invert == true)?'up':'down';
            }
        }else{
            $result = 'equals';
        }
        $icon = Icon::historyIconsTextColor($result);
        return "<span class=\"text-".$icon['color']."\" data-toggle=\"popover\" data-trigger=\"hover\" data-placement=\"top\" data-content=\"".__('ui.old.'.$type).": <b>".self::thousandsCurrencyFormat($old)."</b>\"><i class=\"fas fa-".$icon['icon']."\"></i> ".self::thousandsCurrencyFormat($new)."</span>";
    }

    public static function thousandsCurrencyFormat($num) {

        if($num>100000) {

            $x = round($num);
            $x_number_format = number_format($x);
            $x_array = explode(',', $x_number_format);
            $x_parts = array('k', 'm', 'b', 't');
            $x_count_parts = count($x_array) - 1;
            $x_display = $x;
            $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '') . ((int) $x_array[1][1] !== 0 ? $x_array[1][1] : '');
            $x_display .= $x_parts[$x_count_parts - 1];

            return $x_display;

        }

        return self::numberConv($num);
    }

}
