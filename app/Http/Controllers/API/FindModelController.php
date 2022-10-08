<?php

namespace App\Http\Controllers\API;

use App\Ally;
use App\AllyTop;
use App\Conquer;
use App\HistoryIndex;
use App\Player;
use App\PlayerTop;
use App\Server;
use App\Village;
use App\World;
use App\Util\BasicFunctions;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class FindModelController extends Controller
{
    public function getVillageByCoord(World $world, $x, $y){
        $villageModel = new Village($world);
        $result = $villageModel->where(['x' => $x, 'y' => $y])->first();
        abort_if($result == null, 404, __("ui.errors.404.villageNotFound", ["world" => $world->getDistplayName(), "village" => "$x|$y"]));
        return $this->villageToJSON($world, $result);
    }
    
    public function getVillagePreviewByCoord(World $world, $hId, $x, $y){
        $histIdx = HistoryIndex::find($world, $hId);
        abort_if($world == null, 404, __("ui.errors.404.illegalHistoryIndex", ["index" => $hId]));
        
        $file = gzopen($histIdx->villageFile($world), "r");
        while(! gzeof($file)) {
            $lineOrig = gzgets($file, 4096);
            if($lineOrig === false) continue;
            $line = explode(";", str_replace("\n", "", $lineOrig));
            if($line[2] == $x && $line[3] == $y) {
                return $this->villageHistToJSON($world, $line, $histIdx);
            }
        }
        abort(404, __("ui.errors.404.villageNotFound", ["world" => $world->getDistplayName(), "village" => "$x|$y"]));
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
        $getArray = Request::validate([
            'search' => 'string',
            'page' => 'numeric|integer',
        ]);
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
    
    public function getActiveWorldByServer(Server $server){
        $worlds = World::where('active', '!=', null)->where('server_id', $server->id)->get();
        $array = [];
        foreach ($worlds as $world){
            $array[] = ['id' => $world->id, 'name' => $world->name, 'text' => $world->getDistplayName()];
        }
        return $array;
    }
    
    public function getWorldPopup(World $world, $playerId){
        $playerData = PlayerTop::player($world, $playerId);
        abort_if($playerData == null, 404, __("ui.errors.404.playerNotFound", ["world" => $world->getDistplayName(), "player" => $playerId]));
        return "{$world->getDistplayName()}<br>" .
                __('ui.otherWorldsPlayerPopup.rank') . ": " . BasicFunctions::numberConv($playerData->rank_top) . "<br>" .
                __('ui.otherWorldsPlayerPopup.villages') . ": " . BasicFunctions::numberConv($playerData->village_count_top) . "<br>" .
                __('ui.otherWorldsPlayerPopup.points') . ": " . BasicFunctions::numberConv($playerData->points_top) . "<br>" .
                __('ui.otherWorldsPlayerPopup.bashGes') . ": " . BasicFunctions::numberConv($playerData->gesBash_top);
    }
    
    private function villageToJSON(World $world, $result) {
        $conquer = Conquer::villageConquerCounts($world, $result->villageID);
        
        $ownerAlly = 0;
        $ownerAllyName = '-';
        $ownerAllyNameRaw = '-';
        $ownerAllyTag = '-';
        $ownerAllyTagRaw = '-';
        $ownerLink = "";
        $ownerAllyLink = "";
        if($result->owner == 0) {
            $ownerName = ucfirst(__('ui.player.barbarian'));
            $ownerNameRaw = ucfirst(__('ui.player.barbarian'));
        } else if($result->playerLatest == null) {
            $ownerName = ucfirst(__('ui.player.deleted'));
            $ownerNameRaw = ucfirst(__('ui.player.deleted'));
        } else {
            $ownerName = BasicFunctions::outputName($result->playerLatest->name);
            $ownerNameRaw = BasicFunctions::decodeName($result->playerLatest->name);
            $ownerAlly = $result->playerLatest->ally_id;
            $ownerLink = route('player',[$world->server->code, $world->name, $result->owner]);
            
            if($result->playerLatest->ally_id != 0 && $result->playerLatest->allyLatest != null) {
                $ownerAllyName = BasicFunctions::outputName($result->playerLatest->allyLatest->name);
                $ownerAllyNameRaw = BasicFunctions::decodeName($result->playerLatest->allyLatest->name);
                $ownerAllyTag = BasicFunctions::outputName($result->playerLatest->allyLatest->tag);
                $ownerAllyTagRaw = BasicFunctions::decodeName($result->playerLatest->allyLatest->tag);
                $ownerAllyLink = route('ally',[$world->server->code, $world->name, $result->playerLatest->ally_id]);
            }
        }
        
        return response()->json([
            'villageID' => $result->villageID,
            'name' => BasicFunctions::outputName($result->name),
            'nameRaw' => BasicFunctions::decodeName($result->name),
            'x' => $result->x,
            'y' => $result->y,
            'points' => $result->points,
            'owner' => $result->owner,
            'ownerName' => $ownerName,
            'ownerNameRaw' => $ownerNameRaw,
            'ownerAlly' => $ownerAlly,
            'ownerAllyName' => $ownerAllyName,
            'ownerAllyNameRaw' => $ownerAllyNameRaw,
            'ownerAllyTag' => $ownerAllyTag,
            'ownerAllyTagRaw' => $ownerAllyTagRaw,
            'bonus_id' => $result->bonus_id,
            'bonus' => $result->bonusText(),
            'continent' => $result->continentString(),
            'coordinates' => $result->coordinates(),
            'conquer' => BasicFunctions::linkWinLoose($world, $result->villageID, $conquer, 'villageConquer', '', true),
            'selfLink' => route('village',[$world->server->code, $world->name, $result->villageID]),
            'ownerLink' => $ownerLink,
            'ownerAllyLink' => $ownerAllyLink,
        ]);
    }
    
    private function villageHistToJSON(World $world, $line, $histIdx) {
        $conquer = Conquer::villageConquerCounts($world, $line[0]);
        
        $ownerAlly = 0;
        $ownerAllyName = '-';
        $ownerAllyNameRaw = '-';
        $ownerAllyTag = '-';
        $ownerAllyTagRaw = '-';
        $ownerLink = "";
        $ownerAllyLink = "";
        if($line[5] == 0) {
            $ownerName = ucfirst(__('ui.player.barbarian'));
            $ownerNameRaw = ucfirst(__('ui.player.barbarian'));
        } else {
            $playerTop = PlayerTop::player($world, $line[5]);
            if($playerTop == null) {
                $ownerName = ucfirst(__('ui.player.deleted'));
                $ownerNameRaw = ucfirst(__('ui.player.deleted'));
            } else {
                $ownerName = BasicFunctions::outputName($playerTop->name);
                $ownerNameRaw = BasicFunctions::decodeName($playerTop->name);
                $ownerLink = route('player',[$world->server->code, $world->name, $line[5]]);
                $ownerAlly = $this->historyAllyFromPlayer($line[5], $histIdx, $world);
                $ownerAllyElm = AllyTop::ally($world, $ownerAlly);
                
                if($ownerAlly != 0 && $ownerAllyElm != null) {
                    $ownerAllyName = BasicFunctions::outputName($ownerAllyElm->name);
                    $ownerAllyNameRaw = BasicFunctions::decodeName($ownerAllyElm->name);
                    $ownerAllyTag = BasicFunctions::outputName($ownerAllyElm->tag);
                    $ownerAllyTagRaw = BasicFunctions::decodeName($ownerAllyElm->tag);
                    $ownerAllyLink = route('ally',[$world->server->code, $world->name, $ownerAlly]);
                }
            }
        }
        
        //return parent::toArray($request);
        return response()->json([
            'villageID' => $line[0],
            'name' => BasicFunctions::outputName($line[1]),
            'nameRaw' => BasicFunctions::decodeName($line[1]),
            'x' => $line[2],
            'y' => $line[3],
            'points' => $line[4],
            'owner' => $line[5],
            'ownerName' => $ownerName,
            'ownerNameRaw' => $ownerNameRaw,
            'ownerAlly' => $ownerAlly,
            'ownerAllyName' => $ownerAllyName,
            'ownerAllyNameRaw' => $ownerAllyNameRaw,
            'ownerAllyTag' => $ownerAllyTag,
            'ownerAllyTagRaw' => $ownerAllyTagRaw,
            'bonus_id' => $line[6],
            'bonus' => Village::bonusTextStat($line[6]),
            'continent' => Village::continentStringStat($line[2], $line[3]),
            'coordinates' => Village::coordinatesStat($line[2], $line[3]),
            'conquer' => BasicFunctions::linkWinLoose($world, $line[0], $conquer, 'villageConquer', '', true),
            'selfLink' => route('village',[$world->server->code, $world->name, $line[0]]),
            'ownerLink' => $ownerLink,
            'ownerAllyLink' => $ownerAllyLink,
        ]);
    }
    
    private function historyAllyFromPlayer($playerId, $histIdx, $worldData) {
        $file = gzopen($histIdx->playerFile($worldData), "r");
        while(! gzeof($file)) {
            $lineOrig = gzgets($file, 4096);
            if($lineOrig === false) continue;
            $line = explode(";", str_replace("\n", "", $lineOrig));
            if($line[0] == $playerId) {
                return $line[2];
            }
        }
    }
}
