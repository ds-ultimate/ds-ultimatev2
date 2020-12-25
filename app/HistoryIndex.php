<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoryIndex extends Model
{

    protected $connection = 'mysql';
    protected $table = 'index';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'id',
        'date',
    ];
}
