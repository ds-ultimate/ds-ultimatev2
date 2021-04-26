<?php

namespace App\Console\Commands\Tools;

use App\Tool\Map\Map;
use App\Util\SQLMapGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class MapCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'map:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cached alle maps die seit >1h nicht mehr bearbeitet wurden und noch nicht gecached wurden';

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
        $maps = (new Map())->whereNull('cached_at')->where('shouldUpdate', false)->get();
        if (!file_exists(storage_path(config('tools.map.cacheDir')))) {
            mkdir(storage_path(config('tools.map.cacheDir')), 0777, true);
        }
        
        foreach ($maps as $map){
            $error = false;
            $error |= $this->cacheMap($map, 2000);
            $error |= $this->cacheMap($map, 1000);
            $error |= $this->cacheMap($map, 700);
            $error |= $this->cacheMap($map, 500);
            $error |= $this->cacheMap($map, 200);
            
            if(! $error) {
                $map->cached_at = Carbon::now();
                $map->save();
            }
        }
        return 0;
    }
    
    private function cacheMap(Map $map, $size) {
        try {
            $mapGen = new SQLMapGenerator($map->world, [
                'width' => $size,
                'height' => $size,
            ], false);

            $map->prepareRendering($mapGen);

            $mapGen->render();

            $mapGen->saveTo(storage_path(config('tools.map.cacheDir')."{$map->id}-$size"), "png");
        } catch (\Exception $ex) {
            return true;
        }
        return false;
    }
}
