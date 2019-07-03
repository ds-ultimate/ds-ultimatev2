@extends('layouts.temp')

@section('content')
    <div class="row justify-content-center">
        <div class="col-10">
            <table id="table_id" class="table table-hover table-sm w-100">
                <thead>
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
@endsection

@section('js')
    <script>
        $('#table_id').DataTable({
            "columnDefs": [
                {"targets": 3, "className": 'text-right'},
                {"targets": 4, "className": 'text-right'},
                {"targets": 5, "className": 'text-right'},
                {"targets": 6, "className": 'text-right'},
                {"targets": 7, "className": 'text-right'},
                {"targets": 8, "className": 'text-right'},
                {"targets": 9, "className": 'text-right'},
                {"targets": 10, "className": 'text-right'},
            ],
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('api.worldAlly', [\App\Util\BasicFunctions::getServer($worldData->get('name')), $worldData->get('world')]) }}",
            "columns": [
                { "data": "rank" },
                { "data": "name", "render": function (value, type, row) {return "<a href='{{ route('world', [$worldData->get('server'), $worldData->get('world')]) }}/ally/"+ row.allyID +"'>"+ value +'</a>' }},
                { "data": "tag", "render": function (value, type, row) {return "<a href='{{ route('world', [$worldData->get('server'), $worldData->get('world')]) }}/ally/"+ row.allyID +"'>"+ value +'</a>' }},
                { "data": "points", "render": function (value) {return numeral(value).format('0.[00] a')}},
                { "data": "member_count", "render": function (value) {return numeral(value).format('0,0')}},
                { "data": "village_count", "render": function (value) {return numeral(value).format('0,0')}},
                { "data": "player_points", "render": function (value) {return numeral(value).format('0.[00] a')}, "orderable": false},
                { "data": "village_points", "render": function (value) {return numeral(value).format('0.[00] a')}, "orderable": false},
                { "data": "gesBash" , "render": function (value) {return numeral(value).format('0.[00] a')}},
                { "data": "offBash", "render": function (value) {return numeral(value).format('0.[00] a')} },
                { "data": "defBash", "render": function (value) {return numeral(value).format('0.[00] a')} },
            ],
            responsive: true,
            {!! \App\Util\Datatable::language() !!}
        });
    </script>
@endsection
