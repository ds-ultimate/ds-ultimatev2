@extends('layouts.app')

@section('titel', ucfirst(__('ui.titel.player')).': '.\App\Util\BasicFunctions::decodeName($playerData->name))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ $typeName.': '.\App\Util\BasicFunctions::decodeName($playerData->name) }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ $typeName.': ' }}
            </h1>
            <h4>
                {{ \App\Util\BasicFunctions::decodeName($playerData->name) }}
            </h4>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Datachart Eroberungen -->
        <div class="col-12 mt-2">
            <div class="card">
                <div class="card-body">
                    <table id="table_id" class="table table-hover table-sm w-100">
                        <thead>
                        <tr>
                            <th>{{ ucfirst(__('ui.table.date')) }}</th>
                            <th>{{ ucfirst(__('ui.table.villageName')) }}</th>
                            <th>{{ ucfirst(__('ui.table.old').' '.__('ui.table.owner')) }}</th>
                            <th>{{ ucfirst(__('ui.table.old').' '.__('ui.table.ally')) }}</th>
                            <th>{{ ucfirst(__('ui.table.new').' '.__('ui.table.owner')) }}</th>
                            <th>{{ ucfirst(__('ui.table.new').' '.__('ui.table.ally')) }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- ENDE Datachart Eroberungen -->
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
                    {"targets": 2, "className": 'text-right'},
                    {"targets": 4, "className": 'text-right'},
                ],
                "processing": true,
                "serverSide": true,
                "order": [[ 0, "desc" ]],
                "ajax": "{{ route('api.playerConquer', [$worldData->server->code, $worldData->name, $type, $playerData->playerID]) }}",
                "columns": [
                    { "data": "timestamp" },
                    { "data": "village_html", "orderable": false},
                    { "data": "old_owner_html", "orderable": false},
                    { "data": "old_owner_ally_html", "orderable": false},
                    { "data": "new_owner_html", "orderable": false},
                    { "data": "new_owner_ally_html", "orderable": false},
                ],
                "fnRowCallback": function(row, data) {
                    if(data.type == 3) {//barbarian
                        $('td', row).css('background-color', 'rgba(140,140,140,0.2)'); //gray
                    } else if(data.type == 2) {//self
                        $('td', row).css('background-color', 'rrgba(235,247,64,0.2)'); //Yellow
                    } else if(data.type == 1) {//internal
                        $('td', row).css('background-color', 'rgba(38,79,242,0.2)'); //Blue
                    } else if(data.winLoose == 1) {//win
                        $('td', row).css('background-color', 'rgba(42,175,71,0.2)'); //Green
                    } else if(data.winLoose == -1) {//loose
                        $('td', row).css('background-color', 'rgba(226,54,71,0.2)'); //Red
                    }
                },
                responsive: true,
                {!! \App\Util\Datatable::language() !!}
            });
        } );
    </script>
@endsection
