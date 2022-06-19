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
use Illuminate\Support\Carbon;

class AttackList extends CustomModel
{
    use SoftDeletes;

    protected $fillable = [
        'world_id',
        'user_id',
    ];

    protected $hidden = [
        'edit_key',
        'show_key',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    
    protected $cache = [
        'world',
    ];
    

    /**
     * @return World
     */
    public function world()
    {
        return $this->belongsTo('App\World');
    }

    public function items()
    {
        return $this->hasMany('App\Tool\AttackPlanner\AttackListItem' )->orderBy('send_time');
    }

    public function follows(){
        return $this->morphToMany('App\User', 'followable', 'follows');
    }

    public function nextAttack(){
        $item = $this->items->where('send_time', '>', Carbon::now())->first();
        if (!isset($item->send_time)){
            return '-';
        }
        $date = $item->send_time->locale(\App::getLocale());
        return $date->isoFormat('L LT');
    }

    public function outdatedCount(){
        return $this->items->where('send_time', '<', Carbon::now())->count();
    }

    public function attackCount(){
        return $this->items->where('send_time', '>', Carbon::now())->count();
    }
    
    public function getTitle() {
        if($this->title == null || $this->title == "") {
            return __('tool.attackPlanner.title');
        }
        return $this->title;
    }
}
