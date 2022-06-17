<?php

namespace App\Http\Controllers\API;

use App\Ally;
use App\AllyTop;
use App\HistoryIndex;
use App\Player;
use App\PlayerTop;
use App\Server;
use App\Village;
use App\World;
use App\Util\BasicFunctions;
use App\Http\Controllers\Controller;
use App\Http\Resources\Ally as AllyResource;
use App\Http\Resources\Player as PlayerResource;
use App\Http\Resources\Village as VillageResource;
use App\Http\Resources\VillageHistory as VillageHistoryResource;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class FindModelController extends Controller
{
    public function getVillageByCoord(World $world, $x, $y){
        $villageModel = new Village($world);
        return new VillageResource($villageModel->where(['x' => $x, 'y' => $y])->first());
    }
    
    public function getVillagePreviewByCoord(World $world, $hId, $x, $y){
        $histIdx = HistoryIndex::find($world, $hId);
        abort_if($histIdx === null, 404);
        
        $file = gzopen($histIdx->villageFile($world), "r");
        while(! gzeof($file)) {
            $lineOrig = gzgets($file, 4096);
            if($lineOrig === false) continue;
            $line = explode(";", str_replace("\n", "", $lineOrig));
            if($line[2] == $x && $line[3] == $y) {
                return new VillageHistoryResource([
                    "line" => $line,
                    "worldData" => $world,
                    "histIdx" => $histIdx,
                ]);
            }
        }
        abort(404);
    }

    public function getPlayerByName(World $world, $name){
        $playerModel = new Player($world);
        return new PlayerResource($playerModel->where('name', urlencode($name))->first());
    }

    public function getAllyByName(World $world, $name){
        $allyModel = new Ally($world);
        return new AllyResource($allyModel->where('name', urlencode($name))->orWhere('tag', urlencode($name))->first());
    }

    public function getSelect2Player(World $world){
        $playerModel = new Player($world);
        return $this->select2return($playerModel, array('name'), 'playerID', function($rawData) {
            return array(
                'id' => $rawData->playerID,
                'text' => BasicFunctions::decodeName($rawData->name),
            );
        });
    }

    public function getSelect2Ally(World $world){
        $allyModel = new Ally($world);
        return $this->select2return($allyModel, array('name', 'tag'), 'allyID', function($rawData) {
            return array(
                'id' => $rawData->allyID,
                'text' => BasicFunctions::decodeName($rawData->name) . ' [' . BasicFunctions::decodeName($rawData->tag) . ']',
            );
        });
    }

    public function getSelect2PlayerTop(World $world){
        $playerModel = new PlayerTop($world);
        return $this->select2return($playerModel, array('name'), 'playerID', function($rawData) {
            return array(
                'id' => $rawData->playerID,
                'text' => BasicFunctions::decodeName($rawData->name),
            );
        });
    }

    public function getSelect2AllyTop(World $world){
        $allyModel = new AllyTop($world);
        return $this->select2return($allyModel, array('name', 'tag'), 'allyID', function($rawData) {
            return array(
                'id' => $rawData->allyID,
                'text' => BasicFunctions::decodeName($rawData->name) . ' [' . BasicFunctions::decodeName($rawData->tag) . ']',
            );
        });
    }
    
    private function select2return(Model $model, $searchIn, $idRow, callable $extractOne) {
        $getArray = Request::input();
        $perPage = 50;
        $search = (isset($getArray['search']))?('%'.BasicFunctions::likeSaveEscape(urlencode($getArray['search'])).'%'):('%');
        $page = (isset($getArray['page']))?($getArray['page']-1):(0);
        
        foreach($searchIn as $row) {
            $model = $model->orWhere($row, 'like', $search);
        }
        if(isset($getArray['search']) && ctype_digit($getArray['search'])) {
            //search by ID
            $model = $model->orWhere($idRow, 'like', '%' . intval($getArray['search']) . '%');
        }
        
        $dataAll = $model->offset($perPage*$page)->limit($perPage+1)->get();
        $converted = array('results' => array(), 'pagination' => array('more' => false));
        $i = 0;
        foreach($dataAll as $data) {
            if($i < $perPage) {
                $converted['results'][] = $extractOne($data);
            } else {
                $converted['pagination']['more'] = true;
            }
            $i++;
        }
        return response()->json($converted);
    }
    
    public function getActiveWorldByServer($server){
        $serModel = Server::getAndCheckServerByCode($server);
        
        $worlds = World::where('active', '!=', null)->where('server_id', $serModel->id)->get();
        $array = [];
        foreach ($worlds as $world){
            $array[] = ['id' => $world->name, 'text' => $world->display_name];
        }
        return $array;
    }
    
    public function getWorldPopup(World $world, $playerId){
        $playerData = PlayerTop::player($world, $playerId);
        abort_if($playerData == null, 404);
        return "{$world->display_name}<br>" .
                __('ui.otherWorldsPlayerPopup.rank') . ": " . BasicFunctions::numberConv($playerData->rank_top) . "<br>" .
                __('ui.otherWorldsPlayerPopup.villages') . ": " . BasicFunctions::numberConv($playerData->village_count_top) . "<br>" .
                __('ui.otherWorldsPlayerPopup.points') . ": " . BasicFunctions::numberConv($playerData->points_top) . "<br>" .
                __('ui.otherWorldsPlayerPopup.bashGes') . ": " . BasicFunctions::numberConv($playerData->gesBash_top);
    }
}
