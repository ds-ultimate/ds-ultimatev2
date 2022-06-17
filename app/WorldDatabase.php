<?php

namespace App;

use App\Util\BasicFunctions;
use Illuminate\Database\Eloquent\Model;

class WorldDatabase extends Model
{
    protected $table = 'world_databases';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'id',
        'name',
    ];
}
