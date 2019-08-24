<?php

namespace App\Http\Resources;

use App\Util\BasicFunctions;
use Illuminate\Http\Resources\Json\JsonResource;

class Ally extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        abort_unless(isset($this->allyID), 404);
        //return parent::toArray($request);
        return[
            'allyID' => $this->allyID,
            'name' => BasicFunctions::outputName($this->name),
            'tag' => BasicFunctions::outputName($this->tag),
            'member_count' => $this->member_count,
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
