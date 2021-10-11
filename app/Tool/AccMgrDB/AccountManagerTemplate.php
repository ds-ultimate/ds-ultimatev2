<?php

namespace App\Tool\AccMgrDB;

use App\Util\BasicFunctions;
use App\Tool\AccMgrDB\AccountManagerRating;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AccountManagerTemplate extends Model
{
    use SoftDeletes;
    
    protected $table = "accMgrDB_Template";
    
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $hidden = [
        'show_key',
    ];

    protected $fillable = [
        'id',
        'name',
        'buildings',
        'remove_additional',
        'ignore_remove',
        'user_id',
        'show_key',
        'group_id',
        'contains_watchtower',
        'contains_church',
        'contains_statue',
        'rating',
        'totalVotes',
        'public',
    ];

    public static $BUILDING_NAMES = [
        0 => "main",
        1 => "barracks",
        2 => "stable",
        3 => "garage",
        4 => "church",
        5 => "church_f",
        6 => "watchtower",
        7 => "snob",
        8 => "smith",
        9 => "place",
        10 => "statue",
        11 => "market",
        12 => "wood",
        13 => "stone",
        14 => "iron",
        15 => "farm",
        16 => "storage",
        17 => "hide",
        18 => "wall",
    ];
    
    public static $NAME_DELIMINTER = "\364\200\200\200";

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function follows() {
        return $this->morphToMany('App\User', 'followable', 'follows');
    }
    
    public function buildingArray() {
        $retval = [];
        foreach(explode(";", $this->buildings) as $entry) {
            if($entry !== null && strlen($entry) > 0) {
                $inner = explode(":", $entry);
                if(count($inner) == 2) {
                    $retval[] = [
                        intval($inner[0]),
                        intval($inner[1]),
                    ];
                }
            }
        }
        return $retval;
    }

    public function setBuildings($value) {
        $generated = "";
        $first = true;
        $contChurch = false;
        $contWatch = false;
        $contStatue = false;
        
        foreach($value as $val) {
            if(! $first) {
                $generated .= ";";
            }
            $generated  .= $val[0] . ":" . $val[1];
            $first = false;
            
            if(static::$BUILDING_NAMES[$val[0]] == "church" ||
                    static::$BUILDING_NAMES[$val[0]] == "church_f") {
                $contChurch = true;
            }
            if(static::$BUILDING_NAMES[$val[0]] == "watchtower") {
                $contWatch = true;
            }
            if(static::$BUILDING_NAMES[$val[0]] == "statue") {
                $contStatue = true;
            }
        }
        $this->buildings = $generated;
        $this->contains_church = $contChurch;
        $this->contains_watchtower = $contWatch;
        $this->contains_statue = $contStatue;
    }

    public function buildingsIgnored() {
        if($this->ignore_remove == null || $this->ignore_remove == "") {
            return [];
        }
        
        $retval = [];
        foreach(explode(";", $this->ignore_remove) as $entry) {
            if($entry !== null && strlen($entry) > 0) {
                $retval[] = intval($entry);
            }
        }
        return $retval;
    }

    public function setBuildingsIgnored($value) {
        $generated = "";
        $first = true;
        foreach($value as $val) {
            if(! $first) {
                $generated .= ";";
            }
            $generated  .= $val;
            $first = false;
        }
        $this->ignore_remove = $generated;
    }

    public function exportDS() {
        $buildingInformation = call_user_func_array("pack", array_merge(["C*"], BasicFunctions::flattenArray($this->buildingArray())));
        $len = strlen($buildingInformation);
        
        $generated = chr($len % 256) . chr(intdiv($len, 256));
        $generated .= $buildingInformation;
        
        if($this->remove_additional) {
            $generated .= "\001";
            $generated .= call_user_func_array("pack", array_merge(["C*"], $this->buildingsIgnored()));
        } else {
            $generated .= "\000";
        }
        
        $generated .= static::$NAME_DELIMINTER . $this->name . static::$NAME_DELIMINTER . chr(51);
        return base64_encode($generated);
    }
    
    public static function importDS($input) {
        abort_unless(\Auth::check(), 403);
        
        $input = str_replace(['[construction_template]', '[/construction_template]'], "", $input);
        $raw = base64_decode($input);
        $payloadLenght = ord($raw[0]) + ord($raw[1]) * 256;
        if($payloadLenght > strlen($raw) - 2) {
            return __('tool.accMgrDB.err.paylod_len');
        }
        $payload = substr($raw, 2, $payloadLenght);
        $bytes = unpack("C*", $payload);
        $cursor = 2 + $payloadLenght;

        $buildings = [];
        $contChurch = false;
        $contWatch = false;
        $contStatue = false;
        for($i = 1; $i < count($bytes); $i+=2) {
            if(! isset(static::$BUILDING_NAMES[$bytes[$i]])) {
                return str_replace("[BUILDING]", $bytes[$i], __('tool.accMgrDB.err.unknown_building'));
            }
            if(static::$BUILDING_NAMES[$bytes[$i]] == "church" ||
                    static::$BUILDING_NAMES[$bytes[$i]] == "church_f") {
                $contChurch = true;
            }
            if(static::$BUILDING_NAMES[$bytes[$i]] == "watchtower") {
                $contWatch = true;
            }
            if(static::$BUILDING_NAMES[$bytes[$i]] == "statue") {
                $contStatue = true;
            }
            
            $buildings[] = array($bytes[$i], $bytes[$i + 1]);
        }
        
        $startName = strpos($raw, static::$NAME_DELIMINTER, $cursor) + strlen(static::$NAME_DELIMINTER);
        $endName = strpos($raw, static::$NAME_DELIMINTER, $startName + 1);
        
        $remove_additional = ord($raw[$cursor]) == 1 ? true:false;
        if($remove_additional) {
            $remLen = $startName - strlen(static::$NAME_DELIMINTER) - $cursor - 1;
            if($remLen > 0) {
                $ignore_remove = unpack("C*", substr($raw, $cursor + 1, $remLen));
            } else {
                $ignore_remove = [];
            }
        }

        if($startName === false || $endName === false) {
            return __('tool.accMgrDB.err.name_not_found');
        }
        $name = substr($raw, $startName, $endName - $startName);
        
        $model = new AccountManagerTemplate();
        $model->show_key = Str::random(40);
        $model->setBuildings($buildings);
        $model->remove_additional = $remove_additional;
        if($remove_additional) {
            $model->setBuildingsIgnored($ignore_remove);
        }
        $model->user_id = \Auth::user()->id;
        $model->rating = 0;
        $model->totalVotes = 0;
        $model->name = $name;
        $model->contains_watchtower = $contWatch;
        $model->contains_church = $contChurch;
        $model->contains_statue = $contStatue;
        $model->public = false;
        return $model;
    }
    
    public function calculateRating() {
        $ratings = (new AccountManagerRating())->where('template_id', $this->id)->get();
        
        $sum = 0;
        foreach($ratings as $rating) {
            $sum += $rating->rating;
        }
        if($ratings->count() > 0) {
            $res = $sum / $ratings->count();
        } else {
            $res = 0;
        }
        $this->rating = $res;
        $this->totalVotes = $ratings->count();
        $this->save();
    }
}
