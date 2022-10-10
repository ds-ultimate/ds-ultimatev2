<?php

namespace App\Console\Commands\Tools;

use App\Tool\AnimHistMap\AnimHistMapJob;
use App\Http\Controllers\Tools\AnimatedHistoryMapController;
use Illuminate\Console\Command;

class AnimatedMapsClean extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'animHistMap:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Entfernt alte .zip files';

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
        $ftimeMin = time() - config("tools.animHistMap.zipSaveDuration");
        foreach((new AnimHistMapJob())->whereNotNull('finished_at')->get() as $job) {
            $fName = AnimatedHistoryMapController::getFilePathByType($job, 'zip');
            if(is_file($fName)) {
                if($ftimeMin > filemtime($fName)) {
                    unlink($fName);
                }
            }
        }

        $idList = [];
        foreach((new AnimHistMapJob())->get() as $job) {
            $idList[$job->id] = 1;
        }
        $rPath = storage_path(config('tools.animHistMap.renderDir'));
        $files = scandir($rPath);
        foreach($files as $file) {
            if($file == "." || $file == "..") continue;
            if(! isset($idList[(int) $file])) {
                $this->deleteRecursive("$rPath/$file");
            }
        }
        return 0;
    }

    public function deleteRecursive($path) {
        foreach(scandir($path) as $f) {
            if($f == "." || $f == "..") continue;
            if(is_dir($f)) $this->deleteRecursive("$path/$f");
            else unlink("$path/$f");
        }
        rmdir($path);
    }
}
