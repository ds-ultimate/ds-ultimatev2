<?php

namespace App;

use App\Util\BasicFunctions;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class Ally extends CustomModel
{
    protected $primaryKey = 'allyID';
    protected $fillable = [
        'allyID',
        'name',
        'tag',
        'member_count',
        'village_count',
        'points',
        'rank',
        'offBash',
        'offBashRank',
        'defBash',
        'defBashRank',
        'gesBash',
        'gesBashRank',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    public function __construct($arg1 = [], $arg2 = null)
    {
        if($arg1 instanceof World && $arg2 == null) {
            //allow calls without table name
            $arg2 = "ally_latest";
        }
        parent::__construct($arg1, $arg2);

        $this->hash = config('dsUltimate.hash_ally');
    }

    /**
     *@return int
     */
    public function getHash(){
        return $this->hash;
    }

    /**
     * @return Player
     */
    public function playerLatest()
    {
        $table = explode('.', $this->table);
        return $this->myhasMany('App\Player', 'ally_id', 'allyID', $table[0].'.ally_changes');
    }

    /**
     * @return AllyChanges
     */
    public function allyChangesOld()
    {
        $table = explode('.', $this->table);
        return $this->myhasMany('App\AllyChanges', 'old_ally_id', 'allyID', $table[0].'.ally_changes');
    }

    /**
     * @return AllyChanges
     */
    public function allyChangesNew()
    {
        $table = explode('.', $this->table);
        return $this->myhasMany('App\AllyChanges', 'new_ally_id', 'allyID', $table[0].'.ally_changes');
    }

    /**
     * Gibt die besten 10 Stämme zurück
     *
     * @param World $world
     * @return Collection
     */
    public static function top10Ally(World $world){
        $allyModel = new Ally($world);
        return $allyModel->orderBy('rank')->limit(10)->get();
    }

    /**
     * @param World $world
     * @param  int $ally
     * @return $this
     */
    public static function ally(World $world, $ally){
        $allyModel = new Ally($world);
        return $allyModel->find((int) $ally);
    }

    /**
     * @param World $world
     * @param int $ally
     * @return array
     */
    public static function allyDataChart(World $world, $ally){
        $allyID = (int) $ally;
        $tabelNr = $allyID % config('dsUltimate.hash_ally');

        $allyModel = new Ally($world, 'ally_'.$tabelNr);

        $allyDataArray = $allyModel->where('allyID', $allyID)->orderBy('updated_at', 'ASC')->get();

        $allyDatas = [];

        foreach ($allyDataArray as $ally){
            $allyData = [];
            $allyData['timestamp'] = (int)$ally->updated_at->timestamp;
            $allyData['points'] = $ally->points;
            $allyData['rank'] = $ally->rank;
            $allyData['village'] = $ally->village_count;
            $allyData['gesBash'] = $ally->gesBash;
            $allyData['offBash'] = $ally->offBash;
            $allyData['defBash'] = $ally->defBash;
            $allyDatas[] = $allyData;
        }

        return $allyDatas;
    }

    public function linkTag(World $world) {
        return BasicFunctions::linkAlly($world, $this->allyID, BasicFunctions::outputName("[".$this->tag."]"));
    }

    public function linkName(World $world) {
        return BasicFunctions::linkAlly($world, $this->allyID, BasicFunctions::outputName($this->name));
    }

    public function linkIngame(World $world, $guest=false) {
        $guest ? $guestPart = "guest" : $guestPart = "game";

        return "{$world->url}/$guestPart.php?screen=info_ally&id={$this->allyID}";
    }

    public function allyHistory($days){
        $tableNr = $this->allyID % config('dsUltimate.hash_ally');
        $dbName = explode('.', $this->getTable());

        $allyModel = new Ally();
        $allyModel->setTable($dbName[0].'.ally_'.$tableNr);
        $timestamp = Carbon::now()->subDays($days);
        return $allyModel->where('allyID', $this->allyID)->whereDate('updated_at', $timestamp->toDateString())->orderBy('updated_at', 'DESC')->first();
    }
}
