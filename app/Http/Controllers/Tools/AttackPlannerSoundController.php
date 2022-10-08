<?php
/**
 * Created by IntelliJ IDEA.
 * User: crams
 * Date: 10.08.2019
 * Time: 20:38
 */

namespace App\Http\Controllers\Tools;


use App\Tool\AttackPlanner\CustomSound;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class AttackPlannerSoundController extends BaseController
{
    public static function getAlarmData() {
        $staticData = [
            __("sound.attPlanner.siren") => "sounds/attackplanner/420661__kinoton__alarm-siren-fast-oscillations.mp3",
            __("sound.attPlanner.ahem") => "sounds/attackplanner/ahem_x.mp3",
            __("sound.attPlanner.alarm_beep") => "sounds/attackplanner/alarm_beep.mp3",
            __("sound.attPlanner.baseball") => "sounds/attackplanner/baseball_hit.mp3",
            __("sound.attPlanner.bicycle") => "sounds/attackplanner/bicycle_bell.mp3",
            __("sound.attPlanner.blip") => "sounds/attackplanner/blip.mp3",
            __("sound.attPlanner.bloop") => "sounds/attackplanner/bloop_x.mp3",
            __("sound.attPlanner.blurp") => "sounds/attackplanner/blurp_x.mp3",
            __("sound.attPlanner.boing1") => "sounds/attackplanner/boing2.mp3",
            __("sound.attPlanner.boing2") => "sounds/attackplanner/boing3.mp3",
            __("sound.attPlanner.boing_poing") => "sounds/attackplanner/boing_poing.mp3",
            __("sound.attPlanner.boing_spring") => "sounds/attackplanner/boing_spring.mp3",
            __("sound.attPlanner.boing3") => "sounds/attackplanner/boing_x.mp3",
            __("sound.attPlanner.buzzer1") => "sounds/attackplanner/buzzer3_x.mp3",
            __("sound.attPlanner.buzzer_rd") => "sounds/attackplanner/buzzer_rd.mp3",
            __("sound.attPlanner.buzzer2") => "sounds/attackplanner/buzzer_x.mp3",
            __("sound.attPlanner.cannon") => "sounds/attackplanner/cannon_x.mp3",
            __("sound.attPlanner.car_horn") => "sounds/attackplanner/car_horn_x.mp3",
            __("sound.attPlanner.honk1") => "sounds/attackplanner/honk_x.mp3",
            __("sound.attPlanner.honk2") => "sounds/attackplanner/honk2_x.mp3",
            __("sound.attPlanner.honk_honk") => "sounds/attackplanner/honk_honk_x.mp3",
            __("sound.attPlanner.timer") => "sounds/attackplanner/timer.mp3",
            __("sound.attPlanner.truck_horn") => "sounds/attackplanner/truck_horn.mp3",
            __("sound.attPlanner.warning_horn") => "sounds/attackplanner/warning_horn.mp3",
        ];
        
        if(\Auth::user() != null && \Auth::user()->profile != null) {
            return array_merge(\Auth::user()->profile->getCustomSoundInfo(), $staticData);
        }
        return $staticData;
    }
    
    public function uploadSound(Request $request) {
        $valid = $request->validate([
            'file' => ['required', File::types(['mp3'])->max(100 * 1024)],
            'name' => ['alpha_num', 'max:20', Rule::unique('attackplanner_custom_sound')->where("user_id", \Auth::user()->id)],
        ]);
        
        $curSounds = ['sounds' => \Auth::user()->profile->customSounds->toArray()];
        Validator::validate($curSounds, [
            'sounds' => "array|max:5",
        ]);
        
        $model = new CustomSound();
        $model-> user_id = \Auth::user()->id;
        $model->name = $valid['name'];
        $model->generateUUID();
        $model->saveFile($valid['file']);
        $model->save();
    }
    
    public function getSound(CustomSound $sound) {
        abort_unless(\Auth::user()->id == $sound->user_id, 403);
        
        return response()->file($sound->getFilePath());
    }
    
    public function editName(Request $request, CustomSound $sound) {
        $valid = $request->validate([
            'name' => ['alpha_num', 'max:20', Rule::unique('attackplanner_custom_sound')
                ->where("user_id", \Auth::user()->id)->whereNot("id", $sound->id)],
        ]);
        abort_unless(\Auth::user()->id == $sound->user_id, 403);
        
        $sound->name = $valid['name'];
        $sound->save();
    }
    
    public function deleteSound(CustomSound $sound) {
        abort_unless(\Auth::user()->id == $sound->user_id, 403);
        $sound->deleteFile();
        $sound->delete();
    }
}
