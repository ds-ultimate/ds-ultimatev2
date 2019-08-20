<?php

namespace App\Http\Controllers;

use App\Bugreport;
use App\Http\Requests\BugreportRequest;
use App\Permission;
use App\User;
use App\Util\BasicFunctions;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function bugreport(){
        BasicFunctions::local();
        return view('forms.bugreport');
    }

    public function bugreportStore(BugreportRequest $request){

        $bugreport = Bugreport::create($request->all());

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
