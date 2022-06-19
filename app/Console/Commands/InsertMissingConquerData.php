<?php

namespace App\Console\Commands;

use App\Conquer;
use App\World;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Console\Command;

class InsertMissingConquerData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:insertMissingConquer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Füllt null werte in der Conquer DB';

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
        $worlds = (new World())->get();
        foreach ($worlds as $world){
            $this->insertMissing($world);
        }
        
        return 0;
    }
    
    private function insertMissing(World $worldModel) {
//        todo here points
//        
//        todo general:
//        -> migration for changing the hash size
//        -> migration for moving to shared db
//        -> all aborts should use translations
//        -> translated world displayNames
//        -> Move to self hosted captcha
//        -> use $dayDelta in Player::playerDataChart(World $world, $playerID, $dayDelta = 30)
//        -> disallow access via ds-ultimate.de/index.php/...
//        -> User table email Nullable
//        -> Remove possibility to change roles / permissions in admin interface
//            ['id'=>9, 'title'=>'permission_create'],
//            ['id'=>10, 'title'=>'permission_edit'],
//            ['id'=>11, 'title'=>'permission_show'],
//            ['id'=>12, 'title'=>'permission_delete'],
//            ['id'=>14, 'title'=>'role_edit'],
//            ['id'=>15, 'title'=>'role_create'],
//            ['id'=>16, 'title'=>'role_edit'],
//            ['id'=>18, 'title'=>'role_delete'],
//            ['id'=>21, 'title'=>'world_access'],
//        -> remove discord ifreame from default page (maybe after click?)
//        
//        Update guide:
//        -> dsphp artisan down --render="errors::503" --secret={random string}
//        -< git pull
//        -> remove dsphp artisan up from deploy script
//        -> Move all tables out of the way (all global tables!!)
//        -> run ./deploy.sh (runs migraions and builds initial db)
//        -> Run dsphp artisan migrate:importFromLastVersion {old DB}
//        -> go to https://ds-ultimate.de/{secret from before}
//        -> Check that user permissions are correct
//        -> basic function check
//        -> dsphp artisan up
//        -> Run dsphp artisan migrate:insertMissingConquer  // this will fill entries with points=0
//        
        if(! BasicFunctions::hasWorldDataTable($worldModel, 'conquer')) {
            return;
        }
        
        $conquerModel = new Conquer($worldModel);
        
        $todo = $conquerModel->whereNull('old_owner_name')
                ->orWhereNull('new_owner_name')
                ->orWhereNull('old_ally')
                ->orWhereNull('new_ally')
                ->orWhereNull('old_ally_name')
                ->orWhereNull('new_ally_name')
                ->orWhereNull('old_ally_tag')
                ->orWhereNull('new_ally_tag')
                ->get();
        
        foreach($todo as $con) {
            if(Carbon::now()->timestamp - $con->timestamp < 60 * 60 * 2) continue;
            $old = $con->oldPlayer;
            $new = $con->newPlayer;
            $tempArr = array();
            
            if($con->old_owner == 0 || $old == null) {
                $tempArr['old_owner_name'] = "";
                $tempArr['old_ally'] = 0;
                $tempArr['old_ally_name'] = "";
                $tempArr['old_ally_tag'] = "";
            } else {
                $tempArr['old_owner_name'] = $old->name;
                $tempArr['old_ally'] = $old->ally_id;
                $tempArr['old_ally_name'] = ($old->allyLatest != null)?$old->allyLatest->name:"";
                $tempArr['old_ally_tag'] = ($old->allyLatest != null)?$old->allyLatest->tag:"";
            }
            
            if($con->new_owner == 0 || $new == null) {
                $tempArr['new_owner_name'] = "";
                $tempArr['new_ally'] = 0;
                $tempArr['new_ally_name'] = "";
                $tempArr['new_ally_tag'] = "";
            } else {
                $tempArr['new_owner_name'] = $new->name;
                $tempArr['new_ally'] = $new->ally_id;
                $tempArr['new_ally_name'] = ($new->allyLatest != null)?$new->allyLatest->name:"";
                $tempArr['new_ally_tag'] = ($new->allyLatest != null)?$new->allyLatest->tag:"";
            }
            
            if($con->old_owner_name == null) $con->old_owner_name = $tempArr['old_owner_name'];
            if($con->old_ally == null) $con->old_ally = $tempArr['old_ally'];
            if($con->old_ally_name == null) $con->old_ally_name = $tempArr['old_ally_name'];
            if($con->old_ally_tag == null) $con->old_ally_tag = $tempArr['old_ally_tag'];
            
            if($con->new_owner_name == null) $con->new_owner_name = $tempArr['new_owner_name'];
            if($con->new_ally == null) $con->new_ally = $tempArr['new_ally'];
            if($con->new_ally_name == null) $con->new_ally_name = $tempArr['new_ally_name'];
            if($con->new_ally_tag == null) $con->new_ally_tag = $tempArr['new_ally_tag'];
            $con->save();
        }
    }
}
