<?php

namespace App\Console\Commands\MigrationHelper;

use App\Server;
use App\World;
use App\Console\DatabaseUpdate\TableGenerator;
use App\Tool\AttackPlanner\AttackList;
use App\Tool\AttackPlanner\AttackListItem;
use App\Tool\AttackPlanner\AttackListLegacy;
use App\Tool\AttackPlanner\AttackListOwnership;
use App\Util\BasicFunctions;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportFromLastVersion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:importFromLastVersion {oldDatabase}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importiert daten von der alten version nach dem DB-Layout change';

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
        $oldDB = $this->argument('oldDatabase');
        $this->copyTranslations($oldDB);
        $this->copyUsers($oldDB);
        $this->copySessions($oldDB);
        $this->copyRoleUser($oldDB);
        $this->copyBugreports($oldDB);
        $this->copyNews($oldDB);
        $this->copyBugreportComments($oldDB);
        $this->copyServer($oldDB);
        $this->copyWorlds($oldDB);
        $this->copyChangelogs($oldDB);
        $this->copyMaps($oldDB);
        $this->copyProfiles($oldDB);
        $this->copyDS_Connections($oldDB);
        $this->copySignatures($oldDB);
        $this->copyAccMgrDB($oldDB);
        $this->copyWorldStatistics($oldDB);
        $this->copyAnimHistMaps($oldDB);
        $this->copyDiscordBotNotifications($oldDB);
        $this->copySpeedWorlds($oldDB);
        $this->copyCacheStats($oldDB);
        $this->migrateAttackLists($oldDB);
        $this->copyOtherServers($oldDB);
        
        $this->migrateWorlds();
        return 0;
    }
    
    private function copyTranslations($copyFrom) {
        $data = DB::select("SELECT `id`, `status`, `locale`, `group`, `key`, `value`, `created_at`, `updated_at` FROM `$copyFrom`.`ltm_translations`");
        DB::table('ltm_translations')->insert(static::convertInsertData($data));
    }
    
    private function copyUsers($copyFrom) {
        $data = DB::select("SELECT `id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `deleted_at` FROM `$copyFrom`.`users`");
        DB::table('users')->insert(static::convertInsertData($data));
    }
    
    private function copySessions($copyFrom) {
        $data = DB::select("SELECT `id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity` FROM `$copyFrom`.`sessions`");
        DB::table('sessions')->insert(static::convertInsertData($data));
    }
    
    private function copyRoleUser($copyFrom) {
        $data = DB::select("SELECT `user_id`, `role_id` FROM `$copyFrom`.`role_user`");
        DB::table('role_user')->insert(static::convertInsertData($data));
    }
    
    private function copyBugreports($copyFrom) {
        $data = DB::select("SELECT `id`, `name`, `email`, `title`, `priority`, `description`, ".
                "`url`, `status`, `firstSeenUser_id`, `firstSeen`, `delivery`, ".
                "`created_at`, `updated_at`, `deleted_at` FROM `$copyFrom`.`bugreports`");
        DB::table('bugreports')->insert(static::convertInsertData($data));
    }
    
    private function copyNews($copyFrom) {
        $data = DB::select("SELECT `id`, `order`, `content_de`, `content_en`, ".
                "`created_at`, `updated_at`, `deleted_at` FROM `$copyFrom`.`news`");
        DB::table('news')->insert(static::convertInsertData($data));
    }
    
    private function copyBugreportComments($copyFrom) {
        $data = DB::select("SELECT `id`, `bugreport_id`, `user_id`, `content`, ".
                "`created_at`, `updated_at`, `deleted_at` FROM `$copyFrom`.`bugreport_comments`");
        DB::table('bugreport_comments')->insert(static::convertInsertData($data));
    }
    
    private function copyServer($copyFrom) {
        $data = DB::select("SELECT `id`, `code`, `flag`, `url`, `active`, `speed_active`, ".
                "`classic_active`, `created_at`, `updated_at`, `deleted_at` FROM `$copyFrom`.`server`");
        DB::table('server')->insert(static::convertInsertData($data));
    }
    
    private function copyWorlds($copyFrom) {
        $data = DB::select("SELECT `id`, `server_id`, `name`, `ally_count`, `player_count`, `village_count`, ".
                "`url`, `config`, `units`, `buildings`, `active`, `win_condition`, ".
                "`worldCheck_at`, `worldUpdated_at`, `worldCleaned_at`, `worldTop_at`, `display_name`, ".
                "`created_at`, `updated_at`, `deleted_at` FROM `$copyFrom`.`worlds`");
        $arr = [];
        foreach($data as $d) {
            $a = (array) $d;
            $a["hash_ally"] = config('dsUltimate.hash_ally');
            $a["hash_player"] = config('dsUltimate.hash_player');
            $a["hash_village"] = config('dsUltimate.hash_village');
            $arr[] = $a;
        }
        DB::table('worlds')->insert($arr);
    }
    
    private function copyChangelogs($copyFrom) {
        $data = DB::select("SELECT `id`, `version`, `title`, `de`, `en`, `repository_html_url`, ".
                "`icon`, `color`, `buffer`, `created_at`, `updated_at`, `deleted_at` FROM `$copyFrom`.`changelogs`");
        DB::table('changelogs')->insert(static::convertInsertData($data));
    }
    
    private function copyMaps($copyFrom) {
        $data = DB::select("SELECT `id`, `world_id`, `user_id`, `title`, `edit_key`, `show_key`, ".
                "`markers`, `opaque`, `skin`, `layers`, `dimensions`, `defaultColours`, `markerFactor`, ".
                "`continentNumbers`, `autoDimensions`, `drawing_obj`, `drawing_dim`, `shouldUpdate`, ".
                "`cached_at`, `created_at`, `updated_at`, `deleted_at` FROM `$copyFrom`.`map`");
        DB::table('map')->insert(static::convertInsertData($data));
    }
    
    private function copyProfiles($copyFrom) {
        $data = DB::select("SELECT `id`, `user_id`, `discord_id`, `discord_private_channel_id`, ".
                "`github_id`, `google_id`, `last_seen_changelog`, `map_dimensions`, ".
                "`map_defaultColours`, `map_markerFactor`, `conquerHightlight_World`, ".
                "`conquerHightlight_Ally`, `conquerHightlight_Player`, `conquerHightlight_Village`, ".
                "`created_at`, `updated_at` FROM `$copyFrom`.`profiles`");
        DB::table('profiles')->insert(static::convertInsertData($data));
    }
    
    private function copyDS_Connections($copyFrom) {
        $data = DB::select("SELECT `id`, `user_id`, `world_id`, `player_id`, `key`, ".
                "`verified`, `created_at`, `updated_at` FROM `$copyFrom`.`ds_connections`");
        DB::table('ds_connections')->insert(static::convertInsertData($data));
    }
    
    private function copySignatures($copyFrom) {
        $data = DB::select("SELECT `id`, `worlds_id`, `element_id`, `element_type`, ".
                "`cached`, `created_at`, `updated_at` FROM `$copyFrom`.`signature`");
        $arr = [];
        foreach($data as $d) {
            $a = (array) $d;
            $a['world_id'] = $a['worlds_id'];
            unset($a['worlds_id']);
            $arr[] = $a;
        }
        DB::table('signature')->insert($arr);
    }
    
    private function copyAccMgrDB($copyFrom) {
        $data = DB::select("SELECT `id`, `show_key`, `user_id`, `public`, `name`, ".
                "`buildings`, `remove_additional`, `ignore_remove`, `rating`, ".
                "`totalVotes`, `contains_watchtower`, `contains_church`, ".
                "`contains_statue`, `created_at`, `updated_at`, `deleted_at` FROM `$copyFrom`.`accMgrDB_Template`");
        DB::table('accMgrDB_Template')->insert(static::convertInsertData($data));
        
        $data = DB::select("SELECT `id`, `template_id`, `rating`, `user_id`, ".
                "`created_at`, `updated_at` FROM `$copyFrom`.`accMgrDB_Ratings`");
        DB::table('accMgrDB_Ratings')->insert(static::convertInsertData($data));
    }
    
    private function copyWorldStatistics($copyFrom) {
        $data = DB::select("SELECT `id`, `world_id`, `total_player`, `total_ally`, `total_villages`, ".
                "`total_barbarian_village`, `total_conquere`, `daily_conquer`, `daily_ally_changes`, ".
                "`daily_updates`, `created_at`, `updated_at` FROM `$copyFrom`.`world_statistics`");
        $arr = [];
        foreach($data as $d) {
            $a = (array) $d;
            $a['total_conquer'] = $a['total_conquere'];
            unset($a['total_conquere']);
            $arr[] = $a;
        }
        DB::table('world_statistics')->insert($arr);
    }
    
    private function copyAnimHistMaps($copyFrom) {
        $data = DB::select("SELECT `id`, `world_id`, `user_id`, `edit_key`, `show_key`, ".
                "`markers`, `opaque`, `skin`, `layers`, `autoDimensions`, `dimensions`, ".
                "`defaultColours`, `title`, `markerFactor`, `continentNumbers`, ".
                "`showLegend`, `legendSize`, `legendPosition`, ".
                "`created_at`, `updated_at`, `deleted_at` FROM `$copyFrom`.`animHistMapMap`");
        DB::table('animHistMapMap')->insert(static::convertInsertData($data));
        
        $data = DB::select("SELECT `id`, `world_id`, `user_id`, `edit_key`, `show_key`, ".
                "`markers`, `opaque`, `skin`, `layers`, `autoDimensions`, `dimensions`, ".
                "`defaultColours`, `title`, `markerFactor`, `continentNumbers`, ".
                "`showLegend`, `legendSize`, `legendPosition`, ".
                "`finished_at`, `state`, `animHistMapMap_id`, ".
                "`created_at`, `updated_at`, `deleted_at` FROM `$copyFrom`.`animHistMapJob`");
        DB::table('animHistMapJob')->insert(static::convertInsertData($data));
    }
    
    private function copyDiscordBotNotifications($copyFrom) {
        $data = DB::select("SELECT `id`, `notification_data`, `user_id`, ".
                "`created_at`, `updated_at` FROM `$copyFrom`.`discord_bot_notifications`");
        DB::table('discord_bot_notifications')->insert(static::convertInsertData($data));
    }
    
    private function copySpeedWorlds($copyFrom) {
        $data = DB::select("SELECT `id`, `server_id`, `name`, `display_name`, `instance`, ".
                "`planned_start`, `planned_end`, `started`, `world_id`, ".
                "`created_at`, `updated_at`, `worldCheck_at`, `deleted_at` FROM `$copyFrom`.`speed_worlds`");
        DB::table('speed_worlds')->insert(static::convertInsertData($data));
    }
    
    private function copyCacheStats($copyFrom) {
        $data = DB::select("SELECT `id`, `type`, `hits`, `misses`, `date`, ".
                "`created_at`, `updated_at` FROM `$copyFrom`.`cache_stats`");
        DB::table('cache_stats')->insert(static::convertInsertData($data));
    }
    
    private static function convertInsertData($raw) {
        $retArr = [];
        
        foreach($raw as $r) {
            $retArr[] = (array) $r;
        }
        
        return $retArr;
    }
    
    private function migrateAttackLists($copyFrom) {
        $data = DB::select("SELECT `id`, `world_id`, `user_id`, `edit_key`, `show_key`, ".
                "`title`, `uvMode`, ".
                "`created_at`, `updated_at`, `deleted_at` FROM `$copyFrom`.`attack_lists`");
        DB::table('attack_lists')->insert(static::convertInsertData($data));
        
        $data = DB::select("SELECT `id`, `attack_list_id`, `type`, `start_village_id`, `target_village_id`, ".
                "`slowest_unit`, `note`, `send_time`, `arrival_time`, `ms`, `send`, ".
                "`support_boost`, `tribe_skill`, ".
                "`spear`, `sword`, `axe`, `archer`, `spy`, `light`, `marcher`, ".
                "`heavy`, `ram`, `catapult`, `knight`, `snob`, ".
                "`created_at`, `updated_at` FROM `$copyFrom`.`attack_list_items`");
        
        $arr = [];
        foreach($data as $d) {
            $a = (array) $d;
            $a['spear'] = ($a['spear'] == 0)?(null):($a['spear']);
            $a['sword'] = ($a['sword'] == 0)?(null):($a['sword']);
            $a['axe'] = ($a['axe'] == 0)?(null):($a['axe']);
            $a['archer'] = ($a['archer'] == 0)?(null):($a['archer']);
            $a['spy'] = ($a['spy'] == 0)?(null):($a['spy']);
            $a['light'] = ($a['light'] == 0)?(null):($a['light']);
            $a['marcher'] = ($a['marcher'] == 0)?(null):($a['marcher']);
            $a['heavy'] = ($a['heavy'] == 0)?(null):($a['heavy']);
            $a['ram'] = ($a['ram'] == 0)?(null):($a['ram']);
            $a['catapult'] = ($a['catapult'] == 0)?(null):($a['catapult']);
            $a['knight'] = ($a['knight'] == 0)?(null):($a['knight']);
            $a['snob'] = ($a['snob'] == 0)?(null):($a['snob']);
            $arr[] = $a;
        }
        DB::table('attack_list_items')->insert($arr);
    }
    
    private static function copyModelContents($old, $new, $parts) {
        foreach($parts as $p) {
            $new->{$p} = $old->{$p};
        }
    }
    
    private function copyOtherServers($copyFrom) {
        foreach((new Server())->get() as $s) {
            TableGenerator::otherServersTable($s);
            
            $data = DB::select("SELECT `playerID`, `name`, `worlds`, ".
                    "`created_at`, `updated_at` FROM `$copyFrom`.`other_servers_{$s->code}`");
            DB::table("other_servers_{$s->code}")->insert(static::convertInsertData($data));
        }
        
    }
    
    private function migrateWorlds() {
        foreach((new World())->get() as $model) {
            echo "Doing {$model->serName()}\n";
            BasicFunctions::getWorldDataTable($model, 'conquer');
            Schema::table(BasicFunctions::getWorldDataTable($model, 'conquer'), function (Blueprint $table) {
                $table->integer('points')->default(-1);
            });
        }
        
    }
}
