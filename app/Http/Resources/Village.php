<?php

namespace App\Http\Resources;

use App\Conquer;
use App\Util\BasicFunctions;
use App\World;
use Illuminate\Http\Resources\Json\JsonResource;

class Village extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        abort_unless(isset($this->villageID), 404);
        $server = $request->route('server');
        $world = $request->route('world');
        $worldData = World::getAndCheckWorld($server, $world);
        $conquer = Conquer::villageConquerCounts($worldData, $this->villageID);
        
        $ownerAlly = 0;
        $ownerAllyName = '-';
        $ownerAllyNameRaw = '-';
        $ownerAllyTag = '-';
        $ownerAllyTagRaw = '-';
        $ownerLink = "";
        $ownerAllyLink = "";
        if($this->owner == 0) {
            $ownerName = ucfirst(__('ui.player.barbarian'));
            $ownerNameRaw = ucfirst(__('ui.player.barbarian'));
        } else if($this->playerLatest == null) {
            $ownerName = ucfirst(__('ui.player.deleted'));
            $ownerNameRaw = ucfirst(__('ui.player.deleted'));
        } else {
            $ownerName = BasicFunctions::outputName($this->playerLatest->name);
            $ownerNameRaw = BasicFunctions::decodeName($this->playerLatest->name);
            $ownerAlly = $this->playerLatest->ally_id;
            $ownerLink = route('player',[$worldData->server->code, $worldData->name, $this->owner]);
            
            if($this->playerLatest->ally_id != 0 && $this->playerLatest->allyLatest != null) {
                $ownerAllyName = BasicFunctions::outputName($this->playerLatest->allyLatest->name);
                $ownerAllyNameRaw = BasicFunctions::decodeName($this->playerLatest->allyLatest->name);
                $ownerAllyTag = BasicFunctions::outputName($this->playerLatest->allyLatest->tag);
                $ownerAllyTagRaw = BasicFunctions::decodeName($this->playerLatest->allyLatest->tag);
                $ownerAllyLink = route('ally',[$worldData->server->code, $worldData->name, $this->playerLatest->ally_id]);
            }
        }
        
        //return parent::toArray($request);
        return[
            'villageID' => $this->villageID,
            'name' => BasicFunctions::outputName($this->name),
            'nameRaw' => BasicFunctions::decodeName($this->name),
            'x' => $this->x,
            'y' => $this->y,
            'points' => $this->points,
            'owner' => $this->owner,
            'ownerName' => $ownerName,
            'ownerNameRaw' => $ownerNameRaw,
            'ownerAlly' => $ownerAlly,
            'ownerAllyName' => $ownerAllyName,
            'ownerAllyNameRaw' => $ownerAllyNameRaw,
            'ownerAllyTag' => $ownerAllyTag,
            'ownerAllyTagRaw' => $ownerAllyTagRaw,
            'bonus_id' => $this->bonus_id,
            'bonus' => $this->bonusText(),
            'continent' => $this->continentString(),
            'coordinates' => $this->coordinates(),
            'conquer' => BasicFunctions::linkWinLoose($worldData, $this->villageID, $conquer, 'villageConquer', '', true),
            'selfLink' => route('village',[$worldData->server->code, $worldData->name, $this->villageID]),
            'ownerLink' => $ownerLink,
            'ownerAllyLink' => $ownerAllyLink,
        ];
    }
}
