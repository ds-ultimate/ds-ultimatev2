<?php

namespace App\Console\Commands;

use App\SpeedWorld;
use App\Console\DatabaseUpdate\DoSpeedWorldBackend;
use Illuminate\Console\Command;

class UpdateSpeedWorldBackend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:speedWorldBackend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aktualisiert die speed Welten über das backend in der Datenbank';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DoSpeedWorldBackend::run();
        return 0;
    }
    
    /**
     * nothin to do -> skipable
     */
    public static function canSkip() {
        $curActive = (new SpeedWorld())
            ->orWhere(function ($query) {
                return $query
                    ->where('planned_start', '<=', time())
                    ->where('planned_end', '>=', time())
                    ->where('started', false);
            })
            ->orWhere(function ($query) {
                return $query
                    ->where('planned_end', '<=', time())
                    ->where('started', true);
            })
            ->count();
        return $curActive == 0;
    }
}
