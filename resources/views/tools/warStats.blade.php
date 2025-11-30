@extends('layouts.app')

@section('titel', $worldData->getDistplayName().': '.__('tool.warStats.title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('tool.warStats.title')).' ['.$worldData->getDistplayName().']' }}
            </h1>
        </div>
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">{{ ucfirst(__('tool.warStats.title')) }}</h1>
            <h4>{{ '['.$worldData->getDistplayName().']' }}</h4>
        </div>

        <div class="col-12 mt-2">
            <div class="card">
                <div class="card-body">
                    <form id="war-form"
                          data-url="{{ route('tools.warStatsData', ['server' => request()->route('server'), 'world' => request()->route('world')]) }}">
                        <div class="row align-items-start">
                            <div class="col-12 col-md-4 mb-3 order-1 order-md-1">
                                <h5 class="mb-2">{{ __('tool.warStats.party1') }}</h5>
                                <label class="small d-block mb-1">{{ __('tool.warStats.party1') }}</label>
                                <select name="players1[]" class="select2-player mb-2" multiple></select>
                                <label class="small d-block mb-1">{{ __('tool.warStats.party1') }}</label>
                                <select name="tribes1[]" class="select2-ally" multiple></select>
                            </div>

                            <div class="col-12 col-md-4 mb-3 order-2 order-md-2">
                                <h5 class="mb-2">{{ __('tool.warStats.options') }}</h5>
                                <div class="form-group mb-2">
                                    <label class="small">{{ __('tool.warStats.bucket') }}</label>
                                    <select name="bucket" class="form-control">
                                        <option value="daily">{{ __('tool.warStats.daily') }}</option>
                                        <option value="hourly">{{ __('tool.warStats.hourly') }}</option>
                                        <option value="weekly">{{ __('tool.warStats.weekly') }}</option>
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label class="small">{{ __('tool.warStats.timeSpan') }}</label>
                                    <select id="timeperiod" class="form-control">
                                        <option value="86400">{{ __('tool.warStats.last24hours') }}</option>
                                        <option value="172800">{{ __('tool.warStats.last48hours') }}</option>
                                        <option value="604800">{{ __('tool.warStats.lastWeek') }}</option>
                                        <option value="2629744">{{ __('tool.warStats.lastMonth') }}</option>
                                        <option value="7889231">{{ __('tool.warStats.last3Months') }}</option>
                                        <option value="999999999" selected="selected">{{ __('tool.warStats.forEver') }}</option>
                                        <option value="-1">{{ __('tool.warStats.manuell') }}</option>
                                    </select>
                                </div>
                                <div class="manual-time d-none">
                                    <div class="form-row">
                                        <div class="col-12 col-sm-6 mb-2">
                                            <label class="small">{{ __('tool.warStats.from') }}</label>
                                            <input type="datetime-local" name="from" id="from-input" class="form-control" />
                                        </div>
                                        <div class="col-12 col-sm-6 mb-2">
                                            <label class="small">{{ __('tool.warStats.to') }}</label>
                                            <input type="datetime-local" name="to" id="to-input" class="form-control" />
                                        </div>
                                    </div>
                                </div>

                                <button type="button" id="war-submit" class="btn btn-primary w-100 mt-1">
                                    {{ __('tool.warStats.generate') }}
                                </button>

                                <div id="war-error" class="alert alert-danger py-1 px-2 mt-2 d-none"></div>
                            </div>

                            <div class="col-12 col-md-4 mb-3 order-3 order-md-3">
                                <h5 class="mb-2">{{ __('tool.warStats.party2') }}</h5>
                                <label class="small d-block mb-1">{{ __('tool.warStats.party2') }}</label>
                                <select name="players2[]" class="select2-player mb-2" multiple></select>
                                <label class="small d-block mb-1">{{ __('tool.warStats.party2') }}</label>
                                <select name="tribes2[]" class="select2-ally" multiple></select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 mt-2">
            <div class="card">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-6" id="war-kpi-p1">
                            <div>{{ __('tool.warStats.totalConquers') }} <span id="kpi-p1-conquers">0</span></div>
                            <div>{{ __('tool.warStats.enemyConquers') }} <span id="kpi-p1-enemy">0</span></div>
                            <div>{{ __('tool.warStats.attBash') }} <span id="kpi-p1-attBash">0</span></div>
                            <div>{{ __('tool.warStats.defBash') }} <span id="kpi-p1-defBash">0</span></div>
                        </div>
                        <div class="col-md-6" id="war-kpi-p2">
                            <div>{{ __('tool.warStats.totalConquers') }} <span id="kpi-p2-conquers">0</span></div>
                            <div>{{ __('tool.warStats.enemyConquers') }} <span id="kpi-p2-enemy">0</span></div>
                            <div>{{ __('tool.warStats.attBash') }} <span id="kpi-p2-attBash">0</span></div>
                            <div>{{ __('tool.warStats.defBash') }} <span id="kpi-p2-defBash">0</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 mt-2">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-12 col-lg-8">
                            <div class="row">
                                <div class="col-12 col-md-6 mb-4">
                                    <h5 class="mb-2">{{ __('tool.warStats.conquers') }}</h5>
                                    <canvas id="gainChart"></canvas>
                                </div>
                                <div class="col-12 col-md-6 mb-4">
                                    <h5 class="mb-2">{{ __('tool.warStats.enemyConquers') }}</h5>
                                    <canvas id="enemyChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12 col-lg-8">
                            <div class="row">
                                <div class="col-12 col-md-6 mb-4">
                                    <h5 class="mb-2">{{ __('tool.warStats.bashPoints') }}</h5>
                                    <canvas id="bashChart"></canvas>
                                </div>
                                <div class="col-12 col-md-6 mb-0">
                                    <h5 class="mb-2">{{ __('tool.warStats.pointProgression') }}</h5>
                                    <canvas id="pointChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script src="{{ \App\Util\BasicFunctions::asset('plugin/select2/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(function () {
            function lineScaleWithSparseTicks() {
                return {
                    ticks: {
                        autoSkip: false,
                        callback: function(value, index) {
                            const labels = this.chart.data.labels || [];
                            const total  = labels.length;
                            if (total < 5) return this.getLabelForValue(value);
                            if (index === 0 || index === total - 1 || index % 5 === 0) {
                                return this.getLabelForValue(value);
                            }
                            return '';
                        }
                    }
                };
            }

            let warErrorTimer = null;
            function showWarError(msg) {
                const box = $('#war-error');
                box.text(msg || '{{ __('tool.warStats.unknownMistake') }}').removeClass('d-none');
                if (warErrorTimer) { clearTimeout(warErrorTimer); }
                warErrorTimer = setTimeout(function () {
                    box.addClass('d-none').text('');
                }, 10000);
            }
            function clearWarError() {
                const box = $('#war-error');
                if (warErrorTimer) { clearTimeout(warErrorTimer); warErrorTimer = null; }
                box.addClass('d-none').text('');
            }

            var gainChart = new Chart($('#gainChart'), {
                type: 'line',
                data: { labels: [], datasets: [
                        {label: @json(__('tool.warStats.party1')), data: []},
                        {label: @json(__('tool.warStats.party2')), data: []}
                    ]},
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: { x: lineScaleWithSparseTicks() }
                }
            });

            var enemyChart = new Chart($('#enemyChart'), {
                type: 'line',
                data: { labels: [], datasets: [
                        {label: '{{ __('tool.warStats.party1') }}', data: []},
                        {label: '{{ __('tool.warStats.party2') }}', data: []}
                    ]},
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: { x: lineScaleWithSparseTicks() }
                }
            });

            var bashChart = new Chart($('#bashChart'), {
                type: 'bar',
                data: { labels: ['{{ __('tool.warStats.attBash') }}', '{{ __('tool.warStats.defBash') }}'], datasets: [
                        {label: '{{ __('tool.warStats.party1') }}', data: []},
                        {label: '{{ __('tool.warStats.party2') }}', data: []}
                    ]},
                options: { responsive: true, maintainAspectRatio: true, scales:{ y:{ beginAtZero:true } } }
            });

            var pointChart = new Chart($('#pointChart'), {
                type: 'line',
                data: { labels: [], datasets: [
                        {label: '{{ __('tool.warStats.party1') }}', data: []},
                        {label: '{{ __('tool.warStats.party2') }}', data: []}
                    ]},
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: { x: lineScaleWithSparseTicks() }
                }
            });

            $('.select2-player').select2({
                ajax: {
                    url: '{{ route('api.select2Player', [$worldData->id]) }}',
                    data: function (params) { return { search: params.term, page: params.page || 1 }; },
                    delay: 250
                },
                allowClear: true,
                placeholder: '{{ ucfirst(__('tool.map.playerSelectPlaceholder')) }}',
                theme: 'bootstrap4',
                width: '100%'
            }).on('select2:open', selectAutoFocus);

            $('.select2-ally').select2({
                ajax: {
                    url: '{{ route('api.select2Ally', [$worldData->id]) }}',
                    data: function (params) { return { search: params.term, page: params.page || 1 }; },
                    delay: 250
                },
                allowClear: true,
                placeholder: '{{ ucfirst(__('tool.map.allySelectPlaceholder')) }}',
                theme: 'bootstrap4',
                width: '100%'
            }).on('select2:open', selectAutoFocus);

            $('#timeperiod').on('change', handleTimeChange);
            handleTimeChange();

            $('#war-submit').on('click', function(){
                handleTimeChange();
                clearWarError();

                var form = $('#war-form');
                var data = {
                    players1: form.find('[name="players1[]"]').val() || [],
                    tribes1:  form.find('[name="tribes1[]"]').val()  || [],
                    players2: form.find('[name="players2[]"]').val() || [],
                    tribes2:  form.find('[name="tribes2[]"]').val()  || [],
                    from:     form.find('[name=from]').val(),
                    to:       form.find('[name=to]').val(),
                    bucket:   form.find('[name=bucket]').val()
                };

                $.getJSON(form.data('url'), data, function(resp){
                    $('#kpi-p1-conquers').text(resp.totals?.p1?.conquers_gain ?? 0);
                    $('#kpi-p1-enemy').text(resp.totals?.p1?.enemy_conquers ?? 0);
                    $('#kpi-p1-attBash').text(resp.totals?.p1?.attBash ?? 0);
                    $('#kpi-p1-defBash').text(resp.totals?.p1?.defBash ?? 0);
                    $('#kpi-p2-conquers').text(resp.totals?.p2?.conquers_gain ?? 0);
                    $('#kpi-p2-enemy').text(resp.totals?.p2?.enemy_conquers ?? 0);
                    $('#kpi-p2-attBash').text(resp.totals?.p2?.attBash ?? 0);
                    $('#kpi-p2-defBash').text(resp.totals?.p2?.defBash ?? 0);

                    gainChart.data.labels = resp.series?.labels ?? [];
                    gainChart.data.datasets[0].data = resp.series?.p1?.gain ?? [];
                    gainChart.data.datasets[1].data = resp.series?.p2?.gain ?? [];
                    gainChart.update();

                    enemyChart.data.labels = resp.series?.labels ?? [];
                    enemyChart.data.datasets[0].data = resp.series?.p1?.enemy ?? [];
                    enemyChart.data.datasets[1].data = resp.series?.p2?.enemy ?? [];
                    enemyChart.update();

                    bashChart.data.datasets[0].data = [resp.totals?.p1?.attBash ?? 0, resp.totals?.p1?.defBash ?? 0];
                    bashChart.data.datasets[1].data = [resp.totals?.p2?.attBash ?? 0, resp.totals?.p2?.defBash ?? 0];
                    bashChart.update();

                    pointChart.data.labels = resp.series?.labels ?? [];
                    pointChart.data.datasets[0].data = resp.series?.p1?.points ?? [];
                    pointChart.data.datasets[1].data = resp.series?.p2?.points ?? [];
                    pointChart.update();
                }).fail(function(xhr){
                    let msg = 'Error';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    showWarError(msg);
                });
            });

            function selectAutoFocus(e) {
                const selectId = e.target.id;
                $(".select2-search__field[aria-controls='select2-" + selectId + "-results']").each(function (_, el) { el.focus(); });
            }
            function handleTimeChange(){
                var val = $('#timeperiod').val();
                var fromField = $('#from-input');
                var toField = $('#to-input');
                if(val === '-1'){
                    $('.manual-time').removeClass('d-none');
                    var now = new Date().toISOString().slice(0,16);
                    if(!fromField.val()) fromField.val(now);
                    if(!toField.val()) toField.val(now);
                } else {
                    $('.manual-time').addClass('d-none');
                    if(val === '999999999'){
                        fromField.val('');
                        toField.val('');
                    } else {
                        var nowDate = new Date();
                        var fromDate = new Date(nowDate.getTime() - parseInt(val)*1000);
                        fromField.val(fromDate.toISOString().slice(0,16));
                        toField.val(nowDate.toISOString().slice(0,16));
                    }
                }
            }
        });
    </script>
@endpush
