<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateCleanCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:cleanCache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Entfernt unnötige files aus dem cache';

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
        static::cleanCache(config("tools.chart.cacheDir"), config("tools.chart.cacheDuration"));
        static::cleanCache(config("tools.signature.cacheDir"), config("tools.signature.cacheDuration"));
        return 0;
    }
    
    public function cleanCache($conf, $time) {
        $dir = storage_path($conf);
        $all = 0;
        $deleted = 0;
        $ftimeMin = time() - $time;
        $this->recursiveCleanCache($dir, $ftimeMin, $all, $deleted);
        echo "$conf: Removed $deleted of $all\n";
    }
        
    public function recursiveCleanCache($dir, $ftimeMin, &$all, &$deleted) {
        if(file_exists($dir)) {
            $files = scandir($dir);
            foreach($files as $file) {
                if($file == "." || $file == "..") continue;
                $tmp = "$dir/$file";
                if(is_dir($tmp)) {
                    $this->recursiveCleanCache($tmp, $ftimeMin, $all, $deleted);
                } else if(is_file($tmp)) {
                    $all++;
                    if($ftimeMin > filemtime($tmp)) {
                        unlink($tmp);
                        $deleted++;
                    }
                }
            }
        }
    }
}
