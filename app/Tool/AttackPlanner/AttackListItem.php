<?php
/**
 * Created by IntelliJ IDEA.
 * User: crams
 * Date: 18.08.2019
 * Time: 16:10
 */

namespace App\Tool\AttackPlanner;


use App\CustomModel;
use App\Util\BasicFunctions;
use App\Village;
use App\Tool\AttackPlanner\AttackList as AttackList;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttackListItem extends CustomModel
{
    protected $fillable = [
        'attack_list_id',
        'type',
        'start_village_id',
        'target_village_id',
        'slowest_unit',
        'note',
        'send_time',
        'arrival_time',
        'spear',
        'sword',
        'axe',
        'archer',
        'spy',
        'light',
        'marcher',
        'heavy',
        'ram',
        'catapult',
        'knight',
        'snob',
    ];

    protected $dates = [
        'send_time',
        'arrival_time',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    private static $unit = [
        0 => 'spear',
        1 => 'sword',
        2 => 'axe',
        3 => 'archer',
        4 => 'spy',
        5 => 'light',
        6 => 'marcher',
        7 => 'heavy',
        8 => 'ram',
        9 => 'catapult',
        10 => 'knight',
        11 => 'snob',
    ];

    /**
     * @return AttackList
     */
    public function list(){
        return $this->belongsTo('App\Tool\AttackPlanner\AttackList', 'attack_list_id');
    }

    /**
     * @return Village
     */
    public function start_village(){
        $world = $this->list->world;
        $dbName = BasicFunctions::getDatabaseName($world->server->code, $world->name);

        return $this->mybelongsTo('App\Village', 'start_village_id', 'villageID', $dbName.'.village_latest');
    }

    /**
     * @return Village
     */
    public function target_village(){
        $world = $this->list->world;
        $dbName = BasicFunctions::getDatabaseName($world->server->code, $world->name);

        return $this->mybelongsTo('App\Village', 'target_village_id', 'villageID', $dbName.'.village_latest');
    }

    public function unitIDToName(){
        return $this->unit[$this->slowest_unit];
    }

    public static function unitNameToID($input){
        return array_search($input, self::$unit);
    }

    public function typeIDToName(){
        switch ($this->type) {
            case '8':
                return __('tool.attackPlanner.attack');
            case '11':
                return __('tool.attackPlanner.conquest');
            case '14':
                return __('tool.attackPlanner.fake');
            case '45':
                return __('tool.attackPlanner.wallbreaker');
            case '0':
                return __('tool.attackPlanner.support');
            case '1':
                return __('tool.attackPlanner.standSupport');
            case '7':
                return __('.tool.attackPlanner.fastSupport');
            case '46':
                return __('tool.attackPlanner.fakeSupport');
        }
    }

    public function unitIDToNameOutput(){
        switch ($this->slowest_unit) {
            case '0':
                return __('ui.unit.spear');
            case '1':
                return __('ui.unit.sword');
            case '2':
                return __('ui.unit.axe');
            case '3':
                return __('ui.unit.archer');
            case '4':
                return __('ui.unit.spy');
            case '5':
                return __('ui.unit.light');
            case '6':
                return __('ui.unit.marcher');
            case '7':
                return __('ui.unit.heavy');
            case '8':
                return __('ui.unit.ram');
            case '9':
                return __('ui.unit.catapult');
            case '10':
                return __('ui.unit.knight');
            case '11':
                return __('ui.unit.snob');
        }
    }

    public function attackerName(){
        if($this->start_village->owner == 0) return ucfirst(__('ui.player.barbarian'));
        if($this->start_village->playerLatest == null) return ucfirst(__('ui.player.deleted'));
        return BasicFunctions::decodeName($this->start_village->playerLatest->name);
    }

    public function defenderName(){
        if($this->target_village->owner == 0) return ucfirst(__('ui.player.barbarian'));
        if($this->target_village->playerLatest == null) return ucfirst(__('ui.player.deleted'));
        return BasicFunctions::decodeName($this->target_village->playerLatest->name);
    }

    public function calcSend(){
        $unitConfig = $this->list->world->unitConfig();
        $dist = $this->calcDistance();
        $unit = self::$unit[$this->slowest_unit];
        $runningTime = round(((float)$unitConfig->$unit->speed * 60) * $dist);
        return $this->arrival_time->subSeconds($runningTime);
    }

    public function calcArrival(){
        $unitConfig = $this->list->world->unitConfig();
        $dist = $this->calcDistance();
        $unit = $this->unit[$this->slowest_unit];
        $runningTime = round(((float)$unitConfig->$unit->speed * 60) * $dist);
        return $this->start_time->addSeconds($runningTime);
    }

    public function calcDistance(){
        return sqrt(pow($this->start_village->x - $this->target_village->x, 2) + pow($this->start_village->y - $this->target_village->y, 2));
    }

    public function setVillageID($xStart, $yStart, $xTarget, $yTarget){
        $this->start_village_id = $this->getVillageID($xStart, $yStart);
        $this->target_village_id = $this->getVillageID($xTarget, $yTarget);
        if ($this->start_village_id === null || $this->target_village_id === null){
            return false;
        }
        return true;
    }

    private function getVillageID($x, $y){
        $villageModel = new Village();
        $villageModel->setTable(BasicFunctions::getDatabaseName($this->list->world->server->code, $this->list->world->name).'.village_latest');
        $village = $villageModel->where(['x' => $x, 'y' => $y])->first();
        return isset($village->villageID)? $village->villageID : null;
    }

}
