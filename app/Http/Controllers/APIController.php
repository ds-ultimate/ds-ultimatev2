<?php

namespace App\Http\Controllers;

use App\Ally;
use App\World;
use App\Player;
use App\Server;
use App\Village;
use App\Conquer;
use App\AllyChanges;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Resources\World as WorldResource;
use App\Http\Resources\Village as VillageResource;

class APIController extends Controller
{
    public function getPlayers($server, $world)
    {
        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_latest');

        $datas = $playerModel->newQuery();

        return DataTables::eloquent($datas)
            ->editColumn('name', function ($player){
                return BasicFunctions::decodeName($player->name);
            })
            ->addColumn('ally', function ($player){
                return ($player->ally_id != 0)? BasicFunctions::decodeName($player->allyLatest->tag) : '-';
            })
            ->addColumn('village_points', function ($player){
                return ($player->points == 0 || $player->village_count == 0)? 0 : ($player->points/$player->village_count);
            })
            ->addColumn('utBash', function ($player){
                return $player->gesBash - $player->offBash - $player->defBash;
            })
            ->toJson();
    }

    public function getAllys($server, $world)
    {
        $allyModel = new Ally();
        $allyModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.ally_latest');

        $datas = $allyModel->newQuery();

        return DataTables::eloquent($datas)
            ->editColumn('name', function ($ally){
                return BasicFunctions::decodeName($ally->name);
            })
            ->editColumn('tag', function ($ally){
                return BasicFunctions::decodeName($ally->tag);
            })
            ->addColumn('player_points', function ($ally){
                return ($ally->points == 0 || $ally->member_count == 0)? 0 : ($ally->points/$ally->member_count);
            })
            ->addColumn('village_points', function ($ally){
                return ($ally->points == 0 || $ally->village_count == 0)? 0 : ($ally->points/$ally->village_count);
            })
            ->toJson();
    }

    public function getAllyPlayer($server, $world, $ally)
    {
        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_latest');

        $querry = $playerModel->newQuery();
        $querry->where('ally_id', $ally);

        return DataTables::eloquent($querry)
            ->editColumn('name', function ($player){
                return BasicFunctions::decodeName($player->name);
            })
            ->addColumn('ally', function ($player){
                return ($player->ally_id != 0)? BasicFunctions::decodeName($player->allyLatest->tag) : '-';
            })
            ->addColumn('village_points', function ($player){
                return ($player->points == 0 || $player->village_count == 0)? 0 : ($player->points/$player->village_count);
            })
            ->addColumn('utBash', function ($player){
                return $player->gesBash - $player->offBash - $player->defBash;
            })
            ->toJson();
    }

    public function getPlayerVillage($server, $world, $player)
    {
        $villageModel = new Village();
        $villageModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.village_latest');

        $query = $villageModel->newQuery();
        $query->where('owner', $player);

        return DataTables::eloquent($query)
            ->editColumn('name', function ($village){
                return BasicFunctions::decodeName($village->name);
            })
            ->addColumn('player', function ($village){
                return ($village->owner != 0)? BasicFunctions::decodeName($village->playerLatest->name) : '-';
            })
            ->addColumn('continent', function ($village){
                return $village->continentString();
            })
            ->addColumn('coordinates', function ($village){
                return $village->coordinates();
            })
            ->addColumn('bonus', function ($village){
                return $village->bonusText();
            })
            ->toJson();
    }

    public function getAllyAllyChanges($server, $world, $type, $allyID)
    {
        $allyChangesModel = new AllyChanges();
        $allyChangesModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.ally_changes');

        $query = $allyChangesModel->newQuery();
        
        switch($type) {
            case "all":
                $query->where('new_ally_id', $allyID)->orWhere('old_ally_id', $allyID);
                break;
            case "old":
                $query->where('old_ally_id', $allyID);
                break;
            case "new":
                $query->where('new_ally_id', $allyID);
                break;
            default:
                // FIXME: create error view
                return "Unknown type";
        }

        return $this->doAllyChangeReturn($query);
    }

    public function getPlayerAllyChanges($server, $world, $type, $playerID)
    {
        $allyChangesModel = new AllyChanges();
        $allyChangesModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.ally_changes');

        $query = $allyChangesModel->newQuery();

        switch($type) {
            case "all":
                $query->where('player_id', $playerID);
                break;
            default:
                // FIXME: create error view
                return "Unknown type";
        }
        
        return $this->doAllyChangeReturn($query);
    }
    
    private function doAllyChangeReturn($query) {
        return DataTables::eloquent($query)
            ->addColumn('player_name', function ($allyChange){
                return ($allyChange->player_id != 0)? BasicFunctions::decodeName($allyChange->player->name) : '-';
            })
            ->addColumn('old_ally_name', function ($allyChange){
                if($allyChange->old_ally_id == 0) return ucfirst(__('ui.ally.noAlly'));
                if($allyChange->oldAlly == null) return ucfirst(__('ui.ally.deleted'));
                return BasicFunctions::decodeName($allyChange->oldAlly->name);
            })
            ->addColumn('new_ally_name', function ($allyChange){
                if($allyChange->new_ally_id == 0) return ucfirst(__('ui.ally.noAlly'));
                if($allyChange->newAlly == null) return ucfirst(__('ui.ally.deleted'));
                return BasicFunctions::decodeName($allyChange->newAlly->name);
            })
            ->toJson();
    }

    public function getAllyConquer($server, $world, $type, $allyID)
    {
        $conquerModel = new Conquer();
        $conquerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.conquer');

        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_latest');

        $allyPlayers = array();
        foreach ($playerModel->newQuery()->where('ally_id', $allyID)->get() as $player) {
            $allyPlayers[] = $player->playerID;
        }
        
        $query = $conquerModel->newQuery();
        
        switch($type) {
            case "all":
                $query->whereIn('new_owner', $allyPlayers)->orWhereIn('old_owner', $allyPlayers);
                break;
            case "old":
                $query->whereIn('old_owner', $allyPlayers);
                break;
            case "new":
                $query->whereIn('new_owner', $allyPlayers);
                break;
            default:
                // FIXME: create error view
                return "Unknown type";
        }

        return $this->doConquerReturn($query);
    }

    public function getPlayerConquer($server, $world, $type, $playerID)
    {
        $conquerModel = new Conquer();
        $conquerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.conquer');

        $query = $conquerModel->newQuery();

        switch($type) {
            case "all":
                $query->where('new_owner', $playerID)->orWhere('old_owner', $playerID);
                break;
            case "old":
                $query->where('old_owner', $playerID);
                break;
            case "new":
                $query->where('new_owner', $playerID);
                break;
            default:
                // FIXME: create error view
                return "Unknown type";
        }
        
        return $this->doConquerReturn($query);
    }

    public function getVillageConquer($server, $world, $type, $villageID)
    {
        $conquerModel = new Conquer();
        $conquerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.conquer');

        $query = $conquerModel->newQuery();

        switch($type) {
            case "all":
                $query->where('village_id', $villageID);
                break;
            default:
                // FIXME: create error view
                return "Unknown type";
        }
        
        return $this->doConquerReturn($query);
    }
    
    private function doConquerReturn($query) {
        return DataTables::eloquent($query)
            ->editColumn('timestamp', function ($conquer){
                return Carbon::createFromTimestamp($conquer->timestamp);
            })
            ->addColumn('village_name', function ($conquer){
                if($conquer->village == null) return ucfirst (__("ui.player.deleted"));
                return BasicFunctions::decodeName($conquer->village->name);
            })
            ->addColumn('old_owner_name', function ($conquer){
                if($conquer->old_owner == 0) return ucfirst(__('ui.player.barbarian'));
                if($conquer->oldPlayer == null) return ucfirst(__('ui.player.deleted'));
                return BasicFunctions::decodeName($conquer->oldPlayer->nameWithAlly());
            })
            ->addColumn('new_owner_name', function ($conquer){
                if($conquer->new_owner == 0) return ucfirst(__('ui.player.barbarian'));
                if($conquer->newPlayer == null) return ucfirst(__('ui.player.deleted'));
                return BasicFunctions::decodeName($conquer->newPlayer->nameWithAlly());
            })
            ->addColumn('old_owner_exists', function ($conquer){
                return $conquer->oldPlayer != null;
            })
            ->addColumn('new_owner_exists', function ($conquer){
                return $conquer->newPlayer != null;
            })
            ->toJson();
    }

    public static function getWorld($server, $world){
        $serverData = Server::getServerByCode($server);
        return new WorldResource(World::where([['name', '=', $world],['server_id', '=', $serverData->id]])->first());
    }

    public static function getVillageByCoord($server, $world, $x, $y){
        $villageModel = new Village();
        $villageModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.village_latest');

        return new VillageResource($villageModel->where(['x' => $x, 'y' => $y])->first());
    }
}
