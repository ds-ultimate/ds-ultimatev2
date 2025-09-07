@extends('layouts.app')

@section('titel', $worldData->getDistplayName().': '.__('tool.watchtowerPlanner.title'))

@section('content')
    <div class="row justify-content-center">

        <div class="col-12 p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">
                {{ __('tool.watchtowerPlanner.title').' ['.$worldData->getDistplayName().']' }}
            </h1>
        </div>
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">{{ __('tool.watchtowerPlanner.title') }}</h1>
            <h4>{{ '['.$worldData->getDistplayName().']' }}</h4>
        </div>

        <div class="col-12 mt-2">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3 pb-2">
                        <div class="col-12">
                            <h5 class="font-weight-normal text-muted">
                                {{ __('tool.watchtowerPlanner.textareaHelp') }}
                            </h5>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-lg-6">
                            <label for="attackers" class="form-label fw-bold">{{ __('tool.watchtowerPlanner.attackersLabel') }}</label>
                            <textarea id="attackers" class="form-control" rows="8" placeholder="{{ __('tool.watchtowerPlanner.textareaPlaceholder') }}"></textarea>
                            <div id="atk-info" class="small mt-1 text-muted"></div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <label for="defenders" class="form-label fw-bold">{{ __('tool.watchtowerPlanner.defendersLabel') }}</label>
                            <textarea id="defenders" class="form-control" rows="8" placeholder="{{ __('tool.watchtowerPlanner.textareaPlaceholder') }}"></textarea>
                            <div id="def-info" class="small mt-1 text-muted"></div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-2">
                            <thead>
                            <tr>
                                <th style="width:110px">{{ __('tool.watchtowerPlanner.towers.header.x') }}</th>
                                <th style="width:110px">{{ __('tool.watchtowerPlanner.towers.header.y') }}</th>
                                <th style="width:160px">{{ __('tool.watchtowerPlanner.towers.header.level') }}</th>
                                <th style="width:140px">{{ __('tool.watchtowerPlanner.towers.header.radius') }}</th>
                                <th style="width:80px"></th>
                            </tr>
                            </thead>
                            <tbody id="towers-body">
                            <tr class="d-none tower-row-template">
                                <td><input type="text" inputmode="numeric" class="form-control form-control-sm tw-x" placeholder="{{ __('tool.watchtowerPlanner.placeholder.x') }}"></td>
                                <td><input type="number" min="0" max="999" class="form-control form-control-sm tw-y" placeholder="{{ __('tool.watchtowerPlanner.placeholder.y') }}"></td>
                                <td>
                                    <select class="form-select form-select-sm tw-lv">
                                        @for($i=1;$i<=20;$i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </td>
                                <td><span class="tw-rad">1.1</span></td>
                                <td class="text-center">
                                    <button type="button" class="btn btm-sm btn-danger btn-del-tower" title="{{ __('tool.watchtowerPlanner.actions.removeRow') }}">X</button>
                                </td>
                            </tr>
                            <tr class="tower-row">
                                <td><input type="text" inputmode="numeric" class="form-control form-control-sm tw-x" placeholder="{{ __('tool.watchtowerPlanner.placeholder.x') }}"></td>
                                <td><input type="number" min="0" max="999" class="form-control form-control-sm tw-y" placeholder="{{ __('tool.watchtowerPlanner.placeholder.y') }}"></td>
                                <td>
                                    <select class="form-select form-select-sm tw-lv">
                                        @for($i=1;$i<=20;$i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </td>
                                <td><span class="tw-rad">1.1</span></td>
                                <td class="text-center">
                                    <button type="button" class="btn btm-sm btn-danger btn-del-tower" title="{{ __('tool.watchtowerPlanner.actions.removeRow') }}">X</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" id="btn-add-tower">{{ __('tool.watchtowerPlanner.actions.addTower') }}</button>

                    <div class="mt-4 d-flex">
                        <button type="button" class="btn btn-success" id="btn-run">{{ __('tool.watchtowerPlanner.buttons.calculate') }}</button>
                        <button type="button" class="btn btn-danger ml-2" id="btn-clear">{{ __('tool.watchtowerPlanner.buttons.clear') }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 mt-3 mb-4" id="results-card" style="display:none;">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('tool.watchtowerPlanner.results.title') }}</h5>
                    <div id="res-allowed"></div>
                    <div id="res-warnings" class="text-danger small mt-2"></div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('js')
    <script>
        const WT_R = {
            1:1.1,2:1.3,3:1.5,4:1.7,5:2.0,6:2.3,7:2.6,8:3.0,9:3.4,10:3.9,
            11:4.4,12:5.1,13:5.8,14:6.7,15:7.6,16:8.7,17:10.0,18:11.5,19:13.1,20:15.0
        };

        const sq = function(x){ return x*x; };
        const clamp01 = function(t){ return Math.max(0, Math.min(1, t)); };

        function parseVillages(text) {
            const lines = (text || '').split(/\r?\n/);
            const out = [];
            const seen = {};
            const REG = /(\d{1,3})\s*\|\s*(\d{1,3})/g;
            for (let i = 0; i < lines.length; i++) {
                const line = lines[i];
                let m;
                while ((m = REG.exec(line)) !== null) {
                    const x = Number(m[1]), y = Number(m[2]);
                    if (Number.isFinite(x) && Number.isFinite(y) && x>=0 && x<=999 && y>=0 && y<=999) {
                        const key = x+'|'+y;
                        if (!seen[key]) { seen[key] = 1; out.push({x:x,y:y,key:key}); }
                    }
                }
                REG.lastIndex = 0;
            }
            return out;
        }

        function parseTowers() {
            const towers = [];
            $('#towers-body tr.tower-row').each(function(){
                const $row = $(this);
                const x = Number($row.find('.tw-x').val());
                const y = Number($row.find('.tw-y').val());
                const lv = Number($row.find('.tw-lv').val());
                if (!Number.isFinite(x) || !Number.isFinite(y) || !Number.isFinite(lv) || x === 0 || y === 0) return;
                if (x<0||x>999||y<0||y>999||lv<1||lv>20) return;
                const r = WT_R[lv] || 0;
                towers.push({x:x, y:y, lv:lv, r:r, r2:r*r});
            });
            return towers;
        }

        function updateTowerRowRadius($row){
            const lv = Number($row.find('.tw-lv').val());
            const r = WT_R[lv] || 0;
            $row.find('.tw-rad').text(r.toFixed(1));
        }

        function dist2(a,b){ return sq(a.x-b.x) + sq(a.y-b.y); }

        function circleBlocksPoint(C, r2, P) {
            return dist2(C, P) <= r2;
        }

        function bboxPrefilter(P, Q, C, r) {
            const minx = Math.min(P.x, Q.x) - r;
            const maxx = Math.max(P.x, Q.x) + r;
            const miny = Math.min(P.y, Q.y) - r;
            const maxy = Math.max(P.y, Q.y) + r;
            return (C.x >= minx && C.x <= maxx && C.y >= miny && C.y <= maxy);
        }

        function segmentCircleBlocked(P, Q, C, r2) {
            const vx = Q.x - P.x, vy = Q.y - P.y;
            const wx = C.x - P.x, wy = C.y - P.y;
            const vv = vx*vx + vy*vy;
            if (vv === 0) {
                return (wx*wx + wy*wy) <= r2;
            }
            var t = (wx*vx + wy*vy) / vv;
            t = clamp01(t);
            const nx = P.x + t*vx, ny = P.y + t*vy;
            const dx = nx - C.x, dy = ny - C.y;
            return (dx*dx + dy*dy) <= r2;
        }

        function nearestDist2Seg(P, Q, C) {
            const vx = Q.x - P.x, vy = Q.y - P.y;
            const wx = C.x - P.x, wy = C.y - P.y;
            const vv = vx*vx + vy*vy;
            if (vv === 0) return wx*wx + wy*wy;
            var t = (wx*vx + wy*vy) / vv;
            t = clamp01(t);
            const nx = P.x + t*vx, ny = P.y + t*vy;
            const dx = nx - C.x, dy = ny - C.y;
            return dx*dx + dy*dy;
        }

        function computePairs(attackers, defenders, towers) {
            const allowedByDef = {};
            const suggestByDef = {};
            $.each(defenders, function(_, D){ allowedByDef[D.key] = []; suggestByDef[D.key] = []; });

            const noTowers = (towers.length === 0);

            $.each(defenders, function(_, D){
                $.each(attackers, function(__, A){
                    var minClr = Infinity;
                    var hit = false;

                    $.each(towers, function(___, T){
                        if (!bboxPrefilter(A, D, T, T.r)) return;
                        const d2 = nearestDist2Seg(A, D, T);
                        const clr = Math.sqrt(d2) - T.r;
                        if (clr < minClr) minClr = clr;
                        if (d2 <= T.r2) hit = true;
                    });

                    if (noTowers) {
                        allowedByDef[D.key].push({k:A.key, c:Infinity});
                    } else if (!hit && minClr > 0) {
                        allowedByDef[D.key].push({k:A.key, c:minClr});
                    } else {
                        suggestByDef[D.key].push({k:A.key, c:(isFinite(minClr)?minClr:-Infinity)});
                    }
                });
            });

            return {allowedByDef: allowedByDef, suggestByDef: suggestByDef};
        }

        function renderResults(attackers, defenders, res) {
            var $allowed = $('#res-allowed').empty();

            var lblDef = '{{ __('tool.watchtowerPlanner.defenderVillage') }}';
            var lblAtk = '{{ __('tool.watchtowerPlanner.attackerVillage') }}';

            var $frag = $();
            $.each(defenders, function(_, D){
                var arrAllowed = res.allowedByDef[D.key] || [];
                var arrSuggest = res.suggestByDef[D.key] || [];

                if (arrAllowed.length && typeof arrAllowed[0] === 'object' && 'c' in arrAllowed[0]) {
                    arrAllowed.sort(function(a,b){ return (b.c - a.c); });
                }
                if (arrSuggest.length && typeof arrSuggest[0] === 'object' && 'c' in arrSuggest[0]) {
                    arrSuggest.sort(function(a,b){ return (b.c - a.c); });
                }

                var $wrap = $('<div/>', { 'class': 'mb-3 pb-2 border-bottom' });

                var $lineDef = $('<div/>')
                    .append($('<strong/>', { text: lblDef + ': ' }))
                    .append(document.createTextNode(D.key));

                $wrap.append($lineDef);

                if (arrAllowed.length > 0) {
                    var listAllowed = arrAllowed.map(function(it){ return it.k; }).join(' ');
                    var $lineAtk = $('<div/>')
                        .append($('<strong/>', { text: lblAtk + ': ' }))
                        .append(document.createTextNode(listAllowed));
                    $wrap.append($lineAtk);
                } else {
                    var warnText  = '{{ __('tool.watchtowerPlanner.results.noAllowedWarn') }}';
                    var suggLabel = '{{ __('tool.watchtowerPlanner.results.suggestionsLabel') }}';

                    var $warn = $('<div/>', { 'class':'text-danger mb-1' }).text(warnText);
                    $wrap.append($warn);

                    var top5 = arrSuggest.slice(0, 5).map(function(it){ return it.k; }).join(' ');
                    var $lineSug = $('<div/>')
                        .append($('<strong/>', { text: suggLabel + ' ' }))
                        .append(document.createTextNode(top5 || '—'));
                    $wrap.append($lineSug);
                }

                $frag = $frag.add($wrap);
            });

            $allowed.append($frag);
            $('#results-card').show();
        }

        function addTowerRow() {
            var newElm = $('.tower-row-template').clone()
            newElm.removeClass("tower-row-template").removeClass("d-none").addClass("tower-row")
            $('#towers-body').append(newElm);
            updateTowerRowRadius(newElm);
        }

        $(function(){
            updateTowerRowRadius($('#towers-body tr.tower-row').first());

            $('#btn-add-tower').on('click', function(){ addTowerRow(); });
            $(document).on('change', '.tw-lv', function(){ updateTowerRowRadius($(this).closest('tr.tower-row')); });

            $(document).on('input', '.tw-x', function(){
                var $x = $(this);
                var $row = $x.closest('tr.tower-row');
                var $y = $row.find('.tw-y');
                var val = ($x.val() || '').toString();
                var m = val.match(/^\s*(\d{1,3})\s*\|\s*(\d{1,3})\s*$/);
                if (m) {
                    var xi = Math.max(0, Math.min(999, parseInt(m[1],10)));
                    var yi = Math.max(0, Math.min(999, parseInt(m[2],10)));
                    $x.val(xi);
                    $y.val(yi);
                    $y.focus().select();
                    return;
                }
                var digits = val.replace(/\D/g,'');
                if (digits.length >= 3) {
                    var xi2 = Math.max(0, Math.min(999, parseInt(digits.slice(0,3),10)));
                    $x.val(xi2);
                    $y.focus().select();
                }
            });

            $(document).on('click', '.btn-del-tower', function(){
                var $rows = $('#towers-body tr.tower-row');
                if ($rows.length <= 1) {
                    var $r = $rows.first();
                    $r.find('.tw-x,.tw-y').val('');
                    $r.find('.tw-lv').val('1').trigger('change');
                    return;
                }
                $(this).closest('tr.tower-row').remove();
            });

            $('#btn-clear').on('click', function(){
                $('#attackers,#defenders').val('');
                $('#atk-info,#def-info').text('');
                $('#towers-body tr.tower-row').not(':first').remove();
                var $r = $('#towers-body tr.tower-row').first();
                $r.find('.tw-x,.tw-y').val('');
                $r.find('.tw-lv').val('1').trigger('change');
                $('#results-card').hide();
                $('#res-allowed,#res-warnings').empty();
            });

            $('#btn-run').on('click', function(){
                $('#res-allowed').empty();
                $('#res-warnings').empty();

                var attackers = parseVillages($('#attackers').val());
                var defenders = parseVillages($('#defenders').val());
                var towers    = parseTowers();

                var warns = [];
                if (!attackers.length) warns.push('{{ __('tool.watchtowerPlanner.warnings.noAttackers') }}');
                if (!defenders.length) warns.push('{{ __('tool.watchtowerPlanner.warnings.noDefenders') }}');
                if (!towers.length)    warns.push('{{ __('tool.watchtowerPlanner.warnings.noTowers') }}');

                if (warns.length) {
                    var $list = $();
                    $.each(warns, function(_, w){ $list = $list.add($('<div/>').text('• '+w)); });
                    $('#res-warnings').append($list);
                    $('#results-card').show();
                    return;
                }

                var res = computePairs(attackers, defenders, towers);
                renderResults(attackers, defenders, res);
            });
        });
    </script>
@endpush
