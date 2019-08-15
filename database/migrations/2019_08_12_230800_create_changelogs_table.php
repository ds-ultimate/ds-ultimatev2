<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChangelogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('changelogs', function (Blueprint $table) {
            $table->bigIncrements('id');
	    $table->string('version')->nullable();
            $table->text('title');
            $table->text('content');
            $table->text('repository_html_url')->nullable();
            $table->string('icon')->default('fab fa-github-square');
            $table->string('color')->default('#000000');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('changelogs');
    }
}
