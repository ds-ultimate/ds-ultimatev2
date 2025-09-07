@extends('layouts.app')

@section('titel', $worldData->getDistplayName().': '.__('tool.scavengerCalc.title'))

@section('content')
    <div class="row justify-content-center">

        <div class="col-12 p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('tool.scavengerCalc.title')).' ['.$worldData->getDistplayName().']' }}
            </h1>
        </div>
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">{{ ucfirst(__('tool.scavengerCalc.title')) }}</h1>
            <h4>{{ '['.$worldData->getDistplayName().']' }}</h4>
        </div>

        <div class="col-12 mt-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <label class="me-2 mb-0 fw-bold">{{ __('tool.scavengerCalc.maxAway') }}:</label>
                        <input type="number" id="max-h" class="form-control form-control-sm" style="width:110px" min="0"
                               placeholder="{{ __('tool.scavengerCalc.hours') }}">
                        <span class="mx-1">:</span>
                        <input type="number" id="max-m" class="form-control form-control-sm" style="width:110px" min="0"
                               max="59" placeholder="{{ __('tool.scavengerCalc.minutes') }}">
                        <span class="ms-2" id="max-info" style="min-width:140px;"></span>
                    </div>

                    <div class="mt-2">
                        <div class="form-check">
                            <input class="form-check-input opt-chk" type="radio" name="opt-mode" id="opt-run"
                                   value="run">
                            <label class="form-check-label"
                                   for="opt-run">{{ __('tool.scavengerCalc.opt_perRun') }}</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input opt-chk" type="radio" name="opt-mode" id="opt-hour"
                                   value="hour">
                            <label class="form-check-label"
                                   for="opt-hour">{{ __('tool.scavengerCalc.opt_perHour') }}</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input opt-chk" type="radio" name="opt-mode" id="opt-equal"
                                   value="equal"  checked>
                            <label class="form-check-label"
                                   for="opt-equal">{{ __('tool.scavengerCalc.opt_equal') }}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 mt-2 mb-3">
            <div class="card">
                <div class="card-body p-2 p-md-3">
                    <div class="table-responsive">
                        <table id="scavTable" class="table table-bordered table-striped align-middle mb-0">
                            <thead>
                            <tr>
                                <th style="width:220px">{{ __('global.unit') }}</th>
                                <th style="width:180px">{{ __('tool.scavengerCalc.available') }}</th>
                                <th class="text-center" style="width:90px">
                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input scv-chk" type="checkbox" id="chk-ff" checked>
                                        <label class="form-check-label" for="chk-ff">{{ __('tool.scavengerCalc.ff') }}</label>
                                    </div>
                                </th>
                                <th class="text-center" style="width:90px">
                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input scv-chk" type="checkbox" id="chk-bb" checked>
                                        <label class="form-check-label" for="chk-bb">{{ __('tool.scavengerCalc.bb') }}</label>
                                    </div>
                                </th>
                                <th class="text-center" style="width:90px">
                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input scv-chk" type="checkbox" id="chk-ss" checked>
                                        <label class="form-check-label" for="chk-ss">{{ __('tool.scavengerCalc.ss') }}</label>
                                    </div>
                                </th>
                                <th class="text-center" style="width:90px">
                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input scv-chk" type="checkbox" id="chk-rr" checked>
                                        <label class="form-check-label" for="chk-rr">{{ __('tool.scavengerCalc.rr') }}</label>
                                    </div>
                                </th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr data-row="spear">
                                <td><img src="{{ \App\Util\Icon::icons(0) }}" class="me-1"> {{ __('ui.unit.spear') }}
                                </td>
                                <td>
                                    <input name="spear" class="form-control form-control-sm unit-available"
                                           placeholder="0" inputmode="numeric"
                                           data-unit="spear" data-carry="{{ (int)$unitConfig->spear->carry }}">
                                </td>
                                <td class="text-end alloc-FF">0</td>
                                <td class="text-end alloc-BB">0</td>
                                <td class="text-end alloc-SS">0</td>
                                <td class="text-end alloc-RR">0</td>
                            </tr>

                            <tr data-row="sword">
                                <td><img src="{{ \App\Util\Icon::icons(1) }}" class="me-1"> {{ __('ui.unit.sword') }}
                                </td>
                                <td>
                                    <input name="sword" class="form-control form-control-sm unit-available"
                                           placeholder="0" inputmode="numeric"
                                           data-unit="sword" data-carry="{{ (int)$unitConfig->sword->carry }}">
                                </td>
                                <td class="text-end alloc-FF">0</td>
                                <td class="text-end alloc-BB">0</td>
                                <td class="text-end alloc-SS">0</td>
                                <td class="text-end alloc-RR">0</td>
                            </tr>

                            <tr data-row="axe">
                                <td><img src="{{ \App\Util\Icon::icons(2) }}" class="me-1"> {{ __('ui.unit.axe') }}</td>
                                <td>
                                    <input name="axe" class="form-control form-control-sm unit-available"
                                           placeholder="0" inputmode="numeric"
                                           data-unit="axe" data-carry="{{ (int)$unitConfig->axe->carry }}">
                                </td>
                                <td class="text-end alloc-FF">0</td>
                                <td class="text-end alloc-BB">0</td>
                                <td class="text-end alloc-SS">0</td>
                                <td class="text-end alloc-RR">0</td>
                            </tr>
                            @if ($config->game->archer == 1)

                                <tr data-row="archer">
                                    <td><img src="{{ \App\Util\Icon::icons(3) }}"
                                             class="me-1"> {{ __('ui.unit.archer') }}</td>
                                    <td>
                                        <input name="archer" class="form-control form-control-sm unit-available"
                                               placeholder="0" inputmode="numeric"
                                               data-unit="archer" data-carry="{{ (int)$unitConfig->archer->carry }}">
                                    </td>
                                    <td class="text-end alloc-FF">0</td>
                                    <td class="text-end alloc-BB">0</td>
                                    <td class="text-end alloc-SS">0</td>
                                    <td class="text-end alloc-RR">0</td>
                                </tr>
                            @endif

                            <tr data-row="light">
                                <td><img src="{{ \App\Util\Icon::icons(5) }}" class="me-1"> {{ __('ui.unit.light') }}
                                </td>
                                <td>
                                    <input name="light" class="form-control form-control-sm unit-available"
                                           placeholder="0" inputmode="numeric"
                                           data-unit="light" data-carry="{{ (int)$unitConfig->light->carry }}">
                                </td>
                                <td class="text-end alloc-FF">0</td>
                                <td class="text-end alloc-BB">0</td>
                                <td class="text-end alloc-SS">0</td>
                                <td class="text-end alloc-RR">0</td>
                            </tr>
                            @if ($config->game->archer == 1)
                                <tr data-row="marcher">
                                    <td><img src="{{ \App\Util\Icon::icons(6) }}"
                                             class="me-1"> {{ __('ui.unit.marcher') }}</td>
                                    <td>
                                        <input name="marcher" class="form-control form-control-sm unit-available"
                                               placeholder="0" inputmode="numeric"
                                               data-unit="marcher" data-carry="{{ (int)$unitConfig->marcher->carry }}">
                                    </td>
                                    <td class="text-end alloc-FF">0</td>
                                    <td class="text-end alloc-BB">0</td>
                                    <td class="text-end alloc-SS">0</td>
                                    <td class="text-end alloc-RR">0</td>
                                </tr>
                            @endif

                            <tr data-row="heavy">
                                <td><img src="{{ \App\Util\Icon::icons(7) }}" class="me-1"> {{ __('ui.unit.heavy') }}
                                </td>
                                <td>
                                    <input name="heavy" class="form-control form-control-sm unit-available"
                                           placeholder="0" inputmode="numeric"
                                           data-unit="heavy" data-carry="{{ (int)$unitConfig->heavy->carry }}">
                                </td>
                                <td class="text-end alloc-FF">0</td>
                                <td class="text-end alloc-BB">0</td>
                                <td class="text-end alloc-SS">0</td>
                                <td class="text-end alloc-RR">0</td>
                            </tr>
                            </tbody>

                            <tfoot>
                            <tr class="fw-bold">
                                <td>{{ __('tool.scavengerCalc.idealCap') }}</td>
                                <td></td>
                                <td class="text-end foot-FF-icap">0</td>
                                <td class="text-end foot-BB-icap">0</td>
                                <td class="text-end foot-SS-icap">0</td>
                                <td class="text-end foot-RR-icap">0</td>
                            </tr>
                            <tr>
                                <td>{{ __('tool.scavengerCalc.lootPerRun') }}</td>
                                <td></td>
                                <td class="text-end foot-FF-run">0</td>
                                <td class="text-end foot-BB-run">0</td>
                                <td class="text-end foot-SS-run">0</td>
                                <td class="text-end foot-RR-run">0</td>
                            </tr>
                            <tr>
                                <td>{{ __('tool.scavengerCalc.lootPerHour') }}</td>
                                <td></td>
                                <td class="text-end foot-FF-hour">0</td>
                                <td class="text-end foot-BB-hour">0</td>
                                <td class="text-end foot-SS-hour">0</td>
                                <td class="text-end foot-RR-hour">0</td>
                            </tr>
                            <tr>
                                <td>{{ __('tool.scavengerCalc.duration') }}</td>
                                <td></td>
                                <td class="text-end foot-FF-time">00:00:00</td>
                                <td class="text-end foot-BB-time">00:00:00</td>
                                <td class="text-end foot-SS-time">00:00:00</td>
                                <td class="text-end foot-RR-time">00:00:00</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        const RATIOS = {FF: 0.10, BB: 0.25, SS: 0.50, RR: 0.75};
        const LEVELS_ORDER = ["FF", "BB", "SS", "RR"];

        const DF = (function () {
            const w = Number({{ isset($config->speed->world) ? $config->speed->world : (isset($config->speed) ? $config->speed : '1') }});
            if (!isFinite(w) || w <= 0) return 1;
            return Math.pow(w, -0.55);
        })();

        function T_of_K(Ki, r, df) {
            return (Math.pow((Ki * Ki) * 100 * (r * r), 0.45) + 1800) * df;
        }

        function Kmax_for_T(Tmax, r, df) {
            if (!Tmax) return Infinity;
            const X = (Tmax / df) - 1800;
            if (X <= 0) return 0;
            return Math.sqrt(Math.pow(X, 1 / 0.45) / (100 * r * r));
        }

        const fmtInt = n => (isFinite(n) ? Math.round(n).toString() : "0");
        const fmtFix = (n, d = 2) => (isFinite(n) ? Number(n).toFixed(d) : "0.00");

        function sekinzeit(sek) {
            sek = Math.max(0, Math.floor(sek));
            const h = String(Math.floor(sek / 3600)).padStart(2, '0');
            const m = String(Math.floor((sek % 3600) / 60)).padStart(2, '0');
            const s = String(sek % 60).padStart(2, '0');
            return `${h}:${m}:${s}`;
        }

        function readInputsAndCaps() {
            const inputs = {}, caps = {};
            $('#scavTable .unit-available').each(function () {
                const $i = $(this);
                const u = $i.data('unit');
                const c = Number($i.data('carry') || 0);
                const raw = ($i.val() || '').toString().replace(',', '.');
                const v = Number(raw);
                if (!u) return;
                caps[u] = c;
                inputs[u] = (isFinite(v) && v > 0) ? Math.floor(v) : 0;
            });
            return {inputs, caps};
        }

        function totalCapacityOf(inputs, caps) {
            return Object.keys(inputs).reduce((s, u) => s + (inputs[u] || 0) * (caps[u] || 0), 0);
        }

        function equalDurationDistribution(K, rList, Tmax, df) {
            const sumInv = rList.reduce((s, r) => s + 1 / r, 0);
            let Ki = rList.map(r => K / (r * sumInv));
            if (Tmax) {
                const caps = rList.map(r => Kmax_for_T(Tmax, r, df));
                const scale = Math.min(...Ki.map((k, i) => caps[i] / k));
                if (isFinite(scale) && scale < 1) Ki = Ki.map(k => k * scale);
            }
            return Ki;
        }

        function perRunGreedy(K, rList, Tmax, df) {
            const idx = [...rList.keys()].sort((a, b) => rList[b] - rList[a]);
            const Ki = new Array(rList.length).fill(0);
            let rem = K;
            for (const i of idx) {
                const capMax = Kmax_for_T(Tmax || Infinity, rList[i], df);
                const take = Math.min(rem, capMax);
                Ki[i] = take;
                rem -= take;
            }
            return Ki;
        }

        function perHourHillClimbSimple(K, rList, df) {
            const n = rList.length;
            if (K <= 0 || n === 0) return new Array(n).fill(0);
            let a = new Array(n).fill(1 / n);
            function score(arr) {
                let H = 0;
                for (let i = 0; i < n; i++) {
                    const Ki = Math.max(0, arr[i] * K);
                    const Gi = Ki * rList[i];
                    const Ti = T_of_K(Ki, rList[i], df);
                    if (Ti > 0) H += (Gi / Ti);
                }
                return H;
            }
            function norm(arr) {
                const s = arr.reduce((x, y) => x + Math.max(0, y), 0);
                return arr.map(x => (s > 0 ? Math.max(0, x) / s : 0));
            }
            let best = score(a);
            for (let it = 0; it < 400; it++) {
                let improved = false;
                for (let i = 0; i < n - 1; i++) {
                    const j = i + 1;
                    const di = a[i] * 0.5 || (1 / (2 * n));
                    const dj = a[j] * 0.5 || (1 / (2 * n));
                    const candidates = [
                        a,
                        norm((() => { const b = a.slice(); b[i] -= di; b[j] += di; return b; })()),
                        norm((() => { const b = a.slice(); b[i] += dj; b[j] -= dj; return b; })())
                    ];
                    for (const cand of candidates) {
                        const val = score(cand);
                        if (val > best + 1e-9) {
                            a = cand;
                            best = val;
                            improved = true;
                        }
                    }
                }
                if (!improved) break;
            }
            return a.map(x => Math.max(0, x) * K);
        }

        function perHourWithCaps(K, rList, Tmax, df) {
            const n = rList.length;
            if (n === 0 || K <= 0) return new Array(n).fill(0);
            const caps = rList.map(r => Kmax_for_T(Tmax, r, df));
            let remainingK = Math.min(K, caps.reduce((s, c) => s + (isFinite(c) ? c : 0), 0));
            const fixed = new Array(n).fill(0);
            let activeIdx = [...Array(n).keys()].filter(i => caps[i] > 0);
            while (activeIdx.length > 0 && remainingK > 1e-9) {
                const rAct = activeIdx.map(i => rList[i]);
                let KiAct = perHourHillClimbSimple(remainingK, rAct, df);
                const over = [];
                for (let t = 0; t < activeIdx.length; t++) {
                    const i = activeIdx[t];
                    if (KiAct[t] > caps[i] + 1e-9) over.push(t);
                }
                if (over.length === 0) {
                    for (let t = 0; t < activeIdx.length; t++) fixed[activeIdx[t]] += KiAct[t];
                    remainingK = 0;
                    break;
                }
                let frozenSum = 0;
                const keep = [];
                for (let t = 0; t < activeIdx.length; t++) {
                    const i = activeIdx[t];
                    if (KiAct[t] > caps[i] + 1e-9) {
                        fixed[i] += caps[i];
                        frozenSum += caps[i];
                    } else {
                        keep.push(i);
                    }
                }
                remainingK -= frozenSum;
                if (remainingK < 1e-9) remainingK = 0;
                activeIdx = keep;
                if (activeIdx.length === 0 || remainingK <= 1e-9) break;
            }
            return fixed;
        }

        function perHourHillClimb(K, rList, Tmax, df) {
            if (!Tmax) return perHourHillClimbSimple(totalCap, rList, df);
            return perHourWithCaps(totalCap, rList, Tmax, df);
        }

        function allocateUnitsToLevels(KiDesired, inputs, activeLevels, caps) {
            const out = {};
            activeLevels.forEach(L => out[L] = {});
            const unitList = Object.keys(inputs).filter(u => (caps[u] || 0) > 0)
                .sort((a, b) => (caps[b] || 0) - (caps[a] || 0));
            const need = activeLevels.map((_, i) => KiDesired[i]);
            const left = {};
            unitList.forEach(u => left[u] = inputs[u] || 0);
            activeLevels.forEach(L => unitList.forEach(u => out[L][u] = 0));
            for (let li = 0; li < activeLevels.length; li++) {
                let rem = need[li];
                for (const u of unitList) {
                    if (rem <= 0) break;
                    const have = left[u];
                    if (!have) continue;
                    const cap = caps[u];
                    const maxByCap = Math.floor(rem / cap);
                    if (maxByCap <= 0) continue;
                    const take = Math.min(have, maxByCap);
                    if (take > 0) {
                        out[activeLevels[li]][u] += take;
                        left[u] -= take;
                        rem -= take * cap;
                    }
                }
                need[li] = rem;
            }
            return out;
        }

        function renderAllocationsToTable(activeLevels, unitCols, allocation) {
            unitCols.forEach(u => {
                const $row = $('#scavTable tbody tr[data-row="' + u + '"]');
                activeLevels.forEach(L => {
                    const cls = '.alloc-' + L;
                    const val = allocation[L]?.[u] ? allocation[L][u] : 0;
                    $row.find(cls).text(fmtInt(val));
                });
                ['FF', 'BB', 'SS', 'RR'].forEach(L => {
                    if (!activeLevels.includes(L)) {
                        $row.find('.alloc-' + L).text('0');
                    }
                });
            });
        }

        function calculate() {
            const {inputs, caps} = readInputsAndCaps();
            const totalCap = totalCapacityOf(inputs, caps);
            const activeLevels = $.grep(LEVELS_ORDER, L => $('#chk-' + L.toLowerCase()).prop('checked'));
            const rList = $.map(activeLevels, L => RATIOS[L]);
            const H = Number($('#max-h').val() || 0);
            const M = Number($('#max-m').val() || 0);
            const Tmax = (H || M) ? (H * 3600 + M * 60) : null;
            $('#max-info').text(Tmax ? '{{ __('tool.scavengerCalc.limitActive') }}'.replace('%H%', H).replace('%M%', M) : '');
            if (!totalCap || activeLevels.length === 0) {
                renderAllocationsToTable(activeLevels, Object.keys(inputs), {FF: {}, BB: {}, SS: {}, RR: {}});
                $('.foot-FF-icap,.foot-BB-icap,.foot-SS-icap,.foot-RR-icap,.foot-FF-run,.foot-BB-run,.foot-SS-run,.foot-RR-run,.foot-FF-hour,.foot-BB-hour,.foot-SS-hour,.foot-RR-hour,.foot-FF-time,.foot-BB-time,.foot-SS-time,.foot-RR-time').text('0');
                $('.foot-FF-time,.foot-BB-time,.foot-SS-time,.foot-RR-time').text('00:00:00');
                return;
            }
            const optMode = $('input[name="opt-mode"]:checked').val() || 'hour';
            let Ki;
            if (optMode === 'run') {
                Ki = perRunGreedy(totalCap, rList, Tmax, DF);
            } else if (optMode === 'equal') {
                Ki = equalDurationDistribution(totalCap, rList, Tmax, DF);
            } else {
                Ki = Tmax ? perHourWithCaps(totalCap, rList, Tmax, DF) : perHourHillClimbSimple(totalCap, rList, DF);
            }
            const allocation = allocateUnitsToLevels(Ki, inputs, activeLevels, caps);
            const effectiveKi = $.map(activeLevels, (L, i) => {
                return Object.keys(allocation[L]).reduce((s, u) => s + (allocation[L][u] || 0) * (caps[u] || 0), 0);
            });
            const Gi = effectiveKi.map((k, i) => k * rList[i]);
            const Ti = effectiveKi.map((k, i) => T_of_K(k, rList[i], DF));
            const Hi = Ti.map((t, i) => (t > 0 ? (Gi[i] / t) : 0) * 3600);
            const unitCols = $('#scavTable tbody tr').map(function () {
                return $(this).data('row');
            }).get();
            renderAllocationsToTable(activeLevels, unitCols, allocation);
            ['FF', 'BB', 'SS', 'RR'].forEach((L) => {
                const idx = activeLevels.indexOf(L);
                const icap = (idx >= 0 ? Ki[idx] : 0);
                const gi = (idx >= 0 ? Gi[idx] : 0);
                const hi = (idx >= 0 ? Hi[idx] : 0);
                const ti = (idx >= 0 ? Ti[idx] : 0);
                $('.foot-' + L + '-icap').text(fmtInt(icap));
                $('.foot-' + L + '-run').text(fmtFix(gi));
                $('.foot-' + L + '-hour').text(fmtFix(hi));
                $('.foot-' + L + '-time').text(ti ? sekinzeit(ti) : '00:00:00');
            });
        }

        $(function () {
            $(document).on('input', '.unit-available', calculate);
            $('.scv-chk, .opt-chk, #max-h, #max-m').on('change', calculate);
            calculate();
        });
    </script>
@endpush
