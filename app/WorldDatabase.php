<?php

namespace App;

use App\Util\BasicFunctions;
use Illuminate\Database\Eloquent\Model;

class WorldDatabase extends Model
{
    protected $table = 'world_databases';

    protected $casts = [
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected $fillable = [
        'id',
        'name',
    ];
}
