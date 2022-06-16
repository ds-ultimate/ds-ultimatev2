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

    public function __construct($arg1 = [], $arg2 = null)
    {
        if($arg1 instanceof World && $arg2 == null) {
            //allow calls without table name
            $arg2 = "index";
        }
        parent::__construct($arg1, $arg2);
    }
    
    public static function find(World $world, $id) {
        $model = new HistoryIndex($world);
        return $model->where('id', $id)->first();
    }
    
    public function allyFile(World $worldData) {
        return storage_path(config('dsUltimate.history_directory') . "{$worldData->serName()}/ally_{$this->date}.gz");
    }
    
    public function playerFile(World $worldData) {
        return storage_path(config('dsUltimate.history_directory') . "{$worldData->serName()}/player_{$this->date}.gz");
    }
    
    public function villageFile(World $worldData) {
        return storage_path(config('dsUltimate.history_directory') . "{$worldData->serName()}/village_{$this->date}.gz");
    }
}
