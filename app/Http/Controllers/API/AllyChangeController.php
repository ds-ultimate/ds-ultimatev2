<?php

namespace App\Http\Controllers\API;

use App\AllyChanges;
use App\World;
use App\Http\Controllers\Controller;
use App\Util\BasicFunctions;
use Yajra\DataTables\Facades\DataTables;

class AllyChangeController extends Controller
{
    private static $whitelist = ['created_at', 'player_name', 'new_ally_name', 'old_ally_name', 'points'];
    
    public function getAllyAllyChanges(World $world, $type, $allyID)
    {
        DatatablesController::limitResults(200, static::$whitelist);
        
        $allyChangesModel = new AllyChanges($world);
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
                abort(404, __("ui.errors.404.unknownType", ["type" => $type]));
        }

        return $this->doAllyChangeReturn($query);
    }

    public function getPlayerAllyChanges(World $world, $type, $playerID)
    {
        DatatablesController::limitResults(200, static::$whitelist);
        
        $allyChangesModel = new AllyChanges($world);

        $query = $allyChangesModel->newQuery();

        switch($type) {
            case "all":
                $query->where('player_id', $playerID);
                break;
            default:
                abort(404, __("ui.errors.404.unknownType", ["type" => $type]));
        }
        
        return $this->doAllyChangeReturn($query);
    }
    
    private function doAllyChangeReturn($query) {
        return DataTables::eloquent($query)
            ->editColumn('created_at', function ($allyChange){
                return $allyChange->created_at->format('Y-m-d H:i:s');
            })
            ->addColumn('player_name', function ($allyChange){
                if($allyChange->player_id == 0) return ucfirst(__('ui.player.barbarian'));
                if($allyChange->playerTop == null) return ucfirst(__('ui.player.deleted'));
                return ($allyChange->player_id != 0)? BasicFunctions::decodeName($allyChange->playerTop->name) : '-';
            })
            ->addColumn('old_ally_name', function ($allyChange){
                if($allyChange->old_ally_id == 0) return ucfirst(__('ui.ally.noAlly'));
                if($allyChange->oldAlly == null) {
                    if($allyChange->oldAllyTop == null) return ucfirst(__('ui.ally.deleted'));
                    return BasicFunctions::decodeName($allyChange->oldAllyTop->name);
                }
                return BasicFunctions::decodeName($allyChange->oldAlly->name);
            })
            ->addColumn('new_ally_name', function ($allyChange){
                if($allyChange->new_ally_id == 0) return ucfirst(__('ui.ally.noAlly'));
                if($allyChange->newAlly == null) {
                    if($allyChange->newAllyTop == null) return ucfirst(__('ui.ally.deleted'));
                    return BasicFunctions::decodeName($allyChange->newAllyTop->name);
                }
                return BasicFunctions::decodeName($allyChange->newAlly->name);
            })
            ->whitelist(static::$whitelist)
            ->toJson();
    }
}
