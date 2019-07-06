<?php

namespace App;

use App\Util\BasicFunctions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Village extends Model
{
    private $hash = 109;
    protected $primaryKey = 'villageID';
    protected $fillable =[
            'id', 'name', 'x', 'y', 'points', 'owner', 'bonus_id',
    ];
    
    public $timestamps = true;

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

    public function playerLatest()
    {
        $table = explode('.', $this->table);
        return $this->mybelongsTo('App\Player', 'owner', 'playerID', $table[0].'.player_latest');
    }

    /*
     * Angepasste Funktion für die Ablageart der DB
     * */
    public function mybelongsTo($related, $foreignKey = null, $ownerKey = null, $table, $relation = null)
    {
        if (is_null($relation)) {
            $relation = $this->guessBelongsToRelation();
        }

        $instance = $this->newRelatedInstance($related);

        if (is_null($foreignKey)) {
            $foreignKey = Str::snake($relation).'_'.$instance->getKeyName();
        }

        $ownerKey = $ownerKey ?: $instance->getKeyName();

        $instance->setTable($table);

        return $this->newBelongsTo(
            $instance->newQuery(), $this, $foreignKey, $ownerKey, $relation
        );
    }

    public static function village($server, $world, $village){
        $villageModel = new Village();
        $villageModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.village_latest');

        return $villageModel->find($village);
    }

    public static function villageDataChart($server, $world, $villageID){
        $tabelNr = $villageID % env('HASH_ALLY');

        $villageModel = new Village();
        $villageModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.village_'.$tabelNr);
        
        $villageDataArray = $villageModel->where('villageID', $villageID)->orderBy('updated_at', 'DESC')->get();

        $villageDatas = collect();

        foreach ($villageDataArray as $village){
            $villageData = collect();
            $villageData->put('timestamp', (int)$village->updated_at->timestamp);
            $villageData->put('points', $village->points);
            $villageDatas->push($villageData);
        }

        return $villageDatas;
    }
}