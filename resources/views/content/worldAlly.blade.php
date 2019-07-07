@extends('layouts.temp')

@section('titel', $worldData->displayName().': '.__('Übersicht Stämme'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="col-md-5 p-lg-5 mx-auto my-1 text-center">
                <h1 class="font-weight-normal">{{$worldData->displayName() }}<br>{{ __('Übersicht Stämme') }}</h1>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="table_id" class="table table-striped table-hover table-sm w-100">
                        <thead>
                        <tr class="d-none d-md-table-row">
                            <th colspan="7">{{ ucfirst(__('Allgemein')) }}</th>
                            <th colspan="3">{{ ucfirst(__('Besiegte Gegner')) }}</th>
                        </tr>
                        <tr>
                            <th>{{ ucfirst(__('Rang')) }}</th>
                            <th>{{ ucfirst(__('Name')) }}</th>
                            <th>{{ ucfirst(__('Tag')) }}</th>
                            <th>{{ ucfirst(__('Punkte')) }}</th>
                            <th>{{ ucfirst(__('Mitglieder')) }}</th>
                            <th>{{ ucfirst(__('Dörfer')) }}</th>
                            <th>{{ ucfirst(__('Punkte pro Spieler')) }}</th>
                            <th>{{ ucfirst(__('Insgesamt')) }}</th>
                            <th>{{ ucfirst(__('Angreifer')) }}</th>
                            <th>{{ ucfirst(__('Verteidiger')) }}</th>
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
    <script>
        $(document).ready( function () {
            $('#table_id').DataTable({
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
                "ajax": "{{ route('api.worldAlly', [$worldData->server->code, $worldData->name]) }}",
                "columns": [
                    { "data": "rank" },
                    { "data": "name", "render": function (value, type, row) {return "<a href='{{ route('world', [$worldData->server->code, $worldData->name]) }}/ally/"+ row.allyID +"'>"+ value +'</a>' }},
                    { "data": "tag", "render": function (value, type, row) {return "<a href='{{ route('world', [$worldData->server->code, $worldData->name]) }}/ally/"+ row.allyID +"'>"+ value +'</a>' }},
                    { "data": "points", "render": function (value) {return numeral(value).format('0.[00] a')}},
                    { "data": "member_count", "render": function (value) {return numeral(value).format('0,0')}},
                    { "data": "village_count", "render": function (value) {return numeral(value).format('0,0')}},
                    { "data": "player_points", "render": function (value) {return numeral(value).format('0.[00] a')}, "orderable": false},
                    { "data": "gesBash" , "render": function (value) {return numeral(value).format('0.[00] a')}},
                    { "data": "offBash", "render": function (value) {return numeral(value).format('0.[00] a')} },
                    { "data": "defBash", "render": function (value) {return numeral(value).format('0.[00] a')} },
                ],
                responsive: true,
                {!! \App\Util\Datatable::language() !!}
            });
        } );
    </script>
@endsection
