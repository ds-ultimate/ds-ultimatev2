<?php

namespace App\Http\Controllers\Tools;

use App\Server;
use App\World;
use App\Player;
use App\Util\BasicFunctions;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class WarStatsController extends BaseController
{
    public function index(string $server, string $world)
    {
        $serverObj = Server::getAndCheckServerByCode($server);
        $worldObj  = World::getAndCheckWorld($serverObj, $world);

        return view('tools.warStats', [
            'worldData' => $worldObj,
        ]);
    }

    /**
     * GET /{server}/{world}/tools/warStats/data
     */
    public function data(Request $request, string $server, $world)
    {

        $validated = $request->validate([
            'players1'   => 'array',
            'players1.*' => 'integer',
            'tribes1'    => 'array',
            'tribes1.*'  => 'integer',
            'players2'   => 'array',
            'players2.*' => 'integer',
            'tribes2'    => 'array',
            'tribes2.*'  => 'integer',
            'from'     => 'nullable|date',
            'to'       => 'nullable|date',
            'bucket'   => 'nullable|in:daily,hourly,weekly',
        ]);

        $serverObj = Server::getAndCheckServerByCode((string)$server);
        $worldObj  = World::getAndCheckWorld($serverObj, (string)$world);

        $bucket = $validated['bucket'] ?? 'daily';
        $fromTs = isset($validated['from']) ? strtotime($validated['from']) : 0;
        $toTs   = isset($validated['to'])   ? strtotime($validated['to'])   : Carbon::now()->timestamp;

        if ($bucket === 'hourly' && ($toTs - $fromTs) > 7 * 24 * 3600) {
            abort(422, 'hourly bucket only allowed for <=7 days');
        }

        $p1Tokens = [];
        foreach ($validated['players1'] ?? [] as $pid) {
            $p1Tokens[] = 'player:' . $pid;
        }
        foreach ($validated['tribes1'] ?? [] as $tid) {
            $p1Tokens[] = 'tribe:' . $tid;
        }
        $p1 = $this->resolveParty($worldObj, $p1Tokens);

        $p2Tokens = [];
        foreach ($validated['players2'] ?? [] as $pid) {
            $p2Tokens[] = 'player:' . $pid;
        }
        foreach ($validated['tribes2'] ?? [] as $tid) {
            $p2Tokens[] = 'tribe:' . $tid;
        }
        $p2 = $this->resolveParty($worldObj, $p2Tokens);


        $cacheKey = implode('|', [
            'warStats', $worldObj->id,
            'p1:' . implode(',', $p1),
            'p2:' . implode(',', $p2),
            'from:' . $fromTs,
            'to:' . $toTs,
            'bucket:' . $bucket,
        ]);

        $result = Cache::remember($cacheKey, 600, function () use ($worldObj, $p1, $p2, $fromTs, $toTs, $bucket, $server, $world, $validated) {
            return $this->buildStats(
                $worldObj, $p1, $p2, $fromTs, $toTs, $bucket,
                $server . (string)$world,
                $validated['from'] ?? null,
                $validated['to']   ?? null
            );
        });

        return response()->json($result);
    }

    private function resolveParty(World $world, array $tokens): array
    {
        $playerIds = [];
        foreach ($tokens as $tokenRaw) {
            $token = trim((string)$tokenRaw);
            if ($token === '') continue;

            if (strpos($token, 'player:') === 0) {
                $playerIds[] = (int) substr($token, 7);
            } elseif (strpos($token, 'tribe:') === 0) {
                $ally = (int) substr($token, 6);
                if ($ally > 0) {
                    $playerIds = array_merge($playerIds, $this->playersByTribe($world, $ally));
                }
            }
        }
        return array_values(array_unique($playerIds));
    }

    private function playersByTribe(World $world, int $allyId): array
    {
        $playerModel = new Player($world);
        return $playerModel->where('ally_id', $allyId)->pluck('playerID')->toArray();
    }

    private function buildStats(
        World $world,
        array $p1,
        array $p2,
        int $fromTs,
        int $toTs,
        string $bucket,
        string $worldStr,
        ?string $fromStr,
        ?string $toStr
    ): array
    {
        $conquerTbl = BasicFunctions::getWorldDataTable($world, 'conquer');
        $base = DB::table($conquerTbl)
            ->where('timestamp', '>=', $fromTs)
            ->where('timestamp', '<', $toTs);

        $approx = false;
        $totals = [
            'p1'   => ['conquers_gain' => 0, 'conquers_loss' => 0, 'points_gain' => 0, 'points_loss' => 0, 'enemy_conquers' => 0, 'attBash' => 0, 'defBash' => 0],
            'p2'   => ['conquers_gain' => 0, 'conquers_loss' => 0, 'points_gain' => 0, 'points_loss' => 0, 'enemy_conquers' => 0, 'attBash' => 0, 'defBash' => 0],
            'diff' => ['conquers_gain' => 0, 'conquers_loss' => 0, 'points_gain' => 0, 'points_loss' => 0, 'enemy_conquers' => 0, 'attBash' => 0, 'defBash' => 0],
        ];

        if (!empty($p1)) {
            $totals['p1']['conquers_gain'] = (clone $base)->whereIn('new_owner', $p1)->count();
            $totals['p1']['conquers_loss'] = (clone $base)->whereIn('old_owner', $p1)->count();
            $totals['p1']['points_gain']   = (clone $base)->whereIn('new_owner', $p1)->sum(DB::raw('CASE WHEN points >= 0 THEN points ELSE 0 END'));
            $totals['p1']['points_loss']   = (clone $base)->whereIn('old_owner', $p1)->sum(DB::raw('CASE WHEN points >= 0 THEN points ELSE 0 END'));

            $approx = $approx
                || (clone $base)->whereIn('new_owner', $p1)->where('points', '<', 0)->exists()
                || (clone $base)->whereIn('old_owner', $p1)->where('points', '<', 0)->exists();
        }

        if (!empty($p2)) {
            $totals['p2']['conquers_gain'] = (clone $base)->whereIn('new_owner', $p2)->count();
            $totals['p2']['conquers_loss'] = (clone $base)->whereIn('old_owner', $p2)->count();
            $totals['p2']['points_gain']   = (clone $base)->whereIn('new_owner', $p2)->sum(DB::raw('CASE WHEN points >= 0 THEN points ELSE 0 END'));
            $totals['p2']['points_loss']   = (clone $base)->whereIn('old_owner', $p2)->sum(DB::raw('CASE WHEN points >= 0 THEN points ELSE 0 END'));

            $approx = $approx
                || (clone $base)->whereIn('new_owner', $p2)->where('points', '<', 0)->exists()
                || (clone $base)->whereIn('old_owner', $p2)->where('points', '<', 0)->exists();
        }

        if (!empty($p1) && !empty($p2)) {
            $totals['p1']['enemy_conquers'] = (clone $base)->whereIn('new_owner', $p1)->whereIn('old_owner', $p2)->count();
            $totals['p2']['enemy_conquers'] = (clone $base)->whereIn('new_owner', $p2)->whereIn('old_owner', $p1)->count();
        }

        $playerLatest = BasicFunctions::getWorldDataTable($world, 'player_latest');
        if (!empty($p1)) {
            $totals['p1']['attBash'] = DB::table($playerLatest)->whereIn('playerID', $p1)->sum('offBash');
            $totals['p1']['defBash'] = DB::table($playerLatest)->whereIn('playerID', $p1)->sum('defBash');
        }
        if (!empty($p2)) {
            $totals['p2']['attBash'] = DB::table($playerLatest)->whereIn('playerID', $p2)->sum('offBash');
            $totals['p2']['defBash'] = DB::table($playerLatest)->whereIn('playerID', $p2)->sum('defBash');
        }

        foreach (['conquers_gain', 'conquers_loss', 'points_gain', 'points_loss', 'enemy_conquers', 'attBash', 'defBash'] as $k) {
            $totals['diff'][$k] = $totals['p1'][$k] - $totals['p2'][$k];
        }

        $labels = [];
        $p1Gain = [];
        $p2Gain = [];
        $p1Enemy = [];
        $p2Enemy = [];
        $p1Points = [];
        $p2Points = [];

        if ($bucket === 'weekly') {
            $series = DB::table($conquerTbl)
                ->selectRaw(
                    "YEARWEEK(FROM_UNIXTIME(timestamp), 3) as lbl,
                    SUM(new_owner IN (" . (count($p1) ? implode(',', $p1) : -1) . ")) as p1g,
                    SUM(new_owner IN (" . (count($p1) ? implode(',', $p1) : -1) . ") AND old_owner IN (" . (count($p2) ? implode(',', $p2) : -1) . ")) as p1e,
                    SUM(new_owner IN (" . (count($p2) ? implode(',', $p2) : -1) . ")) as p2g,
                    SUM(new_owner IN (" . (count($p2) ? implode(',', $p2) : -1) . ") AND old_owner IN (" . (count($p1) ? implode(',', $p1) : -1) . ")) as p2e,
                    SUM(CASE WHEN new_owner IN (" . (count($p1) ? implode(',', $p1) : -1) . ") AND points >= 0 THEN points ELSE 0 END) as p1pg,
                    SUM(CASE WHEN old_owner IN (" . (count($p1) ? implode(',', $p1) : -1) . ") AND points >= 0 THEN points ELSE 0 END) as p1pl,
                    SUM(CASE WHEN new_owner IN (" . (count($p2) ? implode(',', $p2) : -1) . ") AND points >= 0 THEN points ELSE 0 END) as p2pg,
                    SUM(CASE WHEN old_owner IN (" . (count($p2) ? implode(',', $p2) : -1) . ") AND points >= 0 THEN points ELSE 0 END) as p2pl"
                )
                ->whereBetween('timestamp', [$fromTs, $toTs - 1])
                ->groupBy('lbl')
                ->orderBy('lbl')
                ->get();
        } else {
            $format = '%Y-%m-%d';
            if ($bucket === 'hourly') {
                $format = '%Y-%m-%d %H:00:00';
            }
            $series = DB::table($conquerTbl)
                ->selectRaw(
                    "FROM_UNIXTIME(timestamp, '$format') as lbl,
                    SUM(new_owner IN (" . (count($p1) ? implode(',', $p1) : -1) . ")) as p1g,
                    SUM(new_owner IN (" . (count($p1) ? implode(',', $p1) : -1) . ") AND old_owner IN (" . (count($p2) ? implode(',', $p2) : -1) . ")) as p1e,
                    SUM(new_owner IN (" . (count($p2) ? implode(',', $p2) : -1) . ")) as p2g,
                    SUM(new_owner IN (" . (count($p2) ? implode(',', $p2) : -1) . ") AND old_owner IN (" . (count($p1) ? implode(',', $p1) : -1) . ")) as p2e,
                    SUM(CASE WHEN new_owner IN (" . (count($p1) ? implode(',', $p1) : -1) . ") AND points >= 0 THEN points ELSE 0 END) as p1pg,
                    SUM(CASE WHEN old_owner IN (" . (count($p1) ? implode(',', $p1) : -1) . ") AND points >= 0 THEN points ELSE 0 END) as p1pl,
                    SUM(CASE WHEN new_owner IN (" . (count($p2) ? implode(',', $p2) : -1) . ") AND points >= 0 THEN points ELSE 0 END) as p2pg,
                    SUM(CASE WHEN old_owner IN (" . (count($p2) ? implode(',', $p2) : -1) . ") AND points >= 0 THEN points ELSE 0 END) as p2pl"
                )
                ->whereBetween('timestamp', [$fromTs, $toTs - 1])
                ->groupBy('lbl')
                ->orderBy('lbl')
                ->get();
        }

        foreach ($series as $row) {
            $labels[]  = $row->lbl;
            $p1Gain[]  = (int) $row->p1g;
            $p2Gain[]  = (int) $row->p2g;
            $p1Enemy[] = (int) ($row->p1e ?? 0);
            $p2Enemy[] = (int) ($row->p2e ?? 0);
            $p1Points[] = (int) ($row->p1pg - $row->p1pl);
            $p2Points[] = (int) ($row->p2pg - $row->p2pl);
        }

        $topAttackers = [];
        if (!empty($p1)) {
            $topAttackers = DB::table($conquerTbl . ' as c')
                ->selectRaw('c.new_owner as player_id, p.name, COUNT(*) as count')
                ->leftJoin($playerLatest . ' as p', 'p.playerID', '=', 'c.new_owner')
                ->whereBetween('c.timestamp', [$fromTs, $toTs - 1])
                ->whereIn('c.new_owner', $p1)
                ->groupBy('c.new_owner', 'p.name')
                ->orderByDesc('count')
                ->limit(10)
                ->get()
                ->map(fn($row) => ['player_id' => $row->player_id, 'name' => $row->name, 'count' => (int)$row->count])
                ->toArray();
        }

        $topDefenders = [];
        if (!empty($p2)) {
            $topDefenders = DB::table($conquerTbl . ' as c')
                ->selectRaw('c.old_owner as player_id, p.name, COUNT(*) as count')
                ->leftJoin($playerLatest . ' as p', 'p.playerID', '=', 'c.old_owner')
                ->whereBetween('c.timestamp', [$fromTs, $toTs - 1])
                ->whereIn('c.old_owner', $p2)
                ->groupBy('c.old_owner', 'p.name')
                ->orderByDesc('count')
                ->limit(10)
                ->get()
                ->map(fn($row) => ['player_id' => $row->player_id, 'name' => $row->name, 'count' => (int)$row->count])
                ->toArray();
        }

        $meta = [
            'world'         => $worldStr,
            'from'          => $fromStr,
            'to'            => $toStr,
            'approx_points' => $approx,
            'has_oda_odd'   => false,
            'sources'       => ['conquer', 'player_*', 'ally_*'],
        ];

        return [
            'totals' => $totals,
            'series' => [
                'bucket' => $bucket,
                'labels' => $labels,
                'p1'     => ['gain' => $p1Gain, 'enemy' => $p1Enemy, 'points' => $p1Points],
                'p2'     => ['gain' => $p2Gain, 'enemy' => $p2Enemy, 'points' => $p2Points],
            ],
            'tables' => [
                'top_attackers'      => $topAttackers,
                'top_defenders_lost' => $topDefenders,
            ],
            'meta' => $meta,
        ];
    }
}
