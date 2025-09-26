<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('worlds', function (Blueprint $table) {
            $table->boolean('village_hisory_on_disk')->default(false);
        });

        // Ensure storage/village_history exists
        if (!Storage::exists('village_history')) {
            Storage::makeDirectory('village_history');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('worlds', function (Blueprint $table) {
            $table->dropColumn('village_hisory_on_disk');
        });
        
        Storage::deleteDirectory('village_history');
    }
};
