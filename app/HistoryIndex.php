<?php

namespace App;

class HistoryIndex extends CustomModel
{

    protected $connection = 'mysql';
    protected $table = 'index';

    protected $casts = [
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
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
