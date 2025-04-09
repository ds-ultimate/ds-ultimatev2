<?php

namespace App\Tool\AnimHistMap;

use Illuminate\Database\Eloquent\SoftDeletes;

class AnimHistMapJob extends AnimHistMapMap
{
    use SoftDeletes;

    protected $table = 'animHistMapJob';

    protected $casts = [
        'finished_at' => 'datetime',
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'edit_key',
        'show_key',
    ];

    protected $fillable = [
        'world_id',
        'user_id',
        'edit_key',
        'show_key',
        'markers',
        'opaque',
        'skin',
        'layers',
        'autoDimensions',
        'dimensions',
        'defaultColours',
        'title',
        'markerFactor',
        'continentNumbers',
        'showLegend',
        'legendSize',
        'legendPosition',
        'finished_at',
        'state',
        'animHistMapMap_id'
    ];
    
    protected $cache = [
        'world',
    ];
    
    public function getStateAsArray() {
        if($this->finished_at !== null) {
            return [
                "cur" => 0,
                "max" => 0,
                "text" => __("tool.animHistMap.render.finished"),
                "finished" => 1,
            ];
        }
        
        $state = explode(";", $this->state);
        if(count($state) < 3) {
            return [
                "cur" => 0,
                "max" => 0,
                "text" => __("tool.animHistMap.render.queue"),
                "finished" => 0,
            ];
        }
        
        $translated = __("tool.animHistMap.render." . $state[2]);
        $translated = str_replace("{numImage}", $state[3], $translated);
        $translated = str_replace("{totalImage}", $state[4], $translated);
        
        return [
            "cur" => $state[0],
            "max" => $state[1],
            "text" => $translated,
            "finished" => 0,
        ];
    }
    
    public function setState($curProgress, $maxProgress, $transID, $transAt, $transOf) {
        $this->state = "$curProgress;$maxProgress;$transID;$transAt;$transOf";
        $this->save();
    }
    
    /**
     * @return AnimatedHistMapMap
     */
    public function srcMap(){
        return $this->belongsTo('App\Tool\AnimHistMap\AnimHistMapMap', 'animHistMapMap_id');
    }
    
    /**
     * returns preview route for this map
     */
    public function preview() {
        if($this->srcMap == null) {
            return "";
        }
        return $this->srcMap->preview();
    }
}
