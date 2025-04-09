<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    protected $connection = 'mysql';
    protected $table = 'signature';

    protected $casts = [
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
        'cached' => 'datetime',
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
    
    public function getCacheFileName() {
        $constrained = explode("\\", $this->element_type);
        return "{$this->id}-{$constrained[count($constrained) - 1]}-{$this->element_id}";
    }
    
    public function getCacheFile() {
        $constrained = explode("\\", $this->element_type);
        return storage_path(config('tools.signature.cacheDir')."{$this->id}-{$constrained[count($constrained) - 1]}-{$this->element_id}.png");
    }
}
