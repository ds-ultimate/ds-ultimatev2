<?php

namespace App\Console\Commands;

use App\Console\DatabaseUpdate\UpdateUtil;
use App\Console\DatabaseUpdate\DoWorldData;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateNextWorld extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:nextWorld';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aktualisiert die nächste Welt';

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
        if(! UpdateUtil::updateNeeded()) {
            echo "No Update needed\n";
            return 0;
        }
        
        $cnt = BasicFunctions::getWorldQuery()->count();
        $toDo = ceil($cnt/(config('dsUltimate.db_update_every_hours') * 12));
        
        for($i = 0; $i < $toDo; $i++) {
            $world = BasicFunctions::getWorldQuery()
                    ->where('worldUpdated_at', '<',
                        Carbon::now()->subHours(config('dsUltimate.db_update_every_hours')))
                    ->orderBy('worldUpdated_at', 'ASC')->first();
            if($world == null) {
                break;
            }
            
            DoWorldData::run($world, 'v,p,a');
        }
        return 0;
    }
}
