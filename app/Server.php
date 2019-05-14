<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    public static function getServer(){
        $serverModel = new Server();
        $serverModel->setTable('c1welt_'.env('DB_DATABASE_MAIN').'.server');
        $serverArray = $serverModel->get();

        var_dump($serverArray);
    }
}
