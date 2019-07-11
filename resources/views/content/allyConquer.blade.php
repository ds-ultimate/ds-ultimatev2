@extends('layouts.temp')

@section('titel', ucfirst(__('ui.titel.ally')).': '.\App\Util\BasicFunctions::outputName($allyData->name))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ $typeName.': '.\App\Util\BasicFunctions::decodeName($allyData->name).' ['.\App\Util\BasicFunctions::decodeName($allyData->tag).']' }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ $typeName.': ' }}
            </h1>
            <h4>
                {{ \App\Util\BasicFunctions::decodeName($allyData->name) }}
                <br>
                [{{ \App\Util\BasicFunctions::decodeName($allyData->tag) }}]
            </h4>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Datachart Stammeswechsel -->
        <div class="col-12 mt-2">
            <div class="card">
                <div class="card-body">
                    <table id="table_id" class="table table-hover table-sm w-100">
                        <thead>
                        <tr>
                            <th>{{ ucfirst(__('ui.table.date')) }}</th>
                            <th>{{ ucfirst(__('ui.table.villageName')) }}</th>
                            <th>{{ ucfirst(__('ui.table.old').' '.__('ui.table.owner')) }}</th>
                            <th>{{ ucfirst(__('ui.table.new').' '.__('ui.table.owner')) }}</th>
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

@section('js')
    <script>

        $(document).ready( function () {
            $.extend( $.fn.dataTable.defaults, {
                responsive: true
            } );

            $('#table_id').DataTable({
                "columnDefs": [
                    {"targets": 1, "className": 'text-right'},
                    {"targets": 2, "className": 'text-right'},
                    {"targets": 3, "className": 'text-right'},
                ],
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('api.allyConquer', [$worldData->server->code, $worldData->name, $type, $allyData->allyID]) }}",
                "columns": [
                    { "data": "timestamp"},
                    { "data": "village_name", "render": function (value, type, row) {return "<a href='{{ route('world', [$worldData->server->code, $worldData->name]) }}/village/"+ row.village_id +"'>"+ value +'</a>'}, "orderable": false},
                    { "data": "old_owner_name", "render": function (value, type, row) {return (row.old_owner_exists)?("<a href='{{ route('world', [$worldData->server->code, $worldData->name]) }}/player/"+ row.old_owner +"'>"+ value +'</a>'):(value)}, "orderable": false},
                    { "data": "new_owner_name", "render": function (value, type, row) {return (row.new_owner_exists)?("<a href='{{ route('world', [$worldData->server->code, $worldData->name]) }}/player/"+ row.new_owner +"'>"+ value +'</a>'):(value)}, "orderable": false},
                ],
                responsive: true,
                {!! \App\Util\Datatable::language() !!}
            });
        } );
    </script>
@endsection
