<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    public static function getServer(){
        $serverModel = new Server();
        $serverModel->setTable(env('DB_DATABASE_MAIN').'.server');
        return $serverModel->get();
    }
}
