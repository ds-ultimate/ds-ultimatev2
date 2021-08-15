<?php

namespace App\Http\Controllers\API;

use App\Ally;
use App\HistoryIndex;
use App\Player;
use App\Server;
use App\Village;
use App\World;
use App\Util\BasicFunctions;
use App\Http\Controllers\Controller;
use App\Http\Resources\Ally as AllyResource;
use App\Http\Resources\Player as PlayerResource;
use App\Http\Resources\Village as VillageResource;
use App\Http\Resources\VillageHistory as VillageHistoryResource;

class FindModelController extends Controller
{
    public function getVillageByCoord($server, $world, $x, $y){
        $villageModel = new Village();
        $villageModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.village_latest');

        return new VillageResource($villageModel->where(['x' => $x, 'y' => $y])->first());
    }
    
    public function getVillagePreviewByCoord($server, $world, $hId, $x, $y){
        World::existWorld($server, $world);
        $dbName = BasicFunctions::getDatabaseName($server, $world);
        $histIdx = HistoryIndex::find($dbName, $hId);
        abort_if($histIdx === null, 404);
        
        $file = gzopen($histIdx->villageFile($dbName), "r");
        while(! gzeof($file)) {
            $lineOrig = gzgets($file, 4096);
            if($lineOrig === false) continue;
            $line = explode(";", str_replace("\n", "", $lineOrig));
            if($line[2] == $x && $line[3] == $y) {
                return new VillageHistoryResource([
                    "line" => $line,
                    "dbName" => $dbName,
                    "histIdx" => $histIdx,
                    "server" => $server,
                    "world" => $world,
                ]);
            }
        }
        abort(404);
    }

    public function getPlayerByName($server, $world, $name){
        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_latest');

        return new PlayerResource($playerModel->where('name', urlencode($name))->first());
    }

    public function getAllyByName($server, $world, $name){
        $allyModel = new Ally();
        $allyModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.ally_latest');

        return new AllyResource($allyModel->where('name', urlencode($name))->orWhere('tag', urlencode($name))->first());
    }

    public function getSelect2Player($server, $world){
        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_latest');
        return $this->select2return($playerModel, array('name'), 'playerID', function($rawData) {
            return array(
                'id' => $rawData->playerID,
                'text' => BasicFunctions::decodeName($rawData->name),
            );
        });
    }

    public function getSelect2Ally($server, $world){
        $allyModel = new Ally();
        $allyModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.ally_latest');
        return $this->select2return($allyModel, array('name', 'tag'), 'allyID', function($rawData) {
            return array(
                'id' => $rawData->allyID,
                'text' => BasicFunctions::decodeName($rawData->name) . ' [' . BasicFunctions::decodeName($rawData->tag) . ']',
            );
        });
    }

    public function getSelect2PlayerTop($server, $world){
        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_top');
        return $this->select2return($playerModel, array('name'), 'playerID', function($rawData) {
            return array(
                'id' => $rawData->playerID,
                'text' => BasicFunctions::decodeName($rawData->name),
            );
        });
    }

    public function getSelect2AllyTop($server, $world){
        $allyModel = new Ally();
        $allyModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.ally_top');
        return $this->select2return($allyModel, array('name', 'tag'), 'allyID', function($rawData) {
            return array(
                'id' => $rawData->allyID,
                'text' => BasicFunctions::decodeName($rawData->name) . ' [' . BasicFunctions::decodeName($rawData->tag) . ']',
            );
        });
    }
    
    private function select2return(\Illuminate\Database\Eloquent\Model $model, $searchIn, $idRow, callable $extractOne) {
        $getArray = \Illuminate\Support\Facades\Request::input();
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
        $server = Server::getServerByCode($server);
        $worlds = World::where('active', '!=', null)->where('server_id', $server->id)->get();
        $array = [];
        foreach ($worlds as $world){
            $array[] = ['id' => $world->name, 'text' => $world->displayName()];
        }
        return $array;
    }
}
