<?php

namespace App\Providers;

use App\Changelog;
use App\Util\BasicFunctions;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function ($view)
        {
            BasicFunctions::local();

            $changelog = Changelog::orderBy('created_at', 'desc')->first();
            if (\Auth::check()){
                $user = \Auth::user();
                if (\Session::get('last_seen_changelog')) {
                    $lastSeenChangelog = \Session::get('last_seen_changelog');
                    if ($lastSeenChangelog > $user->profile->last_seen_changelog) {
                        $user->profile->last_seen_changelog = $lastSeenChangelog;
                        $user->profile->save();
                    }
                }

                $newCangelog = $changelog->created_at > $user->profile->last_seen_changelog;

            }else{
                if (\Session::get('last_seen_changelog')) {
                    if (\Session::get('last_seen_changelog') < $changelog->created_at) {
                        $newCangelog = true;
                    }else{
                        $newCangelog = false;
                    }
                }else{
                    $newCangelog = $changelog->created_at->diffInDays() < 5;
                }
            }

            $view->with('newCangelog', $newCangelog);

        });

        Schema::defaultStringLength(191);

        Blade::directive('hasSlot', function() {
            return '<?php if(isset($slot) && $slot != ""): ?>';
        });
        Blade::directive('hasNoSlot', function() {
            return '<?php if(!isset($slot) || $slot == ""): ?>';
        });
        Blade::directive('setFalse', function($name) {
            return "<?php $name=false; ?>";
        });
        Blade::directive('setTrue', function($name) {
            return "<?php $name=true; ?>";
        });
    }
}
