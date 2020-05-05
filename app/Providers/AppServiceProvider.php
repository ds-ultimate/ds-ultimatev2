<?php

namespace App\Providers;

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
