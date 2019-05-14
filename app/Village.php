<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    private $hash = 109;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->hash = env('HASH_VILLAGE', 109);

    }

    /**
     *@return int
     */
    public function getHash(){
        return $this->hash;
    }

}