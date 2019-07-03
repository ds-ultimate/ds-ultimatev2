<?php

namespace App;

use App\Util\BasicFunctions;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    private $hash = 109;
    protected $primaryKey = 'villageID';
    protected $fillable =[
            'id', 'name', 'x', 'y', 'points', 'owner', 'bonus_id',
    ];

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