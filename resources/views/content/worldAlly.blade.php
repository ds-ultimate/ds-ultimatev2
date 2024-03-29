@extends('layouts.app')

@section('titel', $worldData->getDistplayName().': '.__('ui.titel.overview').' '.__('ui.titel.ally'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="col-md-5 p-lg-5 mx-auto my-1 text-center">
                <h1 class="font-weight-normal">{{$worldData->getDistplayName() }}<br>{{ __('ui.tabletitel.allyRanking') }}</h1>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body cust-responsive">
                    <table id="table_id" class="table table-striped table-hover table-sm w-100 nowrap">
                        <thead>
                        <tr class="d-none d-lg-table-row">
                            <th colspan="7">{{ ucfirst(__('ui.tabletitel.general')) }}</th>
                            <th colspan="3">{{ ucfirst(__('ui.tabletitel.bashStats')) }}</th>
                        </tr>
                        <tr>
                            <th class="all">{{ ucfirst(__('ui.table.rank')) }}</th>
                            <th class="all">{{ ucfirst(__('ui.table.name')) }}</th>
                            <th class="all">{{ ucfirst(__('ui.table.tag')) }}</th>
                            <th class="all">{{ ucfirst(__('ui.table.points')) }}</th>
                            <th class="all">{{ ucfirst(__('ui.table.members')) }}</th>
                            <th class="all">{{ ucfirst(__('ui.table.villages')) }}</th>
                            <th class="tablet-l desktop">{{ ucfirst(__('ui.table.avgPlayer')) }}</th>
                            <th class="desktop">{{ ucfirst(__('ui.table.bashGes')) }}</th>
                            <th class="desktop">{{ ucfirst(__('ui.table.bashOff')) }}</th>
                            <th class="desktop">{{ ucfirst(__('ui.table.bashDeff')) }}</th>
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
                    {"targets": [3, 4, 5, 7, 8, 9], "orderSequence": ["desc", "asc"]},
                ],
                "processing": true,
                "serverSide": true,
                responsive: true,
                "ajax": "{{ route('api.worldAlly', [$worldData->id]) }}",
                "columns": [
                    { "data": "rank" },
                    { "data": "name", "render": function (value, type, row) {return "<a href='" + ("{{ route('ally', [$worldData->server->code, $worldData->name, "%allyID%"]) }}".replace("%allyID%", row.allyID)) +"'>"+ value +'</a>' }},
                    { "data": "tag", "render": function (value, type, row) {return "<a href='" + ("{{ route('ally', [$worldData->server->code, $worldData->name, "%allyID%"]) }}".replace("%allyID%", row.allyID)) +"'>"+ value +'</a>' }},
                    { "data": "points", "render": function (value) {return numeral(value).format('0.[00] a')}},
                    { "data": "member_count", "render": function (value) {return numeral(value).format('0,0')}},
                    { "data": "village_count", "render": function (value) {return numeral(value).format('0,0')}},
                    { "data": "player_points", "render": function (value) {return numeral(value).format('0.[00] a')}, "orderable": false},
                    { "data": "gesBash" , "render": function (value) {return numeral(value).format('0.[00] a')}},
                    { "data": "offBash", "render": function (value) {return numeral(value).format('0.[00] a')} },
                    { "data": "defBash", "render": function (value) {return numeral(value).format('0.[00] a')} },
                ],
                stateSave: true,
                customName: "worldAlly",
                {!! \App\Util\Datatable::language() !!}
            });
        } );
    </script>
@endpush
