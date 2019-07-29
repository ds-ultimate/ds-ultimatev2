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
    public static function linkWorld(World $world, $text, $class = null, $id = null){
        return '<a class="'.$class.'" id="'.$id.'" href="'.route('world',[$world->server->code, $world->name]).'">'.$text.'</a>';
    }

    /**
     * @param World $world
     * @param string $text
     * @param string|null $class
     * @param string|null $id
     * @return string
     */
    public static function linkWorldAlly(World $world, $text, $class = null, $id = null){
        return '<a class="'.$class.'" id="'.$id.'" href="'.route('worldAlly',[$world->server->code, $world->name]).'">'.$text.'</a>';
    }

    /**
     * @param World $world
     * @param string $text
     * @param string|null $class
     * @param string|null $id
     * @return string
     */
    public static function linkWorldPlayer(World $world, $text, $class = null, $id = null){
        return '<a class="'.$class.'" id="'.$id.'" href="'.route('worldPlayer',[$world->server->code, $world->name]).'">'.$text.'</a>';
    }

    /**
     * @param World $world
     * @param int $playerID
     * @param string $text
     * @param string|null $class
     * @param string|null $id
     * @return string
     */
    public static function linkPlayer(World $world, $playerID, $text, $class = null, $id = null){
        return '<a class="'.$class.'" id="'.$id.'" href="'.route('player',[$world->server->code, $world->name, $playerID]).'">'.$text.'</a>';
    }

    /**
     * @param World $world
     * @param int $allyID
     * @param string $text
     * @param string|null $class
     * @param string|null $id
     * @return string
     */
    public static function linkAlly(World $world, $allyID, $text, $class = null, $id = null){
        return '<a class="'.$class.'" id="'.$id.'" href="'.route('ally',[$world->server->code, $world->name, $allyID]).'">'.$text.'</a>';
    }

    /**
     * @param World $world
     * @param int $villageID
     * @param string $text
     * @param string|null $class
     * @param string|null $id
     * @return string
     */
    public static function linkVillage(World $world, $villageID, $text, $class = null, $id = null){
        return '<a class="'.$class.'" id="'.$id.'" href="'.route('village',[$world->server->code, $world->name, $villageID]).'">'.$text.'</a>';
    }

    /**
     * @param World $world
     * @param int $allyID
     * @param \Illuminate\Support\Collection $conquer
     * @param string|null $class
     * @return string
     */
    public static function linkWinLoose (World $world, $itemID, \Illuminate\Support\Collection $conquer, $route, $class = null){
        return '<a class="'.$class.'" href="'.route($route,[$world->server->code, $world->name, 'all', $itemID]).'">'.
                    BasicFunctions::numberConv($conquer->get('total')).
                '</a>' .
                (($conquer->has('new') && $conquer->has('old'))? //assume that there will be always gain an loose
                    (' ( ' . '<a class="'.$class.'" href="'.route($route,[$world->server->code, $world->name, 'new', $itemID]).'">'.
                        '<i class="text-success">'.
                            BasicFunctions::numberConv($conquer->get('new')).
                        '</i>'.
                    '</a> - '.
                    '<a class="'.$class.'" href="'.route($route,[$world->server->code, $world->name, 'old', $itemID]).'">'.
                        '<i class="text-danger">'.
                            BasicFunctions::numberConv($conquer->get('old')).
                        '</i>'.
                    '</a> )'):
                    (''));
    }

    /**
     * @param string $dbName
     * @param string $table
     * @return bool
     */
    public static function existTable($dbName, $table){
        try{
            $result = DB::statement("SELECT 1 FROM `$dbName`.`$table` LIMIT 1");
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
        $log->setTable(env('DB_DATABASE').'.log');
        $log->type = $type;
        $log->msg = $msg;
        $log->save();
    }

    /**
     * @return Collection
     */
    public static function getWorld(){
        $world = new World();
        $world->setTable(env('DB_DATABASE').'.worlds');
        return $world->where('active', '=', '1')->get();
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
                env('DB_DATABASE_WORLD'));
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

    /**
     * @return array
     */
    public static function flags(){
        return ['ad','ae','af','ag','ai','al','am','ao','aq','ar','as','at','au','aw','ax','az','ba','bb','bd','be','bf','bg','bh','bi','bj','bl','bm','bn',
            'bo','bq','br','bs','bt','bv','bw','by','bz','ca','cc','cd','cf','cg','ch','ci','ck','cl','cm','cn','co','cr','cu','cv','cw','cx','cy','cz','de',
            'dj','dk','dm','do','dz','ec','ee','eg','eh','er','es','et','fi','fj','fk','fm','fo','fr','ga','gb','gd','ge','gf','gg','gh','gi','gl','gm',
            'gn','gp','gq','gr','gs','gt','gu','gw','gy','hm','hn','hr','ht','hu','id','ie','il','im','in','io','iq','ir','is','it','je','jm','jo','jp',
            'ke','kg','ki','km','kn','kp','kr','kw','ky','kz','la','lb','lc','li','lk','lr','ls','lt','lu','lv','ly','ma','mc','md','me','mf','mg','mh',
            'mk','ml','mm','mn','mo','mq','mr','ms','mt','mu','mv','mw','mx','my','mz','na','nc','ne','nf','ng','ni','nl','no','np','nr','nu','nz','om',
            'pa','pe','pf','pg','ph','pk','pl','pm','pn','pr','ps','pt','pw','py','qa','re','ro','rs','ru','rw','sa','sb','sc','sd','se','sg','sh','si','sj',
            'sk','sl','sm','sn','so','sr','ss','st','sv','sx','sy','sz','tc','td','tf','tg','th','tj','tk','tl','tm','tn','to','tr','tt','tv','tw','tz','ua',
            'ug','um','us','uy','uz','va','vc','ve','vg','vi','vn','vu','wf','ws','ye','yt','za','zm','zw'];
    }
}
