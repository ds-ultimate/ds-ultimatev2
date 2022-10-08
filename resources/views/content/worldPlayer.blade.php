@extends('layouts.app')

@section('titel', $worldData->getDistplayName().': '.__('ui.titel.overview').' '.__('ui.titel.player'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="col-md-5 p-lg-5 mx-auto my-1 text-center">
                <h1 class="font-weight-normal">{{ $worldData->getDistplayName() }}<br>{{ __('ui.tabletitel.overview').' '.__('ui.tabletitel.player') }}</h1>
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
    <script>
        $(document).ready( function () {
            $('#table_id').DataTable({
                "columnDefs": [
                    {"targets": [3, 4, 6, 7, 8, 9], "className": 'text-right'},
                    {"targets": [3, 4, 6, 7, 8, 9], "orderSequence": ["desc", "asc"]},
                ],
                "processing": true,
                "serverSide": true,
                responsive: true,
                "ajax": "{{ route('api.worldPlayer', [$worldData->id]) }}",
                "columns": [
                    { "data": "rank" },
                    { "data": "name", "render": function (value, type, row) {return "<a href='" + ("{{ route('player', [$worldData->server->code, $worldData->name, "%playerID%"]) }}".replace("%playerID%", row.playerID)) +"'>"+ value +'</a>'}},
                    { "data": "ally", "name": "ally_id", "render": function (value, type, row) {if (value != "-"){return "<a href='" + ("{{ route('ally', [$worldData->server->code, $worldData->name, "%allyID%"]) }}".replace("%allyID%", row.ally_id)) +"'>"+ row.ally +'</a>'}else{return value}}},
                    { "data": "points", "render": function (value) {return numeral(value).format('0.[00] a')}},
                    { "data": "village_count", "render": function (value) {return numeral(value).format('0,0')}},
                    { "data": "village_points", "render": function (value) {return numeral(value).format('0,0')}, "orderable": false},
                    { "data": "gesBash" , "render": function (value) {return numeral(value).format('0.[00] a')}},
                    { "data": "offBash", "render": function (value) {return numeral(value).format('0.[00] a')} },
                    { "data": "defBash", "render": function (value) {return numeral(value).format('0.[00] a')} },
                    { "data": "supBash", "render": function (value) {return numeral(value).format('0.[00] a')}},
                ],
                stateSave: true,
                customName: "worldPlayer",
                {!! \App\Util\Datatable::language() !!}
            });
        } );
    </script>
@endpush
