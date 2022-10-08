<?php

use App\World;
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
        Schema::table('worlds', function (Blueprint $table) {
            $table->string('display_name')->nullable()->change();
            $table->timestamp('world_finalized_at')->nullable()->after('worldTop_at');
        });
        
        foreach((new World())->get() as $w) {
            if($w->generateDisplayName() == $w->display_name) {
                $w->display_name = null;
                $w->save();
            }
        }
        
        Schema::table('server', function (Blueprint $table) {
            $table->text('locale')->default('de')->after('classic_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('worlds', function (Blueprint $table) {
            $table->string('display_name')->change();
            $table->dropColumn('world_finalized_at');
        });
        
        Schema::table('server', function (Blueprint $table) {
            $table->dropColumn('locale');
        });
    }
};
