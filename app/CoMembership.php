<?php

namespace App;

class CoMembership extends CustomModel
{
    protected $defaultTableName = "co_memberships";

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
    ];

    public function __construct($arg1 = [], $arg2 = null)
    {
        if($arg1 instanceof World && $arg2 == null) {
            $arg2 = $this->defaultTableName;
        }
        parent::__construct($arg1, $arg2);
    }

    public function player1()
    {
        return $this->mybelongsTo('App\\Player', 'player_1_id', 'playerID', $this->getRelativeTable("player_latest"));
    }

    public function player2()
    {
        return $this->mybelongsTo('App\\Player', 'player_2_id', 'playerID', $this->getRelativeTable("player_latest"));
    }

    public function ally()
    {
        return $this->mybelongsTo('App\\Ally', 'ally_id', 'allyID', $this->getRelativeTable("ally_latest"));
    }
}
