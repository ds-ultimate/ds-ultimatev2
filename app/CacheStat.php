<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CacheStat extends Model
{
    use SoftDeletes;
    
    protected $table = 'cache_stats';

    protected $fillable = [
        'type',
        'hits',
        'misses',
        'date',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

}
