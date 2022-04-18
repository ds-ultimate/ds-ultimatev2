<?php

namespace App\Console\Commands\MigrationHelper;

use App\Tool\AttackPlanner\AttackListItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixAttackListItemRuntime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:attackListItemRuntime';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes attacks where send and arrive time do not match';

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
        foreach((new AttackListItem())->get() as $item) {
            if($item->list == null) continue; //list has been deleted using soft deletes
            echo $item->id . "/" . $item->send_time . "/";
            $item->send_time = $item->calcSend();
            echo $item->send_time . "\n";
            if($item->send_time != null) {
                $item->save();
            }
        }
        return 0;
    }
    
}
