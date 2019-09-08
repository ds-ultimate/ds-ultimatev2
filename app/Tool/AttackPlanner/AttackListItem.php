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

}
