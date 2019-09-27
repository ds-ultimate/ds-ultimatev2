@extends('layouts.temp')

@section('titel', $worldData->displayName().': '.__('ui.server.ranking').' '.__('ui.titel.player'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="col-md-5 p-lg-5 mx-auto my-1 text-center">
                <h1 class="font-weight-normal">{{ $worldData->displayName() }}<br>{{ __('ui.server.ranking').' '.__('ui.tabletitel.player') }}</h1>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="table_id" class="table table-striped table-hover table-sm w-100">
                        <thead>
                        <tr class="d-none d-md-table-row">
                            <th colspan="6">{{ ucfirst(__('ui.tabletitel.general')) }}</th>
                            <th colspan="4">{{ ucfirst(__('ui.tabletitel.bashStats')) }}</th>
                        </tr>
                        <tr>
                            <th>{{ ucfirst(__('ui.table.rank')) }}</th>
                            <th>{{ ucfirst(__('ui.table.name')) }}</th>
                            <th>{{ ucfirst(__('ui.table.ally')) }}</th>
                            <th>{{ ucfirst(__('ui.table.points')) }}</th>
                            <th>{{ ucfirst(__('ui.table.villages')) }}</th>
                            <th>{{ ucfirst(__('ui.table.avgVillage')) }}</th>
                            <th>{{ ucfirst(__('ui.table.bashGes')) }}</th>
                            <th>{{ ucfirst(__('ui.table.bashOff')) }}</th>
                            <th>{{ ucfirst(__('ui.table.bashDeff')) }}</th>
                            <th>{{ ucfirst(__('ui.table.bashUt')) }}</th>
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

@section('js')
    <script src="{{ asset('plugin/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script>
        $(document).ready( function () {
            var table = $('#table_id').DataTable({
                "columnDefs": [
                    {"targets": 3, "className": 'text-right'},
                    {"targets": 4, "className": 'text-right'},
                    {"targets": 5, "className": 'text-right'},
                    {"targets": 6, "className": 'text-right'},
                    {"targets": 7, "className": 'text-right'},
                    {"targets": 8, "className": 'text-right'},
                    {"targets": 9, "className": 'text-right'},
                ],
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('api.worldPlayerHistory', [$worldData->server->code, $worldData->name, \Illuminate\Support\Carbon::now()->subDay()->toDateString()]) }}",
                "columns": [
                    { "data": "rank" },
                    { "data": "name", "render": function (value, type, row) {return "<a href='{{ route('world', [$worldData->server->code, $worldData->name]) }}/player/"+ row.playerID +"'>"+ value +'</a>'}},
                    { "data": "ally", "render": function (value, type, row) {if (value != "-"){return "<a href='{{ route('world', [$worldData->server->code, $worldData->name]) }}/ally/"+ row.ally_id +"'>"+ value +'</a>'}else{return value}}, "orderable": false},
                    { "data": "points"},
                    { "data": "village_count"},
                    { "data": "village_points", "orderable": false},
                    { "data": "gesBash"},
                    { "data": "offBash"},
                    { "data": "defBash"},
                    { "data": "utBash", "orderable": false},
                ],
                responsive: true,
                "drawCallback": function(settings, json) {
                    $('[data-toggle="popover"]').popover({
                        html : true,
                    });
                    $("#date_picker").datepicker({
                        format:'yyyy-mm-dd',
                        startDate:'{{ \Illuminate\Support\Carbon::now()->subDays(config('dsUltimate.db_save_day'))->toDateString() }}',
                        endDate:'{{ \Illuminate\Support\Carbon::now()->subDay()->toDateString() }}',
                        weekStart:1,
                    })
                },
                {!! \App\Util\Datatable::language() !!}
            });

            $(document).on('change', '#date_picker', function (e) {
                $('[data-toggle="popover"]').popover('disable');
                table.ajax.url('{{ route('api.worldPlayer', [$worldData->server->code,$worldData->name]) }}/' + $(this).val()).load();
            });

            $('#table_id_wrapper').prepend('' +
                '<div class="row">' +
                '<div class="col-4"></div>' +
                '<div class="col-4">' +
                '<div class="form-inline">' +
                '<label class="control-label pr-3">Datum: </label>' +
                '<input id="date_picker" class="form-control form-control-sm col-8" type="text" value="{{ \Illuminate\Support\Carbon::now()->subDay()->toDateString() }}" max="{{ \Illuminate\Support\Carbon::now()->subDay()->toDateString() }}" min="{{ \Illuminate\Support\Carbon::now()->subDays(config('dsUltimate.db_save_day'))->toDateString() }}" readonly>' +
                '</div>' +
                '<div class="col-4">' +
                '</div>' +
                '</div>');

        } );
    </script>
@endsection
