@extends('layouts.app')

@section('titel', $worldData->getDistplayName().': '.__('ui.server.ranking').' '.__('ui.titel.player'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="col-md-5 p-lg-5 mx-auto my-1 text-center">
                <h1 class="font-weight-normal">{{ $worldData->getDistplayName() }}<br>{{ __('ui.server.ranking').' '.__('ui.tabletitel.player') }}</h1>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body cust-responsive">
                    <table id="table_id" class="table table-striped table-hover table-sm w-100 nowrap">
                        <thead>
                        <tr class="d-none d-lg-table-row">
                            <th colspan="6">{{ ucfirst(__('ui.tabletitel.general')) }}</th>
                            <th colspan="4">{{ ucfirst(__('ui.tabletitel.bashStats')) }}</th>
                        </tr>
                        <tr>
                            <th class="all">{{ ucfirst(__('ui.table.rank')) }}</th>
                            <th class="all">{{ ucfirst(__('ui.table.name')) }}</th>
                            <th class="all">{{ ucfirst(__('ui.table.ally')) }}</th>
                            <th class="all">{{ ucfirst(__('ui.table.points')) }}</th>
                            <th class="all">{{ ucfirst(__('ui.table.villages')) }}</th>
                            <th class="tablet-l desktop">{{ ucfirst(__('ui.table.avgVillage')) }}</th>
                            <th class="desktop">{{ ucfirst(__('ui.table.bashGes')) }}</th>
                            <th class="desktop">{{ ucfirst(__('ui.table.bashOff')) }}</th>
                            <th class="desktop">{{ ucfirst(__('ui.table.bashDeff')) }}</th>
                            <th class="desktop">{{ ucfirst(__('ui.table.bashSup')) }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ \App\Util\BasicFunctions::asset('plugin/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script>
        $(document).ready( function () {
            var table = $('#table_id').DataTable({
                "columnDefs": [
                    {"targets": [3, 4, 6, 7, 8, 9], "className": 'text-right'},
                    {"targets": [3, 4, 6, 7, 8, 9], "orderSequence": ["desc", "asc"]},
                ],
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('api.worldPlayerHistory', [$worldData->id, \Illuminate\Support\Carbon::now()->subDay()->toDateString()]) }}",
                "columns": [
                    { "data": "rank" },
                    { "data": "name", "render": function (value, type, row) {return "<a href='" + ("{{ route('player', [$worldData->server->code, $worldData->name, "%playerID%"]) }}".replace("%playerID%", row.playerID)) +"'>"+ value +'</a>'}},
                    { "data": "ally", "name": "ally_id", "render": function (value, type, row) {if (value != "-"){return "<a href='" + ("{{ route('ally', [$worldData->server->code, $worldData->name, "%allyID%"]) }}".replace("%allyID%", row.ally_id)) +"'>"+ row.ally +'</a>'}else{return value}}},
                    { "data": "points"},
                    { "data": "village_count"},
                    { "data": "village_points", "orderable": false},
                    { "data": "gesBash"},
                    { "data": "offBash"},
                    { "data": "defBash"},
                    { "data": "supBash"},
                ],
                responsive: true,
                "drawCallback": function(settings, json) {
                    $('[data-toggle="popover"]').popover({
                        html : true,
                    });
                    $("#date_picker").datepicker({
                        language: 'all',
                        format:'yyyy-mm-dd',
                        startDate:'{{ \Illuminate\Support\Carbon::now()->subDays(config('dsUltimate.db_save_day'))->toDateString() }}',
                        endDate:'{{ \Illuminate\Support\Carbon::now()->subDay()->toDateString() }}',
                        weekStart:1,
                    })
                },
                stateSave: true,
                customName: "worldPlayerHistory",
                {!! \App\Util\Datatable::language() !!}
            });

            $.fn.datepicker.dates['all'] = {
                days: ["{{ __('datepicker.Sunday') }}", "{{ __('datepicker.Monday') }}", "{{ __('datepicker.Tuesday') }}", "{{ __('datepicker.Wednesday') }}", "{{ __('datepicker.Thursday') }}", "{{ __('datepicker.Friday') }}", "{{ __('datepicker.Saturday') }}"],
                daysShort: ["{{ __('datepicker.Sun') }}", "{{ __('datepicker.Mon') }}", "{{ __('datepicker.Tue') }}", "{{ __('datepicker.Wed') }}", "{{ __('datepicker.Thu') }}", "{{ __('datepicker.Fri') }}", "{{ __('datepicker.Sat') }}"],
                daysMin: ["{{ __('datepicker.Su') }}", "{{ __('datepicker.Mo') }}", "{{ __('datepicker.Tu') }}", "{{ __('datepicker.We') }}", "{{ __('datepicker.Th') }}", "{{ __('datepicker.Fr') }}", "{{ __('datepicker.Sa') }}"],
                months: ["{{ __('datepicker.January') }}", "{{ __('datepicker.February') }}", "{{ __('datepicker.March') }}", "{{ __('datepicker.April') }}", "{{ __('datepicker.May') }}", "{{ __('datepicker.June') }}", "{{ __('datepicker.July') }}", "{{ __('datepicker.August') }}", "{{ __('datepicker.September') }}", "{{ __('datepicker.October') }}", "{{ __('datepicker.November') }}", "{{ __('datepicker.December') }}"],
                monthsShort: ["{{ __('datepicker.Jan') }}", "{{ __('datepicker.Feb') }}", "{{ __('datepicker.Mar') }}", "{{ __('datepicker.Apr') }}", "{{ __('datepicker.May') }}", "{{ __('datepicker.Jun') }}", "{{ __('datepicker.Jul') }}", "{{ __('datepicker.Aug') }}", "{{ __('datepicker.Sep') }}", "{{ __('datepicker.Oct') }}", "{{ __('datepicker.Nov') }}", "{{ __('datepicker.Dec') }}"],
                today: "{{ __('datepicker.Today') }}",
                monthsTitle: "{{ __('datepicker.months') }}",
                clear: "{{ __('datepicker.Clear') }}",
            };

            $(document).on('change', '#date_picker', function (e) {
                $('[data-toggle="popover"]').popover('disable');
                table.ajax.url('{{ route('api.worldPlayerHistory', [$worldData->id, "%days%"]) }}'.replace("%days%", $(this).val())).load();
            });

            $('#table_id_wrapper .row:first').prepend(
                '<div class="col-4 d-none d-md-block"></div>' +
                '<div class="col-12 col-md-4">' +
                '<div class="form-inline">' +
                '<label class="control-label pr-3">{{ __('ui.table.date') }}: </label>' +
                '<input id="date_picker" class="form-control form-control-sm col-8" type="text" value="{{ \Illuminate\Support\Carbon::now()->subDay()->toDateString() }}" max="{{ \Illuminate\Support\Carbon::now()->subDay()->toDateString() }}" min="{{ \Illuminate\Support\Carbon::now()->subDays(config('dsUltimate.db_save_day'))->toDateString() }}" readonly>' +
                '</div></div>' +
                '<div class="col-4 d-none d-md-block"></div>');

        } );
    </script>
@endpush
