<?php

namespace App;

use App\Util\BasicFunctions;
use Illuminate\Database\Eloquent\Model;

class Conquer extends Model
{

    protected $table = 'conquer';

    public static function playerConquerCounts($server, $world, $playerID){
        $conquerModel = new Conquer();
        $conquerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.conquer');

        $conquer = collect();
        $conquer->put('old', $conquerModel->where('old_owner', $playerID)->count());
        $conquer->put('new', $conquerModel->where('new_owner', $playerID)->count());
        $conquer->put('total', $conquer->get('old')+$conquer->get('new'));

        return $conquer;

    }
}
