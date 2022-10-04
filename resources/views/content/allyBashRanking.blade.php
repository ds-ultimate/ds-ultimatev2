@extends('layouts.app')

@section('titel', ucfirst(__('ui.titel.ally')).': '.\App\Util\BasicFunctions::decodeName($allyData->name))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('ui.titel.ally')).': '}}{!!
                \App\Util\BasicFunctions::linkAlly($worldData, $allyData->allyID, \App\Util\BasicFunctions::outputName($allyData->name) 
                . " [" . \App\Util\BasicFunctions::outputName($allyData->tag) . "]")!!}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('ui.titel.ally')).': ' }}
            </h1>
            <h4>
                {!! \App\Util\BasicFunctions::linkAlly($worldData, $allyData->allyID, \App\Util\BasicFunctions::outputName($allyData->name) 
                    . " [" . \App\Util\BasicFunctions::outputName($allyData->tag) . "]")!!}
            </h4>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Datachart Spieler -->
        <div class="col-12 mt-2">
            <div class="card">
                <div class="card-body cust-responsive">
                    <table id="table_id" class="table table-hover table-sm w-100 nowrap">
                        <thead><tr>
                            <th>{{ ucfirst(__('ui.table.rank')) }}</th>
                            <th>{{ ucfirst(__('ui.table.name')) }}</th>
                            <th>{{ ucfirst(__('ui.table.bashGes')) }}</th>
                            <th>{{ ucfirst(__('ui.table.bashOff')) }}</th>
                            <th>{{ ucfirst(__('ui.table.bashDeff')) }}</th>
                            <th>{{ ucfirst(__('ui.table.bashSup')) }}</th>
                            <th>{{ ucfirst(__('ui.table.allyKillsPercent')) }}</th>
                            <th>{{ ucfirst(__('ui.table.playerPointPercent')) }}</th>
                        </tr></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- ENDE Datachart Spieler -->
    </div>
@endsection

@push('js')
    <script>
        $(document).ready( function () {
            $('#table_id').DataTable({
                "columnDefs": [
		    {"targets": 0, 'searchable': false, 'orderable': false},
                    {"targets": 3, "className": 'text-right'},
                    {"targets": 4, "className": 'text-right'},
                    {"targets": 5, "className": 'text-right'},
                    {"targets": 6, "className": 'text-right', 'orderable': false},
                    {"targets": 7, "className": 'text-right', 'orderable': false},
                ],
                dom: "<'row'<'col-sm-12 col-md-6'<'d-inline-flex mr-2'B><'d-inline-flex'l>><'col-sm-12 col-md-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [
                    'copy', 'csv', 'print'
                ],
                "order": [[ 2, "desc" ]],
                "pageLength": 100,
                "ajax": "{{ route('api.allyPlayerBashRanking', [$worldData->id, $allyData->allyID]) }}",
                "columns": [
                    { "data": "DT_RowIndex" },
                    { "data": "name", "render": function (value, type, row) {return "<a href='{{ route('world', [$worldData->server->code, $worldData->name]) }}/player/"+ row.playerID +"'>"+ value +'</a>'}},
                    { "data": "gesBash"},
                    { "data": "offBash"},
                    { "data": "defBash"},
                    { "data": "supBash"},
                    { "data": "allyKillsPercent"},
                    { "data": "playerPointPercent"},
                ],
                "drawCallback": function(settings, json) {
                    $('[data-toggle="popover"]').popover({
                        html : true,
                    });
                },
                stateSave: true,
                customName: "allyPlayerBashRanking",
                {!! \App\Util\Datatable::language() !!}
            });
        } );
    </script>
@endpush
