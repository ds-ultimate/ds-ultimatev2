<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BugreportComment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'bugreport_id',
        'user_id',
        'content',
    ];

    public $timestamps = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function users(){
        return $this->belongsTo('App\User', 'user_id');
    }

}
