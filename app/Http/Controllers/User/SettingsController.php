<?php

namespace App\Http\Controllers\User;

use App\DsConnection;
use App\Http\Controllers\Controller;
use App\Profile;
use App\Util\BasicFunctions;
use App\Village;
use App\World;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        $account = \Auth::user()->profile;
        $account->skype = $request->skype;
        $account->discord = $request->discord;
        $account->show_skype = $request->skype_show;
        $account->show_discord = $request->discord_show;
        $account->save();
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
}
