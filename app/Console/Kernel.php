<?php

namespace App\Console;

use App\Console\Commands\Tools\RenderAnimatedMaps;
use App\Console\Commands\UpdateSpeedWorldBackend;
use App\Console\DatabaseUpdate\UpdateUtil;
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
            ->skip(! UpdateUtil::updateNeeded())
            ->onSuccess(function (){
                Log::info('World -> Success');
            })
            ->onFailure(function (){
                Log::critical('World -> Failture');
            })
            ->appendOutputTo("storage/logs/cron-critical.log");

        /*
         * Clean WorldData
         */
        $schedule->command("update:nextClean")
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->skip(! UpdateUtil::cleanNeeded())
            ->onSuccess(function (){
                Log::info('Clean -> Success');
            })
            ->onFailure(function (){
                Log::critical('Clean -> Failture');
            })
            ->appendOutputTo("storage/logs/cron-critical.log");

        /*
         * Update Conquers
         */
        $schedule->command('update:conquer no-progress')
            ->everyThirtyMinutes()
            ->withoutOverlapping()
            ->onSuccess(function (){
                Log::info('Conquer -> Erfolgreich');
            })
            ->onFailure(function (){
                Log::critical('Conquer -> Fehlgeschlagen');
            })
            ->appendOutputTo("storage/logs/cron-critical.log");

        $schedule->command('update:world')
            ->dailyAt('23:55')
            ->appendOutputTo("storage/logs/cron-critical.log");

        $schedule->command('update:statistic')
            ->dailyAt('23:55')
            ->appendOutputTo("storage/logs/cron-critical.log");

        //run this only when there was at least one insert per world / will ignore entries if enty date != current date
        $schedule->command('update:worldHistory')
            ->dailyAt('3:05')
            ->appendOutputTo("storage/logs/cron-critical.log");
        

        //speed servers
        //loads the upcoming speed worlds into the database
        $schedule->command('update:speedWorld')
            ->dailyAt('23:50')
            ->appendOutputTo("storage/logs/cron-critical.log");
        
        $schedule->command('update:speedWorldBackend')
            ->everyFifteenMinutes()
            ->skip(UpdateSpeedWorldBackend::canSkip())
            ->appendOutputTo("storage/logs/cron-critical.log");
        
        
        $schedule->command('session:gc')
            ->everyFifteenMinutes()
            ->runInBackground();
        
        /*
         * Generate next animatedWorldMap
         */
        $schedule->command("animHistMap:render")
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->skip(! RenderAnimatedMaps::renderNeeded())
            ->onSuccess(function (){
                Log::info('Render -> Success');
            })
            ->onFailure(function (){
                Log::critical('Render -> Failture');
            })
            ->appendOutputTo("storage/logs/cron-critical.log");

        /*
         * Map Caching
         */
        $schedule->command("map:cache")
            ->dailyAt('01:55')
            ->onSuccess(function (){
                Log::info('Caching -> Success');
            })
            ->onFailure(function (){
                Log::critical('Caching -> Failture');
            })
            ->appendOutputTo("storage/logs/cron-critical.log");

        /*
         * Insert Conquer Data
         */
        $schedule->command("update:insertMissingConquer")
            ->dailyAt('02:43')
            ->onSuccess(function (){
                Log::info('ConquerData -> Success');
            })
            ->onFailure(function (){
                Log::critical('ConquerData -> Failture');
            })
            ->appendOutputTo("storage/logs/cron-critical.log");
        
        /*
         * Update Top values
         */
        $schedule->command("update:generateTops no-progress")
            ->dailyAt('00:35')
            ->onSuccess(function (){
                Log::info('generateTops -> Success');
            })
            ->onFailure(function (){
                Log::critical('generateTops -> Failture');
            })
            ->appendOutputTo("storage/logs/cron-critical.log");
        
        /*
         * Send out Discord notifications
         */
        $schedule->command("send:discordNotifications")
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->appendOutputTo("storage/logs/cron-critical.log");
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
