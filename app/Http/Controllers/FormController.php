<?php

namespace App\Http\Controllers;

use App\Bugreport;
use App\Permission;
use App\Util\BasicFunctions;

use Illuminate\Http\Request;

class FormController extends Controller
{
    public function bugreport(){
        return view('forms.bugreport');
    }

    public function bugreportStore(Request $request){
        $validated =  $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'title' => 'required',
            'priority' => 'required',
            'description' => 'required',
            'custom_captcha' => 'required|customCaptcha',
        ]);
        
        $bugreport = Bugreport::create($validated);
        $permission = Permission::where('title', 'bugreport_notification')->first();
        $roles = $permission->roles;
        $users = collect();

        foreach ($roles as $role){
            foreach ($role->users as $user){
                $users->add($user);
            }
        }

        if (config('app.debug') == false) {
            \Notification::send($users, new \App\Notifications\Bugreport($bugreport));
        }

        return redirect()->route('index')->with('successBugreport', __('user.bugreport.successMessage'));
    }
}
