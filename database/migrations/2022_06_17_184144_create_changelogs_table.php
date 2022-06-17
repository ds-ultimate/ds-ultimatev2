<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('changelogs', function (Blueprint $table) {
            $table->id();
	    $table->string('version')->nullable();
            $table->text('title');
            $table->text('de');
            $table->text('en')->nullable();
            $table->text('repository_html_url')->nullable();
            $table->string('icon')->default('fab fa-github-square');
            $table->string('color')->default('#000000');
            $table->text('buffer')->nullable();
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
};
