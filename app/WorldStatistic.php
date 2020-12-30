<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class WorldStatistic extends Model
{
    public function increaseDailyUpdates()
    {
        $this->daily_updates ++;
        $this->save();
    }

    public static function todayWorldStatistic(World $world)
    {
        return WorldStatistic::where('created_at', '>', Carbon::now()->startOfDay())
            ->where('world_id', $world->id)
            ->first();
    }
}
