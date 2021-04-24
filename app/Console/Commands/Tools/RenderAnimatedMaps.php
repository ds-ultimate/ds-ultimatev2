<?php

namespace App\Console\Commands\Tools;

use App\Tool\AnimHistMap\AnimHistMapJob;
use App\Http\Controllers\Tools\AnimatedHistoryMapController;
use Illuminate\Console\Command;

class RenderAnimatedMaps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'animHistMap:render {id=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rendert einen Job';

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
        $id = $this->argument('id');
        
        if($id == "null") {
            $jobToDo = (new AnimHistMapJob())->whereNull('finished_at')->whereNull('state')->first();
            if($jobToDo == null) {
                echo "Nothing to do\n";
                return 0;
            }
        } else {
            $jobToDo = AnimHistMapJob::find($id);
            if($jobToDo == null) {
                echo "Job not found\n";
                return 0;
            }
        }
        
        AnimatedHistoryMapController::renderJob($jobToDo);
        return 0;
    }
    
    public static function renderNeeded() {
        $jobToDo = (new AnimHistMapJob())->whereNull('finished_at')->whereNull('state')->first();
        return $jobToDo != null;
    }
}
