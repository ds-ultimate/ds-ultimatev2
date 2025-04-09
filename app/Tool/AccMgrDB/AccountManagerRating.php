<?php

namespace App\Tool\AccMgrDB;

use Illuminate\Database\Eloquent\Model;

class AccountManagerRating extends Model
{
    protected $table = "accMgrDB_Ratings";

    protected $casts = [
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected $hidden = [
        'show_key',
    ];

    protected $fillable = [
        'id',
        'template_id',
        'rating',
        'user_id',
    ];
    
    public function template() {
        return $this->belongsTo('App\Tool\AccMgrDB\AccountManagerTemplate', 'template_id');
    }
    
    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }
    
    public static function findForUser($templateId) {
        return (new AccountManagerRating())->where('user_id', \Auth::user()->id)->where('template_id', $templateId)->first();
    }
}
