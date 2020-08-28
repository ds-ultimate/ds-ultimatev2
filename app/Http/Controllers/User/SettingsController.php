<?php

namespace App\Http\Controllers\User;

use App\DsConnection;
use App\Http\Controllers\Controller;
use App\Player;
use App\Profile;
use App\Util\BasicFunctions;
use App\Village;
use App\World;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class SettingsController extends Controller
{
    public function imgUploade(Request $request){
        BasicFunctions::local();
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        $img = $request->file;
        $imgNewName = time().\Auth::user()->name.'.'.$img->extension();
        $path = Storage::putFileAs('avatar', $img, $imgNewName);
        self::existProfile();
        \Auth::user()->profile()->update(['avatar' => $path]);
        return response()->json([
            'img' => $path,
        ], 201);
    }

    public function imgDestroy(){
        $avatar = \Auth::user()->profile->avatar;
        self::existProfile();
        if (Storage::disk('local')->exists($avatar)) {
            Storage::disk('local')->delete($avatar);
        }
        \Auth::user()->profile()->update(['avatar' => null]);
    }

    public static function existProfile(){
        $user = \Auth::user();
        if ($user->profile()->count() == 0){
            Profile::create([
                'user_id' => $user->id
            ]);
        }
    }

    public function addConnection(Request $request){
        $serverId = intval($request->get('server'));
        $request->validate([
            'player' => 'required|integer',
            'server' => 'required|integer|exists:App\Server,id',
            'world' => "required|integer|exists:App\World,name,server_id,{$serverId}",
        ]);
        
        $worldModel = new World();
        $world = $worldModel->where('server_id', $request->get('server'))->where('name', $request->get('world'))->first();
        
        if (DsConnection::where('user_id', \Auth::user()->id)->where('world_id', $world->id)->where('player_id', $request->get('player'))->count() == 0) {
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
        $validator = Validator::make(['name' => $request->name], [
            'name' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->valid())
        {
            $user->name = $request->name;
            $profile = $user->profile;
            $dt = Carbon::parse($request->birthday);
            $profile->birthday = $dt->format('Y-m-d');
            $user->save();
            $profile->save();
            return \Response::json(array(
                'data' => 'success',
                'msg' => __('ui.personalSettings.saveSettingsSuccess'),
            ));
        }else{
            return \Response::json(array(
                'data' => 'error',
                'msg' => $validator->errors()->first(),
            ));
        }
    }

    public function saveMapSettings(Request $request){
        $user = \Auth::user();
        $profile = $user->profile;
        
        if(isset($request->default)) {
            $profile->setDefaultColours(
                    (isset($request->default['background']))?($request->default['background']):(null),
                    (isset($request->default['player']))?($request->default['player']):(null),
                    (isset($request->default['barbarian']))?($request->default['barbarian']):(null)
            );
        }
        //do this after setting Default Colours as it modifies the same Property
        if(isset($request->showBarbarianHere)) {
            if(!isset($request->showBarbarian)) {
                $profile->disableBarbarian();
            }
        }
        if(isset($request->showPlayerHere)) {
            if(!isset($request->showPlayer)) {
                $profile->disablePlayer();
            }
        }
        
        if(isset($request->zoomValue) &&
                isset($request->centerX) &&
                isset($request->centerY)) {
            $zoom = (int) $request->zoomValue;
            $cX = (int) $request->centerX;
            $cY = (int) $request->centerY;
            
            $profile->setDimensions([
                'xs' => ceil($cX - $zoom / 2),
                'xe' => ceil($cX + $zoom / 2),
                'ys' => ceil($cY - $zoom / 2),
                'ye' => ceil($cY + $zoom / 2),
            ]);
        }
        
        if(isset($request->markerFactor)) {
            $profile->map_markerFactor = $request->markerFactor;
        }
        $profile->save();
        
        return \Response::json(array(
            'data' => 'success',
            'msg' => __('ui.personalSettings.saveSettingsSuccess'),
        ));
    }

    public function destroyConnection(Request $request){
        $connection = DsConnection::find($request->get('id'));
        if ($connection->key == $request->get('key')){
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
        $connection = DsConnection::find($request->get('id'));
        $world = $connection->world;
        $villageModel = new Village();
        $villageModel->setTable(BasicFunctions::getDatabaseName($world->server->code, $world->name).'.village_latest');
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
        $user = \Auth::user();
        $profile = $user->profile;
        
        $profileStr = "";
        foreach(Profile::$CONQUER_HIGHLIGHT_MAPPING as $key => $value) {
            if($request->get($value)) {
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

            BasicFunctions::local();
            $datas = \Auth::user()->dsConnection();

            return DataTables::eloquent($datas)
                ->addColumn('server', function ($connection) {
                    return "<span><span class=\"flag-icon flag-icon-" . $connection->world->server->flag . "\"></span>" . ucwords($connection->world->server->code) . "</span>";
                })
                ->addColumn('world', function ($connection) {
                    return $connection->world->displayName();
                })
                ->addColumn('player', function ($connection) {
                    $player = Player::player($connection->world->server->code, $connection->world->name, $connection->player_id);
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
                ->toJson();
    }
}
