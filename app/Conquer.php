<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conquer extends Model
{
    public static function playerConquerCounts($server, $world, $playerID){
        $conquerModel = new Conquer();
        $replaceArray = array(
            '{server}' => $server,
            '{world}' => $world
        );
        $conquerModel->setTable(str_replace(array_keys($replaceArray), array_values($replaceArray), env('DB_DATABASE_WORLD', 'c1welt_{server}{world}').'.conquers'));

        $conquer = collect();
        $conquer->put('old', $conquerModel->where('old_owner_id', $playerID)->count());
        $conquer->put('new', $conquerModel->where('new_owner_id', $playerID)->count());
        $conquer->put('total', $conquer->get('old')+$conquer->get('new'));

        return $conquer;

    }
}
