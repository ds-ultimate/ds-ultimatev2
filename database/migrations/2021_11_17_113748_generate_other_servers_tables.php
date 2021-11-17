<?php

use App\Server;
use App\Console\DatabaseUpdate\TableGenerator;
use Illuminate\Database\Migrations\Migration;

class GenerateOtherServersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach((new Server())->get() as $server) {
            TableGenerator::otherServersTable($server->code);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach((new Server())->get() as $server) {
            Schema::dropIfExists('other_servers_' . $server->code);
        }
    }
}
