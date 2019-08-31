<?php

namespace App\Http\Resources;

use App\Util\BasicFunctions;
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
        ];
    }
}
