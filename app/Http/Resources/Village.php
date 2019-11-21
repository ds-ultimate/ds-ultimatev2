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
        World::existWorld($server, $world);
        $worldData = World::getWorld($server, $world);
        $conquer = Conquer::villageConquerCounts($server, $world, $this->villageID);
        
        //return parent::toArray($request);
        return[
            'villageID' => $this->villageID,
            'name' => BasicFunctions::outputName($this->name),
            'nameRaw' => BasicFunctions::decodeName($this->name),
            'x' => $this->x,
            'y' => $this->y,
            'points' => $this->points,
            'owner' => $this->owner,
            'ownerName' => ($this->owner == 0)? ucfirst(__('ui.player.barbarian')) : BasicFunctions::outputName($this->playerLatest->name),
            'ownerNameRaw' => ($this->owner == 0)? ucfirst(__('ui.player.barbarian')) : BasicFunctions::decodeName($this->playerLatest->name),
            'ownerAlly' => ($this->owner == 0)? 0 : BasicFunctions::outputName($this->playerLatest->ally_id),
            'ownerAllyName' => ($this->owner == 0 || $this->playerLatest->ally_id == 0)? '-' : BasicFunctions::outputName($this->playerLatest->allyLatest->name),
            'ownerAllyNameRaw' => ($this->owner == 0 || $this->playerLatest->ally_id == 0)? '-' : BasicFunctions::decodeName($this->playerLatest->allyLatest->name),
            'ownerAllyTag' => ($this->owner == 0 || $this->playerLatest->ally_id == 0)? '-' : BasicFunctions::outputName($this->playerLatest->allyLatest->tag),
            'ownerAllyTagRaw' => ($this->owner == 0 || $this->playerLatest->ally_id == 0)? '-' : BasicFunctions::decodeName($this->playerLatest->allyLatest->tag),
            'bonus_id' => $this->bonus_id,
            'bonus' => $this->bonusText(),
            'continent' => $this->continentString(),
            'coordinates' => $this->coordinates(),
            'conquer' => BasicFunctions::linkWinLoose($worldData, $this->villageID, $conquer, 'villageConquer', '', true),
            'selfLink' => route('village',[$worldData->server->code, $worldData->name, $this->villageID]),
            'ownerLink' => ($this->owner != 0)?route('player',[$worldData->server->code, $worldData->name, $this->owner]):"",
            'ownerAllyLink' => ($this->owner == 0 || $this->playerLatest->ally_id == 0)?"":
                    route('ally',[$worldData->server->code, $worldData->name, $this->playerLatest->ally_id])
        ];
    }
}
