@extends('layouts.app')

@section('titel', ucfirst(__('ui.titel.player')).': '.\App\Util\BasicFunctions::decodeName($playerTopData->name))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ $typeName.': '.\App\Util\BasicFunctions::decodeName($playerTopData->name) }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ $typeName.': ' }}
            </h1>
            <h4>
                {{ \App\Util\BasicFunctions::decodeName($playerTopData->name) }}
            </h4>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Datachart Stammeswechsel -->
        <div class="col-12 mt-2">
            <div class="card">
                <div class="card-body cust-responsive">
                    <table id="table_id" class="table nowrap table-hover table-sm w-100">
                        <thead>
                        <tr>
                            <th>{{ ucfirst(__('ui.table.date')) }}</th>
                            <th>{{ ucfirst(__('ui.table.playerName')) }}</th>
                            <th>{{ ucfirst(__('ui.table.new').' '.__('ui.table.ally')) }}</th>
                            <th>{{ ucfirst(__('ui.table.old').' '.__('ui.table.ally')) }}</th>
                            <th>{{ ucfirst(__('ui.table.points')) }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- ENDE Datachart Stammeswechsel -->
    </div>
@endsection

@push('js')
    <script>
        $(document).ready( function () {
            $('#table_id').DataTable({
                "columnDefs": [
                    {"targets": 4, "className": 'text-right'},
                ],
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('api.playerAllyChanges', [$worldData->id, $type, $playerTopData->playerID]) }}",
                "columns": [
                    { "data": "created_at" },
                    { "data": "player_name", "render": function (value, type, row) {return "<a href='{{ route('world', [$worldData->server->code, $worldData->name]) }}/player/"+ row.player_id +"'>"+ value +'</a>'}, "orderable": false},
                    { "data": "new_ally_name", "render": function (value, type, row) {return (row.new_ally_id==0)?(value):("<a href='{{ route('world', [$worldData->server->code, $worldData->name]) }}/ally/"+ row.new_ally_id +"'>"+ value +'</a>')}, "orderable": false},
                    { "data": "old_ally_name", "render": function (value, type, row) {return (row.old_ally_id==0)?(value):("<a href='{{ route('world', [$worldData->server->code, $worldData->name]) }}/ally/"+ row.old_ally_id +"'>"+ value +'</a>')}, "orderable": false},
                    { "data": "points", "render": function (value) {return numeral(value).format('0,0')}},
                ],
                "order": [[ 0, "desc" ]],
                stateSave: true,
                customName: "playerAllyChanges",
                {!! \App\Util\Datatable::language() !!}
            });
        } );
    </script>
@endpush
