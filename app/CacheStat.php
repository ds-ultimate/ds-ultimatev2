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

    protected $casts = [
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

}
