<?php
/**
 * Created by IntelliJ IDEA.
 * User: crams
 * Date: 18.08.2019
 * Time: 16:05
 */

namespace App\Tool\AttackPlanner;


use App\CustomModel;
use App\World;
use App\Util\BasicFunctions;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttackListLegacy extends CustomModel
{
    use SoftDeletes;

    protected $fillable = [
        'world_id',
        'new_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    
    protected $cache = [
        'world',
    ];
    
    
    public function __construct($arg1 = [], $arg2 = null) {
        if($arg1 instanceof World && $arg2 == null) {
            //allow calls without table name
            $arg2 = "attack_lists";
        }
        parent::__construct($arg1, $arg2);
    }

    /**
     * @return World
     */
    public function world()
    {
        return $this->belongsTo('App\World');
    }
    
    public function newList() {
        return $this->mybelongsTo('App\Tool\AttackPlanner\AttackList', '', 'new_id', BasicFunctions::getWorldDataTable($this->world, 'ally_latest'));
    }
}
