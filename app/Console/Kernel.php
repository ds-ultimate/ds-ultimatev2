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
        $schedule->command("update:nextWorld")
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->skip(! DBController::updateNeeded())
            ->onSuccess(function (){
                Log::debug('World -> Success');
            })
            ->onFailure(function (){
                Log::debug('World -> Failture');
            })
            ->appendOutputTo("storage/logs/cron-critical.log");

        /*
         * Update WorldData
         */
        $schedule->command("update:nextClean")
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->skip(! DBController::cleanNeeded())
            ->onSuccess(function (){
                Log::debug('Clean -> Success');
            })
            ->onFailure(function (){
                Log::debug('Clean -> Failture');
            })
            ->appendOutputTo("storage/logs/cron-critical.log");
            
        /*
         * Update Conquers
         */
        $schedule->command('update:conquer no-progress')
            ->everyThirtyMinutes()
            ->withoutOverlapping()
            ->onSuccess(function (){
                Log::debug('Conquer -> Erfolgreich');
            })
            ->onFailure(function (){
                Log::debug('Conquer -> Fehlgeschlagen');
            })
            ->appendOutputTo("storage/logs/cron-critical.log");

        $schedule->command('update:world')
            ->dailyAt('23:55')
            ->appendOutputTo("storage/logs/cron-critical.log");
        
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
