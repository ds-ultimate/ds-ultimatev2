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
    
    public static function find($dbName, $id) {
        $model = new HistoryIndex();
        $model->setTable("$dbName.index");
        return $model->where('id', $id)->first();
    }
    
    public function allyFile($dbName) {
        return storage_path(config('dsUltimate.history_directory') . "{$dbName}/ally_{$this->date}.gz");
    }
    
    public function playerFile($dbName) {
        return storage_path(config('dsUltimate.history_directory') . "{$dbName}/player_{$this->date}.gz");
    }
    
    public function villageFile($dbName) {
        return storage_path(config('dsUltimate.history_directory') . "{$dbName}/village_{$this->date}.gz");
    }
}
