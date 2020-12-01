<?php

namespace App\Tool\AccMgrDB;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountManagerTemplateGroup extends Model
{
    use SoftDeletes;
    
    protected $table = "accMgrDB_TemplateGroup";

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
        'show_key',
        'user_id',
        'name',
    ];

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }

    public function follows(){
        return $this->morphToMany('App\User', 'followable', 'follows');
    }

    public function contents(){
        return $this->hasMany('App\Tool\AccountManagerTemplates\AccountManagerTemplate', 'group_id');
    }
}
