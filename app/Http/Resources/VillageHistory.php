<?php

namespace App\Http\Resources;

use App\AllyTop;
use App\Conquer;
use App\PlayerTop;
use App\Village;
use App\Util\BasicFunctions;
use Illuminate\Http\Resources\Json\JsonResource;

class VillageHistory extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $line = $this->resource["line"];
        $worldData = $this->resource["worldData"];
        $server = $worldData->server->code;
        $world = $worldData->name;
        $histIdx = $this->resource["histIdx"];
        $conquer = Conquer::villageConquerCounts($worldData, $line[0]);
        
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
            $playerTop = PlayerTop::player($worldData, $line[5]);
            if($playerTop == null) {
                $ownerName = ucfirst(__('ui.player.deleted'));
                $ownerNameRaw = ucfirst(__('ui.player.deleted'));
            } else {
                $ownerName = BasicFunctions::outputName($playerTop->name);
                $ownerNameRaw = BasicFunctions::decodeName($playerTop->name);
                $ownerLink = route('player',[$server, $world, $line[5]]);
                $ownerAlly = $this->historyAllyFromPlayer($line[5], $histIdx, $worldData);
                $ownerAllyElm = AllyTop::ally($worldData, $ownerAlly);
                
                if($ownerAlly != 0 && $ownerAllyElm != null) {
                    $ownerAllyName = BasicFunctions::outputName($ownerAllyElm->name);
                    $ownerAllyNameRaw = BasicFunctions::decodeName($ownerAllyElm->name);
                    $ownerAllyTag = BasicFunctions::outputName($ownerAllyElm->tag);
                    $ownerAllyTagRaw = BasicFunctions::decodeName($ownerAllyElm->tag);
                    $ownerAllyLink = route('ally',[$server, $world, $ownerAlly]);
                }
            }
        }
        
        //return parent::toArray($request);
        return[
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
            'conquer' => BasicFunctions::linkWinLoose($worldData, $line[0], $conquer, 'villageConquer', '', true),
            'selfLink' => route('village',[$server, $world, $line[0]]),
            'ownerLink' => $ownerLink,
            'ownerAllyLink' => $ownerAllyLink,
        ];
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
