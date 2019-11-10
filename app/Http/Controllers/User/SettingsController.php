<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Profile;
use App\Util\BasicFunctions;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function imgUploade(Request $request){
        BasicFunctions::local();
        \Log::warning($request->all());
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
        self::existProfile();
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
}
