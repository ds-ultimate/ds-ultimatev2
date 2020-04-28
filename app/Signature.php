<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    protected $connection = 'mysql';
    protected $table = 'signature';

    protected $dates = [
        'created_at',
        'updated_at',
        'cached',
    ];

    protected $fillable = [
        'id',
        'worlds_id',
        'element_id',
        'element_type',
        'cached',
    ];
    
    public function isCached() {
        if(!isset($this->cached) || $this->cached == null) return false;
        
        return Carbon::now()->subSeconds(config('tools.signature.cacheDuration'))->lt($this->cached);
    }
    
    public function getCacheFile() {
        $constrained = explode("\\", $this->element_type);
        return "../".config('tools.signature.cacheDir')."{$constrained[count($constrained) - 1]}-{$this->element_id}.png";
    }
}
