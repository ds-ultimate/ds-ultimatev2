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
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

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
     * @param int $float
     * @return string
     */
    public static function floatConvtoProcent($float){
        return round($float*100);
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
    public static function linkWinLoose (World $world, $itemID, Array $conquer,
            $route, $class = null, $blank=false, $tooltipSpace=null){
        $data = static::linkGeneric(BasicFunctions::numberConv($conquer['total']),
                route($route,[$world->server->code, $world->name, 'all', $itemID]), $class, $blank, [$tooltipSpace, "total"]);

        if(isset($conquer['new']) && isset($conquer['old'])) {
            //assume that there will be always gain an loose
            $appCls = $class ?? "";
            $data .= ' ( ';
            $data .= static::linkGeneric(BasicFunctions::numberConv($conquer['new']),
                    route($route,[$world->server->code, $world->name, 'new', $itemID]), $appCls." text-success", $blank, [$tooltipSpace, "win"]);
            $data .= ' - ';

            if(isset($conquer['own'])) {
                $data .= static::linkGeneric(BasicFunctions::numberConv($conquer['own']),
                        route($route,[$world->server->code, $world->name, 'own', $itemID]), $appCls." text-info", $blank, [$tooltipSpace, "self"]);
                $data .= ' - ';
            }

            $data .= static::linkGeneric(BasicFunctions::numberConv($conquer['old']),
                    route($route,[$world->server->code, $world->name, 'old', $itemID]), $appCls." text-danger", $blank, [$tooltipSpace, "loose"]);
            $data .= ' )';
        }
        return $data;
    }

    private static function linkGeneric($text, $href, $class=null, $blank=false, $toolT=null) {
        $t = "";
        if($toolT != null && $toolT[0] != null) {
            $t = "title='" . __("{$toolT[0]}.{$toolT[1]}") . "' ";
        }
        $trg_blank = $blank ? 'target="_blank "' : '';
        $cls = ($class !== null)?(" class='$class'"):"";
        return "<a {$cls}{$trg_blank}{$t}href='$href'>$text</a>";
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

    public static function local(){
        $locale = \Session::get('locale');
        if($locale === null) {
            $replacements = ["cs" => "cz"];

            $locale = substr(\Request::server('HTTP_ACCEPT_LANGUAGE'), 0, 2);
            if(isset($replacements[$locale])) {
                $locale = $replacements[$locale];
            }
            if (! in_array($locale, Navigation::getAvailableLocales())) {
                $locale = "de";
            }
        }
        App::setLocale($locale);
    }

    public static function changelogUpdate(){
        if (\Auth::check()) {
            $user = \Auth::user();
            $user->profile->last_seen_changelog = Carbon::now();
            $user->profile->save();
        }
        \Session::put('last_seen_changelog', Carbon::now());
    }

    /**
     * This function only decodes the Data
     * the output must be escaped properly afterwards
     * {{ BasicFunctions::decodeName($test) }}
     *
     * @param string $name
     * @return string
     */
    public static function decodeName($name) {
        return urldecode($name);
    }

    /**
     *{!! BasicFunctions::outputName($test) !!}
     *
     * @param string $name
     * @return string
     */
    public static function outputName($name) {
        return self::escape(urldecode($name));
    }

    public static function escape($text) {
        return nl2br(htmlentities($text));
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
     * Returns the raw database name where that world will be stored in
     * Intended only for creating that database / makting sure it exists
     * 
     * The internal behavior is expected to change soon (multi server databases)
     * !do not rely on knowing what it does internally!
     * @param World $model
     * @return type
     */
    public static function getWorldDataDatabase(World $model) {
        $replaceArray = array(
            '{server}' => $model->server->code,
            '{world}' => $model->name,
        );
        return str_replace(array_keys($replaceArray),
            array_values($replaceArray),
            config('dsUltimate.db_database_world'));
    }

    /**
     * @param World $model
     * @param $tableName
     * @return string
     */
    public static function getWorldDataTable(World $model, $tableName) {
        return static::getWorldDataDatabase($model) . "." . $tableName;
    }
    
    public static function hasWorldDataTable(World $model, $tableName) {
        return static::existTable(static::getWorldDataDatabase($model), $tableName);
    }
    
    public static function hasUserWorldDataTable(World $model, $tableName) {
        return static::existTable(config('dsUltimate.db_database_wData'), "{$model->server->code}{$model->name}_{$tableName}");
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
        $input = ceil($input / 1000);

        $seconds = $input % 60;
        $input = floor($input / 60);

        $minutes = $input % 60;
        $input = floor($input / 60);

        $hour = $input % 24;
        $day = floor($input / 24);

        return $day.' '.__('tool.distCalc.days').' '.str_pad($hour, 2, "0", STR_PAD_LEFT).':'.str_pad($minutes, 2, "0", STR_PAD_LEFT).':'.str_pad($seconds, 2, "0", STR_PAD_LEFT);
    }

    public static function sign( $number ) {
        return ( $number > 0 ) ? 1 : (( $number < 0 ) ? -1 : 0 );
    }

    public static function flattenArray($array) {
        if (!is_array($array)) {
            // nothing to do if it's not an array
            return array($array);
        }

        $result = array();
        foreach ($array as $value) {
            // explode the sub-array, and add the parts
            $result = array_merge($result, static::flattenArray($value));
        }

        return $result;
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
        return self::historyReturn($type, $oldModel->$type, $newModel->$type, $icon, true);
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
        return self::historyReturn($type, $old, $new, $icon, true);
    }

    public static function modelHistoryCalcPopupless($newModel, $oldModel, $type, $invert = false){
        if($oldModel == null) {
            return self::thousandsCurrencyFormat($newModel->$type);
        }

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
        return self::historyReturn($type, $oldModel->$type, $newModel->$type, $icon, false);
    }
    
    private static function historyReturn($type, $old, $new, $icon, $hasPopup) {
        $cls = "";
        if($icon['color'] != null) {
            $cls = " class=\"text-".$icon['color']."\"";
        }
        $popup = "";
        if($hasPopup) {
            $popup = "data-toggle=\"popover\" data-trigger=\"hover\" data-placement=\"top\" data-content=\"".__('ui.old.'.$type).": <b>".self::thousandsCurrencyFormat($old)."</b>\"";
        }
        $ret = "<span$cls$popup>";
        if($icon['icon'] != null) {
            $ret .= "<i class=\"fas fa-".$icon['icon']."\"></i> ";
        }
        return $ret.self::thousandsCurrencyFormat($new)."</span>";
    }

    /**
     * Formats a given value with according suffix
     * rounds to 3 import digits
     *
     * @param type $num the given number
     * @return string
     */
    public static function thousandsCurrencyFormat($num) {
        $exp = 0;
        while($num > 1000) {
            $exp++;
            $num/= 1000;
        }

        $suffixes = array('', 'K', 'M', 'G', 'T');
        $suffix = $suffixes[$exp];

        $num_digits = floor(log10($num + 0.01));
        $num = round($num, 2 - $num_digits);

        $converted = $num . " " . $suffix;

        return $converted;
    }

    public static function formEntryEdit($generateFrom, $type, $name, $id, $value, $readonly, $required, $optional = array()) {
        $index = str_replace("[]", "", $id);
        $val = $generateFrom->$index ?? $value;
        if($type == "time") {
            $val = [
                'd' => Carbon::createFromTimestamp($val)->format("Y-m-d"),
                't' => Carbon::createFromTimestamp($val)->format("H:i"),
            ];
        }

        return array_merge([
            'type' => $type,
            'name' => $name,
            'id' => $id,
            'value' => $val,
            'readonly' => $readonly,
            'required' => $required,
        ], $optional);
    }

    public static function formEntryShow($name, $value, $escape = true, $optional = array()) {
        return array_merge([
            'name' => $name,
            'value' => $value,
            'escape' => $escape,
        ], $optional);
    }

    public static function indexEntry($title, $data, $style = "", $class = "", $optional=array()) {
        return array_merge([
            'title' => $title,
            'data' => $data,
            'style' => $style,
            'class' => $class,
            'dataAdditional' => "",
        ], $optional);
    }

    public static function asset($path) {
        return asset($path) . "?" . filemtime(public_path($path));
    }
}
