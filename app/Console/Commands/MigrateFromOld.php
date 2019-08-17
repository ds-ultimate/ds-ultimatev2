<?php

namespace App\Console\Commands;

use App\Util\BasicFunctions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MigrateFromOld extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:database {dbName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aktualisiert die Datenbank einer Welt auf das neue Format';

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
        BasicFunctions::ignoreErrs();
        
        $hashes = [
            'ally' => config('dsUltimate.hash_ally'),
            'player' => config('dsUltimate.hash_player'),
            'village' => config('dsUltimate.hash_village'),
        ];
        
        $databases = DB::select("SELECT schema_name FROM information_schema.schemata WHERE schema_name LIKE '".$this->argument('dbName')."'");
        
        $perTable = 4;
        $perDatabase = ($hashes['ally'] + $hashes['player'] + $hashes['village'] + 4) * $perTable + 1;
        $bar = $this->output->createProgressBar( $perDatabase * count($databases) );
        $bar->start();
        $this->bar = $bar;

        $baseProgress = 0;
        foreach ($databases as $database) {
            $this->bar->setProgress($baseProgress);
            $this->updateDatabase($database->schema_name, $baseProgress, $perTable, $hashes);
            $baseProgress += $perDatabase;
        }
        
        $bar->finish();
    }
    
    private function updateDatabase($databaseName, $baseProgress, $perTable, $hashes) {
        for ($i = 0; $i < $hashes['ally']; $i++) {
            $this->bar->setProgress($baseProgress);
            $this->updateAllyTable($databaseName.'.'.'ally_'.$i);
            $baseProgress += $perTable;
        }
        for ($i = 0; $i < $hashes['player']; $i++) {
            $this->bar->setProgress($baseProgress);
            $this->updatePlayerTable($databaseName.'.'.'player_'.$i);
            $baseProgress += $perTable;
        }
        for ($i = 0; $i < $hashes['village']; $i++) {
            $this->bar->setProgress($baseProgress);
            $this->updateVillageTable($databaseName.'.'.'village_'.$i);
            $baseProgress += $perTable;
        }
        
        $this->updateAllyLatest($databaseName.'.'.'ally_latest');
        $this->updatePlayerLatest($databaseName.'.'.'player_latest');
        $this->updateVillageLatest($databaseName.'.'.'village_latest');
        $this->doAccess("ALTER TABLE $databaseName.conquers RENAME TO $databaseName.conquer");
        $this->updateConquerTable($databaseName.'.'.'conquer');
        \App\Http\Controllers\DBController::allyChangeTable($databaseName);
        $this->bar->advance();
    }
    
    private function updateAllyTable($tableName) {
        /**
         * //remove index from allyID
         * name -> varchar(191)
         * tag -> varchar(191)
         * offBash -> bigint(20)
         * defBash -> bigint(20)
         * gesBash -> bigint(20)
         * - timestamp int(11)
         * + created_at timestamp
         * + updated_at timestamp
         */
        
        //alter existing
        $this->doAccess("ALTER TABLE $tableName "
                . "CHANGE `name` `name` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, "
                . "CHANGE `tag` `tag` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, "
                . "CHANGE `offBash` `offBash` BIGINT(20) NULL DEFAULT NULL AFTER `rank`, "
                . "CHANGE `offBashRank` `offBashRank` INT(11) NULL DEFAULT NULL AFTER `offBash`, "
                . "CHANGE `deffBash` `defBash` BIGINT(20) NULL DEFAULT NULL AFTER `offBashRank`, "
                . "CHANGE `deffBashRank` `defBashRank` INT(11) NULL DEFAULT NULL AFTER `defBash`, "
                . "CHANGE `gesBash` `gesBash` BIGINT(20) NULL DEFAULT NULL AFTER `defBashRank`, "
                . "CHANGE `gesBashRank` `gesBashRank` INT(11) NULL DEFAULT NULL AFTER `gesBash`, "
                . "DROP INDEX `allyID`;");
        $this->bar->advance();
        
        //add new coloums
        $this->doAccess("ALTER TABLE $tableName "
                . "ADD `created_at` timestamp NULL DEFAULT NULL AFTER `gesBashRank`, "
                . "ADD `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`;");
        $this->bar->advance();
        
        $this->performTimestampMigration($tableName);
        
        //remove unused rows
        $this->doAccess("ALTER TABLE $tableName "
                . " DROP `timestamp`;");
        $this->bar->advance();
    }
    
    private function updatePlayerTable($tableName) {
        //alter existing
        $this->doAccess("ALTER TABLE $tableName "
                . "CHANGE `name` `name` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, "
                . "CHANGE `ally_id` `ally_id` INT(11) NOT NULL AFTER `name`, "
                . "CHANGE `village_count` `village_count` INT(11) NOT NULL AFTER `ally_id`, "
                . "CHANGE `points` `points` INT(11) NOT NULL AFTER `village_count`, "
                . "CHANGE `rank` `rank` INT(11) NOT NULL AFTER `points`, "
                . "CHANGE `offBash` `offBash` BIGINT(20) NULL DEFAULT NULL AFTER `rank`, "
                . "CHANGE `offBashRank` `offBashRank` INT(11) NULL DEFAULT NULL AFTER `offBash`, "
                . "CHANGE `deffBash` `defBash` BIGINT(20) NULL DEFAULT NULL AFTER `offBashRank`, "
                . "CHANGE `deffBashRank` `defBashRank` INT(11) NULL DEFAULT NULL AFTER `defBash`, "
                . "CHANGE `gesBash` `gesBash` BIGINT(20) NULL DEFAULT NULL AFTER `defBashRank`, "
                . "CHANGE `gesBashRank` `gesBashRank` INT(11) NULL DEFAULT NULL AFTER `gesBash`, "
                . "DROP INDEX `playerID`;");
        $this->bar->advance();
        
        //add new coloums
        $this->doAccess("ALTER TABLE $tableName "
                . "ADD `created_at` timestamp NULL DEFAULT NULL AFTER `gesBashRank`, "
                . "ADD `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`;");
        $this->bar->advance();
        
        $this->performTimestampMigration($tableName);
        
        //remove unused rows
        $this->doAccess("ALTER TABLE $tableName "
                . " DROP `timestamp`;");
        $this->bar->advance();
    }
    
    private function updateVillageTable($tableName) {
        //alter existing
        $this->doAccess("ALTER TABLE $tableName "
                . "CHANGE `name` `name` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, "
                . "DROP INDEX `villageID`;");
        $this->bar->advance();
        
        //add new coloums
        $this->doAccess("ALTER TABLE $tableName "
                . "ADD `created_at` timestamp NULL DEFAULT NULL AFTER `bonus_id`, "
                . "ADD `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`;");
        $this->bar->advance();
        
        $this->performTimestampMigration($tableName);
        
        //remove unused rows
        $this->doAccess("ALTER TABLE $tableName "
                . " DROP `timestamp`;");
        $this->bar->advance();
    }
    
    private function updateConquerTable($tableName) {
        //alter existing
        $this->doAccess("ALTER TABLE $tableName "
                . "CHANGE `dorf_id` `village_id` INT(11) NULL DEFAULT NULL, "
                . "CHANGE `timestamp` `timestamp` BIGINT(20) NULL DEFAULT NULL AFTER `village_id`, "
                . "CHANGE `new_owner_id` `new_owner` INT(11) NULL DEFAULT NULL AFTER `timestamp`, "
                . "CHANGE `old_owner_id` `old_owner` INT(11) NULL DEFAULT NULL AFTER `new_owner`, "
                . "DROP PRIMARY KEY, "
                . "DROP `id`, "
                . "DROP `points`;");
        $this->bar->advance();
        
        //add new coloums
        $this->doAccess("ALTER TABLE $tableName "
                . "ADD `created_at` timestamp NULL DEFAULT NULL AFTER `old_owner`, "
                . "ADD `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`;");
        $this->bar->advance();
        
        $this->performTimestampMigration($tableName);
        $this->bar->advance();
    }
    
    private function performTimestampMigration($tableName) {
        $this->doAccess("UPDATE $tableName SET `created_at`=FROM_UNIXTIME(`timestamp`),`updated_at`=FROM_UNIXTIME(`timestamp`) WHERE 1;");
        $this->bar->advance();
    }
    
    private function updateAllyLatest($tableName) {
        //alter existing
        $this->doAccess("ALTER TABLE $tableName "
                . "CHANGE `name` `name` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, "
                . "CHANGE `tag` `tag` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, "
                . "CHANGE `offBash` `offBash` BIGINT(20) NULL DEFAULT NULL AFTER `rank`, "
                . "CHANGE `offBashRank` `offBashRank` INT(11) NULL DEFAULT NULL AFTER `offBash`, "
                . "CHANGE `deffBash` `defBash` BIGINT(20) NULL DEFAULT NULL AFTER `offBashRank`, "
                . "CHANGE `deffBashRank` `defBashRank` INT(11) NULL DEFAULT NULL AFTER `defBash`, "
                . "CHANGE `gesBash` `gesBash` BIGINT(20) NULL DEFAULT NULL AFTER `defBashRank`, "
                . "CHANGE `gesBashRank` `gesBashRank` INT(11) NULL DEFAULT NULL AFTER `gesBash`, "
                . "DROP PRIMARY KEY;");
        $this->bar->advance();
        
        //add new coloums
        $this->doAccess("ALTER TABLE $tableName "
                . "ADD `created_at` timestamp NULL DEFAULT NULL AFTER `gesBashRank`, "
                . "ADD `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`;");
        $this->bar->advance();
        
        $this->fillTimestamps($tableName);
        $this->bar->advance();
    }
    
    private function updatePlayerLatest($tableName) {
        //alter existing
        $this->doAccess("ALTER TABLE $tableName "
                . "CHANGE `name` `name` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, "
                . "CHANGE `ally_id` `ally_id` INT(11) NOT NULL AFTER `name`, "
                . "CHANGE `village_count` `village_count` INT(11) NOT NULL AFTER `ally_id`, "
                . "CHANGE `points` `points` INT(11) NOT NULL AFTER `village_count`, "
                . "CHANGE `rank` `rank` INT(11) NOT NULL AFTER `points`, "
                . "CHANGE `offBash` `offBash` BIGINT(20) NULL DEFAULT NULL AFTER `rank`, "
                . "CHANGE `offBashRank` `offBashRank` INT(11) NULL DEFAULT NULL AFTER `offBash`, "
                . "CHANGE `deffBash` `defBash` BIGINT(20) NULL DEFAULT NULL AFTER `offBashRank`, "
                . "CHANGE `deffBashRank` `defBashRank` INT(11) NULL DEFAULT NULL AFTER `defBash`, "
                . "CHANGE `gesBash` `gesBash` BIGINT(20) NULL DEFAULT NULL AFTER `defBashRank`, "
                . "CHANGE `gesBashRank` `gesBashRank` INT(11) NULL DEFAULT NULL AFTER `gesBash`, "
                . "DROP PRIMARY KEY;");
        $this->bar->advance();
        
        //add new coloums
        $this->doAccess("ALTER TABLE $tableName "
                . "ADD `created_at` timestamp NULL DEFAULT NULL AFTER `gesBashRank`, "
                . "ADD `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`;");
        $this->bar->advance();
        
        $this->fillTimestamps($tableName);
        $this->bar->advance();
    }
    
    private function updateVillageLatest($tableName) {
        //alter existing
        $this->doAccess("ALTER TABLE $tableName "
                . "CHANGE `name` `name` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, "
                . "DROP PRIMARY KEY;");
        $this->bar->advance();
        
        //add new coloums
        $this->doAccess("ALTER TABLE $tableName "
                . "ADD `created_at` timestamp NULL DEFAULT NULL AFTER `bonus_id`, "
                . "ADD `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`;");
        $this->bar->advance();
        
        $this->fillTimestamps($tableName);
        $this->bar->advance();
    }
    
    private function fillTimestamps($tableName) {
        $carbon = Carbon::createFromTimestamp(time());
        $this->doAccess("UPDATE $tableName SET `created_at`='$carbon',`updated_at`='$carbon' WHERE 1;");
        $this->bar->advance();
    }
    
    private function doAccess($sql) {
        DB::statement($sql);
        //fwrite($this->file, $sql . "\n;");
    }
}
