<?php

namespace App\Http\Resources;

use App\Util\BasicFunctions;
use Illuminate\Http\Resources\Json\JsonResource;

class Player extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        abort_unless(isset($this->playerID), 404);
        //return parent::toArray($request);
        return[
            'playerID' => $this->playerID,
            'name' => BasicFunctions::outputName($this->name),
            'nameRaw' => BasicFunctions::decodeName($this->name),
            'ally_id' => $this->ally_id,
            'points' => $this->points,
            'rank' => $this->rank,
            'village_count' => $this->village_count,
            'offBash' => $this->offBash,
            'offBashRank' => $this->offBashRank,
            'defBash' => $this->defBash,
            'defBashRank' => $this->defBashRank,
            'gesBash' => $this->gesBash,
            'gesBashRank' => $this->gesBashRank,
        ];
    }
}
