<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CacheStat extends Model
{
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
