<?php

namespace App\Console;

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
        // $schedule->command('inspire')
        //          ->hourly();
        // FIXME: uncomment ->runInBackground() for live server
        /*
         * Update Village
         */
        $schedule->command('update:village')
            ->hourlyAt(1)
            //->runInBackground()
            ->onSuccess(function (){
                Log::debug('Village -> Erfolgreich');
            })
            ->onFailure(function (){
                Log::debug('Village -> Fehlgeschlagen');
            });

        /*
         * Update Ally
         */
        $schedule->command('update:ally')
            ->hourlyAt(1)
            //->runInBackground()
            ->onSuccess(function (){
                Log::debug('Ally -> Erfolgreich');
            })
            ->onFailure(function (){
                Log::debug('Ally -> Fehlgeschlagen');
            });

        /*
         * Update Player
         */
        $schedule->command('update:player')
            ->hourlyAt(1)
            //->runInBackground()
            ->onSuccess(function (){
                Log::debug('Player -> Erfolgreich');
            })
            ->onFailure(function (){
                Log::debug('Player -> Fehlgeschlagen');
            });

        /*
         * Update Player
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
            ->dailyAt('23:55')
            ->runInBackground();
        
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
