<?php

namespace App\Console\Commands\MigrationHelper;

use App\World;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExportWorld extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:exportWorld {world} {directory}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exports a single world and all it\'s data';

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
        $worldStr = $this->argument('world');
        $exportDir = $this->argument('directory');
        $worldModel = World::getAndCheckWorld(substr($worldStr, 0, 2), substr($worldStr, 2));
        if($worldModel->deleted_at != null) {
            return 1;
        }
        static::generateWorldExport($exportDir, $worldModel);
        return 0;
    }
    
    public static function generateWorldExport($exportDir, World $worldModel) {
        if(!is_dir($exportDir)) {
            mkdir($exportDir, recursive: true);
        }
        static::exportConfig($exportDir . "/world.cfg", $worldModel);
        static::exportData($exportDir, $worldModel);
        $phar = new \PharData($exportDir . '.tar');
        $phar->buildFromDirectory($exportDir);
        static::delTree($exportDir);
    }

    private static function exportConfig($fName, World $worldModel) {
        $file = fopen($fName, "w");
        $wData = $worldModel->server->code;
        $wData.= "|" . $worldModel->name;
        $wData.= "|" . $worldModel->ally_count;
        $wData.= "|" . $worldModel->player_count;
        $wData.= "|" . $worldModel->village_count;
        $wData.= "|" . $worldModel->url;
        $wData.= "|" . base64_encode($worldModel->config);
        $wData.= "|" . base64_encode($worldModel->units);
        $wData.= "|" . base64_encode($worldModel->buildings);
        $wData.= "|" . $worldModel->worldCheck_at;
        $wData.= "|" . $worldModel->worldCleaned_at;
        $wData.= "|" . $worldModel->worldTop_at;
        $wData.= "|" . base64_encode($worldModel->display_name);
        $wData.= "|" . $worldModel->hash_ally;
        $wData.= "|" . $worldModel->hash_player;
        $wData.= "|" . $worldModel->hash_village;
        $wData.= "|" . ($worldModel->active ?? "null");
        $wData.= "\n";
        fwrite($file, $wData);
        fclose($file);
    }

    private static function exportData($dirName, World $worldModel) {
        static::exportAllyHistory($dirName . "/ally_history.csv.gz", $worldModel);
        static::exportAllyChanges($dirName . "/ally_changes.csv.gz", $worldModel);
        static::exportCurrentAlly($dirName . "/ally_latest.csv.gz", $worldModel);
        static::exportAllyTopValues($dirName . "/ally_top.csv.gz", $worldModel);
        static::exportConquers($dirName . "/conquers.csv.gz", $worldModel);
        static::exportWorldHistory($dirName . "/worldHistory", $worldModel);
        static::exportPlayerHistory($dirName . "/player_history.csv.gz", $worldModel);
        static::exportCurrentPlayer($dirName . "/player_latest.csv.gz", $worldModel);
        static::exportPlayerTopValues($dirName . "/player_top.csv.gz", $worldModel);
        static::exportVillageHistory($dirName . "/village_history.csv.gz", $worldModel);
        static::exportCurrentVillage($dirName . "/village_latest.csv.gz", $worldModel);
    }

    private static function exportAllyHistory($fName, World $worldModel) {
        $tables = [];
        for($i = 0; $i < $worldModel->hash_ally; $i++) {
            $tables[] = "ally_$i";
        }
        static::exportGeneric($fName, $worldModel, $tables, [
            ["allyID", false],
            ["name", false],
            ["tag", false],
            ["member_count", false],
            ["points", false],
            ["village_count", false],
            ["rank", false],
            ["offBash", false],
            ["offBashRank", false],
            ["defBash", false],
            ["defBashRank", false],
            ["gesBash", false],
            ["gesBashRank", false],
            ["created_at", true],
            ["updated_at", true],
        ]);
    }

    private static function exportAllyChanges($fName, World $worldModel) {
        static::exportGeneric($fName, $worldModel, ["ally_changes"], [
            ["player_id", false],
            ["old_ally_id", false],
            ["new_ally_id", false],
            ["points", false],
            ["created_at", true],
            ["updated_at", true],
        ]);
    }

    private static function exportCurrentAlly($fName, World $worldModel) {
        static::exportGeneric($fName, $worldModel, ["ally_latest"], [
            ["allyID", false],
            ["name", false],
            ["tag", false],
            ["member_count", false],
            ["points", false],
            ["village_count", false],
            ["rank", false],
            ["offBash", false],
            ["offBashRank", false],
            ["defBash", false],
            ["defBashRank", false],
            ["gesBash", false],
            ["gesBashRank", false],
            ["created_at", true],
            ["updated_at", true],
        ]);
    }

    private static function exportAllyTopValues($fName, World $worldModel) {
        static::exportGeneric($fName, $worldModel, ["ally_top"], [
            ["allyID", false],
            ["name", false],
            ["tag", false],
            ["member_count_top", false],
            ["member_count_date", true],
            ["village_count_top", false],
            ["village_count_date", true],
            ["points_top", false],
            ["points_date", true],
            ["rank_top", false],
            ["rank_date", true],
            ["offBash_top", false],
            ["offBash_date", true],
            ["offBashRank_top", false],
            ["offBashRank_date", true],
            ["defBash_top", false],
            ["defBash_date", true],
            ["defBashRank_top", false],
            ["defBashRank_date", true],
            ["gesBash_top", false],
            ["gesBash_date", true],
            ["gesBashRank_top", false],
            ["gesBashRank_date", true],
            ["created_at", true],
            ["updated_at", true],
        ]);
    }

    private static function exportConquers($fName, World $worldModel) {
        static::exportGeneric($fName, $worldModel, ["conquer"], [
            ["village_id", false],
            ["timestamp", false],
            ["new_owner", false],
            ["old_owner", false],
            ["id", false],
            ["old_owner_name", false],
            ["new_owner_name", false],
            ["old_ally", false],
            ["new_ally", false],
            ["old_ally_name", false],
            ["new_ally_name", false],
            ["old_ally_tag", false],
            ["new_ally_tag", false],
            ["points", false],
            ["created_at", true],
            ["updated_at", true],
        ]);
    }

    private static function exportWorldHistory($fName, World $worldModel) {
        if(!is_dir($fName)) {
            mkdir($fName, recursive: true);
        }
        static::exportGeneric($fName . "/index.csv", $worldModel, ["index"], [
            ["id", false],
            ["date", false],
            ["created_at", true],
            ["updated_at", true],
        ]);

        $tblName = BasicFunctions::getWorldDataTable($worldModel, "index");
        $data = DB::select("SELECT * FROM $tblName");
        $fromBase = storage_path(config('dsUltimate.history_directory') . $worldModel->serName() . "/");
        foreach($data as $d) {
            $datFName = "village_{$d->date}.gz";
            copy($fromBase . $datFName, $fName . "/" . $datFName);
            $datFName = "player_{$d->date}.gz";
            copy($fromBase . $datFName, $fName . "/" . $datFName);
            $datFName = "ally_{$d->date}.gz";
            copy($fromBase . $datFName, $fName . "/" . $datFName);
        }
    }

    private static function exportPlayerHistory($fName, World $worldModel) {
        $tables = [];
        for($i = 0; $i < $worldModel->hash_player; $i++) {
            $tables[] = "player_$i";
        }
        static::exportGeneric($fName, $worldModel, $tables, [
            ["playerID", false],
            ["name", false],
            ["ally_id", false],
            ["village_count", false],
            ["points", false],
            ["rank", false],
            ["offBash", false],
            ["offBashRank", false],
            ["defBash", false],
            ["defBashRank", false],
            ["supBash", false],
            ["supBashRank", false],
            ["gesBash", false],
            ["gesBashRank", false],
            ["created_at", true],
            ["updated_at", true],
        ]);
    }

    private static function exportCurrentPlayer($fName, World $worldModel) {
        static::exportGeneric($fName, $worldModel, ["player_latest"], [
            ["playerID", false],
            ["name", false],
            ["ally_id", false],
            ["village_count", false],
            ["points", false],
            ["rank", false],
            ["offBash", false],
            ["offBashRank", false],
            ["defBash", false],
            ["defBashRank", false],
            ["supBash", false],
            ["supBashRank", false],
            ["gesBash", false],
            ["gesBashRank", false],
            ["created_at", true],
            ["updated_at", true],
        ]);
    }

    private static function exportPlayerTopValues($fName, World $worldModel) {
        static::exportGeneric($fName, $worldModel, ["player_top"], [
            ["playerID", false],
            ["name", false],
            ["village_count_top", false],
            ["village_count_date", true],
            ["points_top", false],
            ["points_date", true],
            ["rank_top", false],
            ["rank_date", true],
            ["offBash_top", false],
            ["offBash_date", true],
            ["offBashRank_top", false],
            ["offBashRank_date", true],
            ["defBash_top", false],
            ["defBash_date", true],
            ["defBashRank_top", false],
            ["defBashRank_date", true],
            ["supBash_top", false],
            ["supBash_date", true],
            ["supBashRank_top", false],
            ["supBashRank_date", true],
            ["gesBash_top", false],
            ["gesBash_date", true],
            ["gesBashRank_top", false],
            ["gesBashRank_date", true],
            ["created_at", true],
            ["updated_at", true],
        ]);
    }

    private static function exportVillageHistory($fName, World $worldModel) {
        $tables = [];
        for($i = 0; $i < $worldModel->hash_village; $i++) {
            $tables[] = "village_$i";
        }
        static::exportGeneric($fName, $worldModel, $tables, [
            ["villageID", false],
            ["name", false],
            ["x", false],
            ["y", false],
            ["points", false],
            ["owner", false],
            ["bonus_id", false],
            ["created_at", true],
            ["updated_at", true],
        ]);
    }

    private static function exportCurrentVillage($fName, World $worldModel) {
        static::exportGeneric($fName, $worldModel, ["village_latest"], [
            ["villageID", false],
            ["name", false],
            ["x", false],
            ["y", false],
            ["points", false],
            ["owner", false],
            ["bonus_id", false],
            ["created_at", true],
            ["updated_at", true],
        ]);
    }

    private static function exportGeneric($fName, World $worldModel, $tables, $fields) {
        $file = gzopen($fName, "w");
        $field_names = [];
        foreach($fields as $f) {
            $field_names[] = $f[0];
        }
        gzwrite($file, implode(",", $field_names) . "\n");
        foreach($tables as $tbl) {
            if(!BasicFunctions::hasWorldDataTable($worldModel, $tbl)) {
                continue;
            }

            $tblName = BasicFunctions::getWorldDataTable($worldModel, $tbl);
            $data = DB::select("SELECT * FROM $tblName");
            foreach($data as $d) {
                $fieldData = [];
                foreach($fields as $f) {
                    $tmp = $d->{$f[0]};
                    if($f[1]) {
                        $tmp = Carbon::parse($tmp)->timestamp;
                    }
                    $fieldData[] = $tmp;
                }
                fwrite($file, implode(",", $fieldData) . "\n");
            }
        }
        gzclose($file);
    }
    
    private static function delTree($dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            if(is_dir("$dir/$file")){
                static::delTree("$dir/$file");
            } else {
                unlink("$dir/$file");
            }
        }
        return rmdir($dir);
    }
}
