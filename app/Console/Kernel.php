<?php

namespace App\Console;

use App\Util\BasicFunctions;
use App\Http\Controllers\DBController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];
    
    /*
     * Use something like:
     * ->everyFiveMinutes()->skip(worldNeedsUpdate($server, $world))
     * TODO create "daily" scedule for cleanOldEntries(...)
     * withoutOverlapping
     *          ->appendOutputTo($filePath);
     */
    
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /*
         * Update WorldData
         */
        $schedule->command("update:worldNextData")
            ->everyFiveMinutes()
            ->skip(! DBController::updateNeeded())
            //->runInBackground()
            ->onSuccess(function (){
                Log::debug('World -> Success');
            })
            ->onFailure(function (){
                Log::debug('World -> Failture');
            });

        /*
         * Update Conquers
         */
        $schedule->command('update:conquer')
            ->everyThirtyMinutes()
            //->runInBackground()
            ->onSuccess(function (){
                Log::debug('Conquer -> Erfolgreich');
            })
            ->onFailure(function (){
                Log::debug('Conquer -> Fehlgeschlagen');
            });

        $schedule->command('update:world')
            ->dailyAt('23:55');
        
        $schedule->command('session:gc')
            ->everyFifteenMinutes()
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
