<?php

namespace App\Console\Commands\MigrationHelper;

use App\Server;
use App\World;
use Illuminate\Console\Command;

class MassDeleteWorlds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:massDeleteWorlds {server} {name_expression}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes multiple worlds and all their data';

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
        $serverStr = $this->argument('server');
        $worldLike = $this->argument('name_expression');
        $serverModel = Server::getAndCheckServerByCode($serverStr);
        $worldModels = (new World())->where("server_id", $serverModel->id)->where("name", "LIKE", $worldLike)->get();
        static::removeOtherworldReferences($worldModels, true);
        foreach($worldModels as $wModel) {
            if($wModel->deleted_at != null) {
                return 1;
            }
            DeleteWorld::deleteWorld($wModel);
        }
        return 0;
    }
}
