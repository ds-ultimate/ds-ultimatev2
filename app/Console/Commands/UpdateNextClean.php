<?php

namespace App\Console\Commands;

use App\Console\DatabaseUpdate\DoClean;
use App\Console\DatabaseUpdate\UpdateUtil;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateNextClean extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:nextClean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Entfernt alte einträge von der nächsten Welt';

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
        \App\Util\BasicFunctions::ignoreErrs();
        if(! UpdateUtil::cleanNeeded()) {
            echo "No Clean needed\n";
            return 0;
        }
        $cnt = \App\Util\BasicFunctions::getWorldQuery()->count();
        $toDo = ceil($cnt/(config('dsUltimate.db_clean_every_hours') * 12));
        
        for($i = 0; $i < $toDo; $i++) {
            $world = \App\Util\BasicFunctions::getWorldQuery()
                    ->where('worldCleaned_at', '<', Carbon::now()
                        ->subHours(config('dsUltimate.db_clean_every_hours')))
                    ->orderBy('worldCleaned_at', 'ASC')->first();
            if($world == null) {
                break;
            }
            
            //DoClean::run($world, 'v');
            DoClean::run($world, 'p');
            DoClean::run($world, 'a');
        }
        return 0;
    }
}
