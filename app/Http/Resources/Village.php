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
            'x' => $this->x,
            'y' => $this->y,
            'points' => $this->points,
            'owner' => $this->owner,
            'ownerName' => ($this->owner == 0)? '-' : BasicFunctions::outputName($this->playerLatest->name),
            'ownerAlly' => ($this->owner == 0)? '-' : BasicFunctions::outputName($this->playerLatest->allyLatest->name),
            'bonus_id' => $this->bonus_id,
            'bonus' => $this->bonusText(),
            'continent' => $this->continentString(),
        ];
    }
}
