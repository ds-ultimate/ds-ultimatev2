<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Profile;
use App\Util\BasicFunctions;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Carbon\Carbon;

class LoginController extends Controller
{
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($driver)
    {
        return Socialite::driver($driver)->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handleProviderCallback($driver)
    {
        BasicFunctions::local();
        $userAuth = Socialite::driver($driver)->user();

        if (Auth::check()){
            $driverName = $driver.'_id';
            $userProfile = Auth::user()->profile;
            $userProfile->$driverName = $userAuth->getId();
            $userProfile->save();
            return \redirect($this->redirectTo.'user/settings/settings-account');
        }

        $profile = Profile::where($driver.'_id', $userAuth->getId())->first();

        if (!$profile){
            $validator = Validator::make(['email' => $userAuth->getEmail()], [
                'email' => ['unique:users'],
            ]);
            if ($validator->valid())
            {
                $user = User::create([
                    'name' => $userAuth->getName(),
                    'email' => $userAuth->getEmail(),
                    'email_verified_at' => Carbon::createFromTimestamp(time()),
                ]);

                $driverName = $driver.'_id';
                $userProfile = $user->profile;
                $userProfile->$driverName = $userAuth->getId();
                $userProfile->save();
                Auth::login($user, true);
                return \redirect($this->redirectTo);
            }else{
                $messages = $validator->messages();
                $status = $messages->messages();
                return redirect()->route('login')->with('status', $status['email'][0]);
            }
        }else{
            $user = User::find($profile->user_id);
            Auth::login($user, true);
            return \redirect($this->redirectTo);
        }

    }
}
