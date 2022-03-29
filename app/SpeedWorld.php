<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class SpeedWorld extends CustomModel
{
    use SoftDeletes;

    protected $table = 'speed_worlds';
    protected $connection = 'mysql';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'id',
        'server_id',
        'name',
        'planned_start',
        'planned_end',
        'started',
        'worldCheck_at',
    ];
    
    protected $cache = [
        'server',
        'world',
    ];

    /**
     * Verbindet die world Tabelle mit der server Tabelle
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function server(){
        return $this->belongsTo('App\Server','server_id');
    }

    /**
     * Verbindet die world Tabelle mit der server Tabelle
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function world(){
        return $this->belongsTo('App\World','world_id');
    }
}
