<?php

namespace App;

use App\Util\BasicFunctions;

class Conquer extends CustomModel
{
    protected $table = 'conquer';
    
    protected $fillable = [
        'village_id',
        'timestamp',
        'new_owner',
        'old_owner',
        'id',
        'old_owner_name',
        'new_owner_name',
        'old_ally',
        'new_ally',
        'old_ally_name',
        'new_ally_name',
        'old_ally_tag',
        'new_ally_tag',
        'created_at',
        'updated_at',
    ];
    
    protected $dates = [
        'updated_at',
        'created_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function oldPlayer()
    {
        $table = explode('.', $this->table);
        return $this->mybelongsTo('App\Player', 'old_owner', 'playerID', $table[0].'.player_latest');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function newPlayer()
    {
        $table = explode('.', $this->table);
        return $this->mybelongsTo('App\Player', 'new_owner', 'playerID', $table[0].'.player_latest');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function oldAlly()
    {
        $table = explode('.', $this->table);
        return $this->mybelongsTo('App\Ally', 'old_ally', 'allyID', $table[0].'.ally_latest');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function newAlly()
    {
        $table = explode('.', $this->table);
        return $this->mybelongsTo('App\Ally', 'new_ally', 'allyID', $table[0].'.ally_latest');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function village()
    {
        $table = explode('.', $this->table);
        return $this->mybelongsTo('App\Village', 'village_id', 'villageID', $table[0].'.village_latest');
    }

    /**
     * @param string $server
     * @param $world
     * @param int $playerID
     * @return \Illuminate\Support\Collection
     */
    public static function playerConquerCounts($server, $world, $playerID){
        $conquerModel = new Conquer();
        $conquerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.conquer');

        $conquer = collect();
        $conquer->put('old', $conquerModel->where([['old_owner', "=", $playerID],['new_owner', '!=', $playerID]])->count());
        $conquer->put('new', $conquerModel->where([['old_owner', "!=", $playerID],['new_owner', '=', $playerID]])->count());
        $conquer->put('own', $conquerModel->where([['old_owner', "=", $playerID],['new_owner', '=', $playerID]])->count());
        $conquer->put('total', $conquer->get('old')+$conquer->get('new')+$conquer->get('own'));

        return $conquer;
    }

    /**
     * @param  string $server
     * @param $world
     * @param int $allyID
     * @return \Illuminate\Support\Collection
     */
    public static function allyConquerCounts($server, $world, $allyID){
        $conquerModel = new Conquer();
        $conquerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.conquer');

        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_latest');

        $allyPlayers = array();
        foreach ($playerModel->newQuery()->where('ally_id', $allyID)->get() as $player) {
            $allyPlayers[] = $player->playerID;
        }

        $conquer = collect();
        $conquer->put('old', $conquerModel->whereIn('old_owner', $allyPlayers)->whereNotIn('new_owner', $allyPlayers)->count());
        $conquer->put('new', $conquerModel->whereNotIn('old_owner', $allyPlayers)->whereIn('new_owner', $allyPlayers)->count());
        $conquer->put('own', $conquerModel->whereIn('old_owner', $allyPlayers)->whereIn('new_owner', $allyPlayers)->count());
        $conquer->put('total', $conquer->get('old')+$conquer->get('new')+$conquer->get('own'));

        return $conquer;
    }

    /**
     * @param string $server
     * @param $world
     * @param int $villageID
     * @return \Illuminate\Support\Collection
     */
    public static function villageConquerCounts($server, $world, $villageID){
        $conquerModel = new Conquer();
        $conquerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.conquer');

        $conquer = collect();
        $conquer->put('total', $conquerModel->where('village_id', $villageID)->count());

        return $conquer;
    }

    public function linkVillageCoordsWithName($world) {
        if($this->village == null) return ucfirst (__("ui.player.deleted"));
        return BasicFunctions::linkVillage($world, $this->village_id,
                '['.$this->village->coordinates().'] '.BasicFunctions::outputName($this->village->name));
    }

    public function linkOldPlayer($world) {
        if($this->old_owner == 0) return ucfirst(__('ui.player.barbarian'));
        $oldName = $this->old_owner_name;
        if($oldName == null || $oldName == "") {
            if($this->oldPlayer == null) {
                return ucfirst(__('ui.player.deleted'));
            } else {
                $oldName = $this->oldPlayer->name;
            }
        }

        return BasicFunctions::linkPlayer($world, $this->old_owner, BasicFunctions::outputName($oldName));
    }

    public function linkNewPlayer($world) {
        if($this->new_owner == 0) return ucfirst(__('ui.player.barbarian'));
        $newName = $this->new_owner_name;
        if($newName == null || $newName == "") {
            if($this->newPlayer == null) {
                return ucfirst(__('ui.player.deleted'));
            } else {
                $newName = $this->newPlayer->name;
            }
        }

        return BasicFunctions::linkPlayer($world, $this->new_owner, BasicFunctions::outputName($newName));
    }

    public function linkOldAlly($world, $useTag=true) {
        if($this->old_owner == 0) return "-";
        if($useTag) {
            $oldAlly = $this->old_ally_tag;
        } else {
            $oldAlly = $this->old_ally_name;
        }
        $oldID = $this->old_ally;
        if($oldAlly == null) {
            if($this->oldPlayer == null || $this->oldPlayer->allyLatest == null) {
                return "-";
            } else {
                if($useTag) {
                    $oldAlly = "[{$this->oldPlayer->allyLatest->tag}]";
                } else {
                    $oldAlly = "{$this->oldPlayer->allyLatest->name}";
                }
                $oldID = $this->oldPlayer->ally_id;
            }
        } else if($oldAlly == "") {
            if($this->old_ally == 0) {
                return "-"; //player had no ally while conquer
            } else if($this->oldAlly == null) {
                return ucfirst(__('ui.player.deleted'));
            }
        } else if($this->oldAlly == null) {
            //deleted ally
            return BasicFunctions::outputName($oldAlly);
        }
        return BasicFunctions::linkAlly($world, $oldID, BasicFunctions::outputName($oldAlly));
    }

    public function linkNewAlly($world, $useTag=true) {
        if($this->new_owner == 0) return "-";
        if($useTag) {
            $newAlly = $this->new_ally_tag;
        } else {
            $newAlly = $this->new_ally_name;
        }
        $newID = $this->new_ally;
        if($newAlly == null) {
            if($this->newPlayer == null || $this->newPlayer->allyLatest == null) {
                return "-";
            } else {
                if($useTag) {
                    $newAlly = "[{$this->newPlayer->allyLatest->tag}]";
                } else {
                    $newAlly = "{$this->newPlayer->allyLatest->name}";
                }
                $newID = $this->newPlayer->ally_id;
            }
        } else if($newAlly == "") {
            if($this->new_ally == 0) {
                return "-"; //player had no ally while conquer
            } else if($this->newAlly == null) {
                return ucfirst(__('ui.player.deleted'));
            }
        } else if($this->newAlly == null) {
            //deleted ally
            return BasicFunctions::outputName($newAlly);
        }
        return BasicFunctions::linkAlly($world, $newID, BasicFunctions::outputName($newAlly));
    }

    /**
     * 0 ... normal
     * 1 ... internal
     * 2 ... self
     * 3 ... barbarian
     * 4 ... deletion
     *
     * @return integer Type of the conquer
     */
    function getConquerType() {
        if($this->new_owner == 0) return 4;
        if($this->old_owner == 0) return 3;
        if($this->old_owner == $this->new_owner) return 2;
        if($this->getOldAllyID() == $this->getNewAllyID()) return 1;
        return 0;
    }

    public static $REFERTO_VILLAGE = 0;
    public static $REFERTO_PLAYER = 1;
    public static $REFERTO_ALLY = 2;
    /**
     * +1 for win
     * 0 for unknown / doesnt matter
     * -1 for loose
     */
    function getWinLoose($referTO, $id) {
        switch($referTO) {
            case Conquer::$REFERTO_ALLY:
                $oldID = $this->getOldAllyID();
                $newID = $this->getNewAllyID();
                if($oldID == $newID) return 0;
                if($oldID == $id) return -1;
                if($newID == $id) return 1;
                break;
            case Conquer::$REFERTO_PLAYER:
                if($this->old_owner == $this->new_owner) return 0;
                if($this->old_owner == $id) return -1;
                if($this->new_owner == $id) return 1;
                break;
            case Conquer::$REFERTO_VILLAGE:
            default:
                return 0;
        }
        return 0;
    }

    private function getOldAllyID() {
        $oldAllyID = $this->old_ally;
        if($this->old_ally_name == null &&
                $this->oldPlayer != null && $this->oldPlayer->allyLatest != null) {
            $oldAllyID = $this->oldPlayer->ally_id;
        }
        return $oldAllyID;
    }

    private function getNewAllyID() {
        $newAllyID = $this->new_ally;
        if($this->new_ally_name == null &&
                $this->newPlayer != null && $this->newPlayer->allyLatest != null) {
            $newAllyID = $this->newPlayer->ally_id;
        }
        return $newAllyID;
    }
}
