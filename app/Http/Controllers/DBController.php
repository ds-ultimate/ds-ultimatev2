<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DBController extends Controller
{

    public function serverTable(){
        DB::statement('CREATE DATABASE c1welt_'.env('DB_DATABASE_MAIN'));
        Schema::create('c1welt_'.env('DB_DATABASE_MAIN').'.server', function (Blueprint $table){
            $table->integer('id')->autoIncrement();
            $table->char('code');
            $table->text('url');
        });
    }

    public function getWorld(){

    }

    public function test(){
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        ini_set('max_execution_time', 1800);
        ini_set('max_input_time', 1800);
        ini_set('memory_limit', '500M');
        DB::statement('CREATE DATABASE c1welt_test');
        Schema::create('c1welt_test.player', function (Blueprint $table) {
            $table->integer('id');
            $table->string('name');
            $table->integer('ally');
            $table->integer('village');
            $table->integer('points');
            $table->integer('rank');
            $table->integer('off')->nullable();
            $table->integer('offR')->nullable();
            $table->integer('def')->nullable();
            $table->integer('defR')->nullable();
            $table->integer('tot')->nullable();
            $table->integer('totR')->nullable();
            $table->timestamps();
        });
        $time = time();
        $lines = gzfile("https://de166.die-staemme.de/map/player.txt.gz");
        if(!is_array($lines)) die("Datei player konnte nicht ge&ouml;ffnet werden");
        $players = collect();
        foreach ($lines as $line){
            list($id, $name, $ally, $villages, $points, $rank) = explode(',', $line);
            $player = collect();
            $player->put('id', (int)$id);
            $player->put('name', $name);
            $player->put('ally', (int)$ally);
            $player->put('villages', (int)$villages);
            $player->put('points', (int)$points);
            $player->put('rank', (int)$rank);
            $player->put('off', (int)null);
            $player->put('offRank', (int)null);
            $player->put('def', (int)null);
            $player->put('defRank', (int)null);
            $player->put('tot', (int)null);
            $player->put('totRank', (int)null);
            $players->put($player->get('id'),$player);
        }

        $offs = gzfile("https://de166.die-staemme.de/map/kill_att.txt.gz");
        if(!is_array($offs)) die("Datei kill_off konnte nicht ge&ouml;ffnet werden");
        foreach ($offs as $off){
            list($rank, $id, $kills) = explode(',', $off);
            $player = $players->firstWhere('id', '=', $id);
            $player->put('offRank', (int)$rank);
            $player->put('off', (int)$kills);
            $players->put($player->get('id'), $player);
        }

        $defs = gzfile("https://de166.die-staemme.de/map/kill_def.txt.gz");
        if(!is_array($defs)) die("Datei kill_def konnte nicht ge&ouml;ffnet werden");
        foreach ($defs as $def){
            list($rank, $id, $kills) = explode(',', $def);
            $player = $players->firstWhere('id', '=', $id);
            $player->put('defRank', (int)$rank);
            $player->put('def', (int)$kills);
            $players->put($player->get('id'), $player);
        }

        $tots = gzfile("https://de166.die-staemme.de/map/kill_all.txt.gz");
        if(!is_array($tots)) die("Datei kill_all konnte nicht ge&ouml;ffnet werden");
        foreach ($tots as $tot){
            list($rank, $id, $kills) = explode(',', $tot);
            $player = $players->firstWhere('id', '=', $id);
            $player->put('totRank', (int)$rank);
            $player->put('tot', (int)$kills);
            $players->put($player->get('id'), $player);
        }

        $insert = new Player();
        $insert->setTable('c1welt_test.player');
        $array = array();
        foreach ($players as $player) {
            $data = [
                'id' => $player->get('id'),
                'name' => $player->get('name'),
                'ally' => $player->get('ally'),
                'village' => $player->get('villages'),
                'points' => $player->get('points'),
                'rank' => $player->get('rank'),
                'off' => $player->get('off'),
                'offR' => $player->get('offRank'),
                'def' => $player->get('def'),
                'defR' => $player->get('defRank'),
                'tot' => $player->get('tot'),
                'totR' => $player->get('totRank'),
                'created_at' => Carbon::createFromTimestamp(time()),
                'updated_at' => Carbon::createFromTimestamp(time()),
            ];
            $array []= $data;
        }
        foreach (array_chunk($array,3000) as $t){
            $insert->insert($t);
        }

        echo time()-$time;
    }
}
