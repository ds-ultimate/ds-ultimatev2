<?php

namespace App\Console\Commands\Tools;

use App\Tool\AttackPlanner\AttackList;
use App\Tool\AttackPlanner\AttackListItem;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteOldAttackPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:unusedAttackPlans {param=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Löscht unbenutzte Angriffspläne';

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
        $param = $this->argument('param');
        static::delete((new AttackList())->onlyTrashed(), $param);
        static::delete((new AttackList())->where('updated_at', '<', Carbon::now()->subMonth(6))->whereNull('user_id'), $param);
        static::delete((new AttackList())->where('updated_at', '<', Carbon::now()->subDays(7))->where('api', 1), $param);
        return 0;
    }
    
    private static function delete($models, $param) {
        foreach($models->get() as $m) {
            if($param == "v" || $param == "d" || $param == "dry" || $param == "dryRun") {
                echo $m->id . "\n";
            }
            if($param != "d" && $param != "dry" && $param != "dryRun") {
                (new AttackListItem())->where('attack_list_id', $m->id)->forceDelete();
                $m->forceDelete();
            }
        }
    }
}
