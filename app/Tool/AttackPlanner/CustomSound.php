<?php
/**
 * Created by IntelliJ IDEA.
 * User: crams
 * Date: 18.08.2019
 * Time: 16:10
 */

namespace App\Tool\AttackPlanner;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomSound extends Model
{
    protected $table = "attackplanner_custom_sound";

    protected $fillable = [
        'user_id',
        'name',
        'internal_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function generateUUID() {
        for($i = 0; $i < 10; $i++) {
            $fName = Str::random(20);
            if(!is_file(storage_path(config('dsUltimate.attackPlannerSoundDirectory') . "/" . $fName))) {
                $this->internal_id = $fName;
                return;
            }
        }
        throw new Exception("Unable to find free space");
    }

    public function getFilePath() {
        return storage_path(config('dsUltimate.attackPlannerSoundDirectory') . "/" . $this->internal_id);
    }
    
    public function saveFile($file) {
        $file->move(storage_path(config('dsUltimate.attackPlannerSoundDirectory')), $this->internal_id);
    }

    public function generateURL() {
        return route("tools.attackPlannerSound.fetch", [$this->id]);
    }

    public function deleteFile() {
        if(is_file($this->getFilePath())) {
            unlink($this->getFilePath());
        }
    }
}
