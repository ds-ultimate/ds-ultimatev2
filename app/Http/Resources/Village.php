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
            'ownerName' => ($this->owner == 0)? '-' : BasicFunctions::outputName($this->playerLatest->name),
            'ownerNameRaw' => BasicFunctions::decodeName($this->name),
            'ownerAlly' => ($this->owner == 0 || $this->playerLatest->ally_id == 0)? '-' : BasicFunctions::outputName($this->playerLatest->allyLatest->name),
            'ownerAllyRaw' => ($this->owner == 0 || $this->playerLatest->ally_id == 0)? '-' : BasicFunctions::decodeName($this->playerLatest->allyLatest->name),
            'bonus_id' => $this->bonus_id,
            'bonus' => $this->bonusText(),
            'continent' => $this->continentString(),
            'coordinates' => $this->coordinates(),
            'conquer' => BasicFunctions::linkWinLoose($worldData, $this->villageID, $conquer, 'villageConquer', '', true),
            'ownerLink' => ($this->owner != 0)?BasicFunctions::linkPlayer($worldData, $this->owner, BasicFunctions::outputName($this->playerLatest->name), '', '', true) : ucfirst(__('ui.player.barbarian')),
            'ownerAllyLink' => ($this->owner == 0 || $this->playerLatest->ally_id == 0)? '-' :
                    BasicFunctions::linkAlly($worldData, $this->playerLatest->ally_id, BasicFunctions::outputName($this->playerLatest->allyLatest->name.' ['.$this->playerLatest->allyLatest->tag.']'), '', '', true),
        ];
    }
}
