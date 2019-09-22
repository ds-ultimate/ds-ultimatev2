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
        switch ($this->slowest_unit) {
            case '0':
                return 'spear';
            case '1':
                return 'sword';
            case '2':
                return 'axe';
            case '3':
                return 'archer';
            case '4':
                return 'spy';
            case '5':
                return 'light';
            case '6':
                return 'marcher';
            case '7':
                return 'heavy';
            case '8':
                return 'ram';
            case '9':
                return 'catapult';
            case '10':
                return 'knight';
            case '11':
                return 'snob';
        }
    }

    public static function unitNameToID($input){
        switch ($input) {
            case 'spear':
                return '0';
            case 'sword':
                return '1';
            case 'axe':
                return '2';
            case 'archer':
                return '3';
            case 'spy':
                return '4';
            case 'light':
                return '5';
            case 'marcher':
                return '6';
            case 'heavy':
                return '7';
            case 'ram':
                return '8';
            case 'catapult':
                return '9';
            case 'knight':
                return '10';
            case 'snob':
                return '11';
        }
    }

    public function typeIDToName(){
        switch ($this->type) {
            case '8':
                return __('ui.tool.attackPlanner.attack');
            case '11':
                return __('ui.tool.attackPlanner.conquest');
            case '14':
                return __('ui.tool.attackPlanner.fake');
            case '45':
                return __('ui.tool.attackPlanner.wallbreaker');
            case '0':
                return __('ui.tool.attackPlanner.support');
            case '1':
                return __('ui.tool.attackPlanner.standSupport');
            case '7':
                return __('ui.tool.attackPlanner.fastSupport');
            case '46':
                return __('ui.tool.attackPlanner.fakeSupport');
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

}
