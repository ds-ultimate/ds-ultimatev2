<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Notifications\DiscordNotification;
use App\Profile;
use App\Util\BasicFunctions;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Carbon\Carbon;
use mysql_xdevapi\Collection;
use NotificationChannels\Discord\Discord;
use WebSocket\Exception;

class LoginController extends Controller
{

    private static $drivers = [
        'facebook' => ['name' => 'facebook', 'icon' => 'fab fa-facebook', 'color' => '#4267B2'],
        'google' => ['name' => 'google', 'icon' => 'fab fa-google-plus', 'color' => '#ea4335'],
        'github' => ['name' => 'github', 'icon' => 'fab fa-github', 'color' => '#333333'],
        'twitter' => ['name' => 'twitter', 'icon' => 'fab fa-twitter', 'color' => '#1da1f2'],
        'discord' => ['name' => 'discord', 'icon' => 'fab fa-discord', 'color' => '#7289da'],
    ];

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    public static function getDriver(){
        return static::$drivers;
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToProvider($driver)
    {
        if ($driver == 'discord'){
            return Socialite::driver($driver)->scopes(['email'])->redirect();
        }
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
        try{
            $userAuth = Socialite::driver($driver)->user();
        }catch (\Exception $e){
            return redirect()->route('login');
        }

        if (Auth::check()){
            $driverName = $driver.'_id';
            $userProfile = Auth::user()->profile;
            if (Profile::where($driverName, $userAuth->getId())->count() > 0){
                return redirect()->route('user.settings', 'settings-account')->with('status', __('validation.unique', ['attribute' => ucfirst($driver).'-Konto']));
            }
            $userProfile->$driverName = $userAuth->getId();
            if ($driver == 'discord'){
                $userProfile->discord_private_channel_id = app(Discord::class)->getPrivateChannel($userAuth->getId());
            }
            $userProfile->save();

            return redirect()->route('user.settings', 'settings-account');
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
                if ($driver == 'discord'){
                    $userProfile->discord_private_channel_id = app(Discord::class)->getPrivateChannel($userAuth->getId());
                }
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

    public function destroyDriver($driver){
        $name = $driver.'_id';
        $profile = Auth::user()->profile;
        $i = 0;
        foreach (LoginController::$drivers as $driverArray){
            $driverName = $driverArray['name'].'_id';
            if ($profile->$driverName != null){
                $i++;
            }
        }
        if ($i > 1 || Auth::user()->getAuthPassword() != null){
            $profile->$name = null;
            if ($driver == 'discord'){
                $profile->discord_private_channel_id = null;
            }
            $profile->save();
            return redirect()->route('user.settings', 'settings-account');
        }else{
            return redirect()->route('user.settings', 'settings-account')->with('status', __('ui.personalSettings.noPassword'));
        }
    }
}
