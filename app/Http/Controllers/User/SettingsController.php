<?php

namespace App\Http\Controllers\User;

use App\DsConnection;
use App\Http\Controllers\API\DatatablesController;
use App\Http\Controllers\Controller;
use App\Player;
use App\Profile;
use App\Util\BasicFunctions;
use App\Village;
use App\World;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class SettingsController extends Controller
{
    public static function existProfile(){
        $user = \Auth::user();
        if ($user->profile()->count() == 0){
            Profile::create([
                'user_id' => $user->id
            ]);
        }
    }

    public function addConnection(Request $request){
        $data = $request->validate([
            'player' => 'required|integer',
            'world' => "required|integer|exists:App\World,id",
        ]);
        
        $world = World::findOrFail($data['world']);
        
        if (DsConnection::where('user_id', \Auth::user()->id)->where('world_id', $world->id)->where('player_id', $data['player'])->count() == 0) {
            DsConnection::create([
                'user_id' => \Auth::user()->id,
                'world_id' => $world->id,
                'player_id' => $request->get('player'),
                'key' => Str::random(32),
            ]);
        }
    }

    public function saveSettingsAccount(Request $request){
        $user = \Auth::user();
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        $user->name = $data['name'];
        $user->save();
        return \Response::json(array(
            'data' => 'success',
            'msg' => __('ui.personalSettings.saveSettingsSuccess'),
        ));
    }

    public function saveMapSettings(Request $request){
        $user = \Auth::user();
        $profile = $user->profile;
        
        $data = $request->validate([
            'default' => 'array',
            'default.background' => 'string|size:6',
            'default.player' => 'string|size:6',
            'default.barbarian' => 'string|size:6',
            'showBarbarianHere' => 'boolean',
            'showBarbarian' => 'string|max:3',
            'showPlayerHere' => 'boolean',
            'showPlayer' => 'string|max:3',
            'zoomValue' => 'required|numeric|integer',
            'centerX' => 'required|numeric|integer',
            'centerY' => 'required|numeric|integer',
            'markerFactor' => 'required|numeric|min:0|max:1',
        ]);
        
        if(isset($data['default'])) {
            $profile->setDefaultColours(
                    (isset($data['default']['background']))?($data['default']['background']):(null),
                    (isset($data['default']['player']))?($data['default']['player']):(null),
                    (isset($data['default']['barbarian']))?($data['default']['barbarian']):(null)
            );
        }
        //do this after setting Default Colours as it modifies the same Property
        if(isset($data['showBarbarianHere'])) {
            if(!isset($data['showBarbarian'])) {
                $profile->disableBarbarian();
            }
        }
        if(isset($data['showPlayerHere'])) {
            if(!isset($data['showPlayer'])) {
                $profile->disablePlayer();
            }
        }
        
        $zoom = (int) $data['zoomValue'];
        $cX = (int) $data['centerX'];
        $cY = (int) $data['centerY'];

        $profile->setDimensions([
            'xs' => ceil($cX - $zoom / 2),
            'xe' => ceil($cX + $zoom / 2),
            'ys' => ceil($cY - $zoom / 2),
            'ye' => ceil($cY + $zoom / 2),
        ]);
        
        if(isset($data['markerFactor'])) {
            $profile->map_markerFactor = $data['markerFactor'];
        }
        $profile->save();
        
        return \Response::json(array(
            'data' => 'success',
            'msg' => __('ui.personalSettings.saveSettingsSuccess'),
        ));
    }

    public function destroyConnection(Request $request){
        $data = $request->validate([
            'id' => 'required|numeric|integer',
            'key' => 'required|string',
        ]);
        
        $connection = DsConnection::find($data['id']);
        if ($connection->key == $data['key']){
            $connection->delete();
            return \Response::json(array(
                'data' => 'success',
                'msg' => __('ui.personalSettings.connectionDestroy'),
            ));
        }else{
            return \Response::json(array(
                'data' => 'error',
                'msg' => __('ui.personalSettings.connectionNiceTroll'),
            ));
        }

    }

    public function checkConnection(Request $request){
        $data = $request->validate([
            'id' => 'required|numeric|integer',
        ]);
        
        $connection = DsConnection::find($data['id']);
        $world = $connection->world;
        $villageModel = new Village($world);
        $villageCount = $villageModel->where(['owner' => $connection->player_id, 'name' => $connection->key])->count();
        if ($connection->created_at->floatDiffInHours($world->worldUpdated_at, false) < 0){
            return \Response::json(array(
                'data' => 'error',
                'msg' => __('ui.personalSettings.connectionTooEarly'),
            ));
        }

        if ($villageCount > 0){
            $connection->verified = 1;
            $connection->save();
            return \Response::json(array(
                'data' => 'success',
                'msg' => __('ui.personalSettings.connectionSuccess'),
            ));
        }

        return \Response::json(array(
            'data' => 'error',
            'msg' => __('ui.personalSettings.connectionError'),
        ));
    }

    public function saveConquerHighlighting(Request $request, $type){
        $valid = [];
        foreach(Profile::$CONQUER_HIGHLIGHT_MAPPING as $value) {
            $valid[$value] = 'boolean';
        }
        $data = $request->validate($valid);
        
        $user = \Auth::user();
        $profile = $user->profile;
        
        $profileStr = "";
        foreach(Profile::$CONQUER_HIGHLIGHT_MAPPING as $key => $value) {
            if(isset($data[$value]) && $data[$value]) {
                if(strlen($profileStr) > 0) $profileStr .= ":";
                $profileStr .= $key;
            }
        }
        
        switch($type) {
            case "world":
                $profile->conquerHightlight_World = $profileStr;
                break;
            case "ally":
                $profile->conquerHightlight_Ally = $profileStr;
                break;
            case "player":
                $profile->conquerHightlight_Player = $profileStr;
                break;
            case "village":
                $profile->conquerHightlight_Village = $profileStr;
                break;
        }
        $profile->save();
    }
    
    public function getDsConnection()
    {
        $whitelist = ['server', 'world', 'player', 'key', 'action'];
        DatatablesController::limitResults(100,$whitelist);

        $datas = \Auth::user()->dsConnection();

        return DataTables::eloquent($datas)
            ->addColumn('server', function ($connection) {
                return "<span><span class=\"flag-icon flag-icon-" . $connection->world->server->flag . "\"></span>" . ucwords($connection->world->server->code) . "</span>";
            })
            ->addColumn('world', function ($connection) {
                return $connection->world->getDistplayName();
            })
            ->addColumn('player', function ($connection) {
                $player = Player::player($connection->world, $connection->player_id);
                return BasicFunctions::decodeName(($player != null)?$player->name:'<b>'.__('ui.player.deleted').'</b>');
            })
            ->editColumn('key', function ($connection) {
                if ($connection->verified == 0) {
                    return'<div class="input-group mb-2">
                                <input id="key_'.$connection->id.'" type="text" class="form-control" value="'.$connection->key.'" aria-label="Recipient\'s username" aria-describedby="basic-addon2">
                                <div class="input-group-append">
                                    <span class="input-group-text" style="cursor:pointer" id="basic-addon2" onclick="copy(\'key_'.$connection->id.'\')"><i class="far fa-copy"></i></span>
                                </div>
                            </div>';
                }
                return'<b>'.__('ui.personalSettings.connectionVerified').'</b>';
            })
            ->addColumn('action', function ($connection) {
                $button = '';
                if ($connection->verified == 0) {
                    $button = '<a class="btn btn-success" style="cursor:pointer" onclick="checkConnection('.$connection->id.')"><i class="fas fa-sync text-white"></i></a> ';
                }
                $button .= '<a class="btn btn-danger" style="cursor:pointer" onclick="destroyConnection('.$connection->id.', \''.$connection->key.'\')"><i class="fas fa-times text-white"></i></a>';

                return $button;
            })
            ->rawColumns(['server', 'action','key', 'player'])
            ->whitelist($whitelist)
            ->toJson();
    }
}
