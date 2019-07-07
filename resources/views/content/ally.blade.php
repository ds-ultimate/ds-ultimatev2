@extends('layouts.temp')

@section('titel', ucfirst(__('Stamm')).': '.\App\Util\BasicFunctions::outputName($allyData->name))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('Stamm')).': '.\App\Util\BasicFunctions::decodeName($allyData->name).' ['.\App\Util\BasicFunctions::decodeName($allyData->tag).']' }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('Stamm')).': ' }}
            </h1>
            <h4>
                {{ \App\Util\BasicFunctions::decodeName($allyData->name) }}
                <br>
                [{{ \App\Util\BasicFunctions::decodeName($allyData->tag) }}]
            </h4>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Informationen -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ucfirst(__('Informationen'))}}</h4>
                    <h5 class="card-subtitle">{{__('Allgemein')}}</h5>
                    <table id="data1" class="table table-bordered no-wrap">
                        <thead>
                        <tr>
                            <th>{{ ucfirst(__('Rang')) }}</th>
                            <th>{{ ucfirst(__('Name')) }}</th>
                            <th>{{ ucfirst(__('Tag')) }}</th>
                            <th>{{ ucfirst(__('Punkte')) }}</th>
                            <th>{{ ucfirst(__('Dörfer')) }}</th>
                            <th>{{ ucfirst(__('Mitglieder')) }}</th>
                            <th>{{ ucfirst(__('Punkte pro Spieler')) }}</th>
                            <th>{{ ucfirst(__('Punkte pro Dorf')) }}</th>
                            <th>{{ ucfirst(__('Eroberungen')) }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th>{{ \App\Util\BasicFunctions::numberConv($allyData->rank) }}</th>
                            <td>{{ \App\Util\BasicFunctions::decodeName($allyData->name) }}</td>
                            <td>{{ \App\Util\BasicFunctions::decodeName($allyData->tag) }}</td>
                            <td>{{ \App\Util\BasicFunctions::numberConv($allyData->points) }}</td>
                            <td>{{ \App\Util\BasicFunctions::numberConv($allyData->village_count) }}</td>
                            <td>{{ \App\Util\BasicFunctions::numberConv($allyData->member_count) }}</td>
                            <td>{{ ($allyData->points != 0 && $allyData->member_count != 0)?\App\Util\BasicFunctions::numberConv($allyData->points/$allyData->member_count): '-' }}</td>
                            <td>{{ ($allyData->points != 0 && $allyData->village_count != 0)?\App\Util\BasicFunctions::numberConv($allyData->points/$allyData->village_count): '-' }}</td>
                            <td>{{ \App\Util\BasicFunctions::numberConv($conquer->get('total')) }}(<i class="text-success">{{ \App\Util\BasicFunctions::numberConv($conquer->get('new')) }}</i>-<i class="text-danger">{{ \App\Util\BasicFunctions::numberConv($conquer->get('old')) }}</i>)</td>
                        </tr>
                        </tbody>
                    </table>
                    <br>
                    <h5 class="card-subtitle">{{__('Besiegte Gegner')}}</h5>
                    <table id="data2" class="table table-bordered no-wrap">
                        <thead>
                        <tr>
                            <th>{{ ucfirst(__('Rang')) }} ({{__('Insgesamt') }})</th>
                            <th>{{ ucfirst(__('Punkte')) }} ({{__('Insgesamt') }})</th>
                            <th>{{ ucfirst(__('KP-Rate')) }}</th>
                            <th>{{ ucfirst(__('Rang')) }} ({{__('Angreifer') }})</th>
                            <th>{{ ucfirst(__('Punkte')) }} ({{__('Angreifer') }})</th>
                            <th>{{ ucfirst(__('Rang')) }} ({{__('Verteidiger') }})</th>
                            <th>{{ ucfirst(__('Punkte')) }} ({{__('Verteidiger') }})</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th>{{ \App\Util\BasicFunctions::numberConv($allyData->gesBashRank) }}</th>
                            <td>{{ \App\Util\BasicFunctions::numberConv($allyData->gesBash) }}</td>
                            <td>{{ \App\Util\BasicFunctions::numberConv(($allyData->gesBash/$allyData->points)*100) }}%</td>
                            <th>{{ \App\Util\BasicFunctions::numberConv($allyData->offBashRank) }}</th>
                            <td>{{ \App\Util\BasicFunctions::numberConv($allyData->offBash) }}</td>
                            <th>{{ \App\Util\BasicFunctions::numberConv($allyData->defBashRank) }}</th>
                            <td>{{ \App\Util\BasicFunctions::numberConv($allyData->defBash) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- ENDE Informationen -->
        <!-- Allgemein Chart -->
        <div class="col-12 col-md-6 mt-2">
            <div class="card" style=" height: 320px">
                <div class="card-body">
                    <h4 class="card-title">{{ __('Allgemein') }}:</h4>
                    <select id="statsGeneral" class="form-control form-control-sm">
                        @for($i = 0; $i < count($statsGeneral); $i++)
                            <option value="{{ $statsGeneral[$i] }}" {{ ($i == 0)? 'selected=""' : null }}>{{ __('chart.titel_'.$statsGeneral[$i]) }}</option>
                        @endfor
                    </select>
                    @for($i = 0; $i < count($statsGeneral); $i++)
                        <div id="{{ $statsGeneral[$i] }}" class="col-12 position-absolute pl-0 mt-2">
                            <div class="card mr-4">
                                <div id="chart-{{ $statsGeneral[$i] }}"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
        <!-- ENDE Allgemein Chart -->
        <!-- Besiegte Gegner Chart -->
        <div class="col-12 col-md-6 mt-2">
            <div class="card" style="height: 320px">
                <div class="card-body">
                    <h4 class="card-title">{{ __('Besiegte Gegner') }}:</h4>
                    <select id="statsBash" class="form-control form-control-sm">
                        @for($i = 0; $i < count($statsBash); $i++)
                            <option value="{{ $statsBash[$i] }}" {{ ($i == 0)? 'selected=""' : null }}>{{ __('chart.titel_'.$statsBash[$i]) }}</option>
                        @endfor
                    </select>
                    @for($i = 0; $i < count($statsBash); $i++)
                        <div id="{{ $statsBash[$i] }}" class="col-12 position-absolute pl-0 mt-2">
                            <div class="card mr-4">
                                <div id="chart-{{ $statsBash[$i] }}"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
        <!-- ENDE Besiegte Gegner Chart -->
        <!-- Datachart Spieler -->
        <div class="col-12 mt-2">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">{{ ucfirst(__('Spieler')) }}</h2>
                    <table id="table_id" class="table table-hover table-sm w-100">
                        <thead>
                        <tr class="d-none d-md-table-row">
                            <th colspan="6">{{ ucfirst(__('Allgemein')) }}</th>
                            <th colspan="4">{{ ucfirst(__('Besiegte Gegner')) }}</th>
                        </tr>
                        <tr>
                            <th>{{ ucfirst(__('Rang')) }}</th>
                            <th>{{ ucfirst(__('Name')) }}</th>
                            <th>{{ ucfirst(__('Stamm')) }}</th>
                            <th>{{ ucfirst(__('Punkte')) }}</th>
                            <th>{{ ucfirst(__('Dörfer')) }}</th>
                            <th>{{ ucfirst(__('Punkte pro Dorf')) }}</th>
                            <th>{{ ucfirst(__('Insgesamt')) }}</th>
                            <th>{{ ucfirst(__('Angreifer')) }}</th>
                            <th>{{ ucfirst(__('Verteidiger')) }}</th>
                            <th>{{ ucfirst(__('Unterstützer')) }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- ENDE Datachart Spieler -->
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            $("#{{ $statsGeneral[0] }}").css('visibility', 'visible');
            $("#{{ $statsGeneral[1] }}").css('visibility', 'hidden');
            $("#{{ $statsGeneral[2] }}").css('visibility', 'hidden');
            $("#{{ $statsBash[0] }}").css('visibility', 'visible');
            $("#{{ $statsBash[1] }}").css('visibility', 'hidden');
            $("#{{ $statsBash[2] }}").css('visibility', 'hidden');
        });

        $("#statsGeneral").change(function () {
            var option1 = $("#statsGeneral").val();
            if (option1 == '{{ $statsGeneral[0] }}') {
                $("#{{ $statsGeneral[0] }}").css('visibility', 'visible');
                $("#{{ $statsGeneral[1] }}").css('visibility', 'hidden');
                $("#{{ $statsGeneral[2] }}").css('visibility', 'hidden');
            }
            if (option1 == '{{ $statsGeneral[1] }}') {
                $("#{{ $statsGeneral[0] }}").css('visibility', 'hidden');
                $("#{{ $statsGeneral[1] }}").css('visibility', 'visible');
                $("#{{ $statsGeneral[2] }}").css('visibility', 'hidden');
            }
            if (option1 == '{{ $statsGeneral[2] }}') {
                $("#{{ $statsGeneral[0] }}").css('visibility', 'hidden');
                $("#{{ $statsGeneral[1] }}").css('visibility', 'hidden');
                $("#{{ $statsGeneral[2] }}").css('visibility', 'visible');
            }
        });

        $("#statsBash").change(function () {
            var option1 = $("#statsBash").val();
            if (option1 == '{{ $statsBash[0] }}') {
                $("#{{ $statsBash[0] }}").css('visibility', 'visible');
                $("#{{ $statsBash[1] }}").css('visibility', 'hidden');
                $("#{{ $statsBash[2] }}").css('visibility', 'hidden');
            }
            if (option1 == '{{ $statsBash[1] }}') {
                $("#{{ $statsBash[0] }}").css('visibility', 'hidden');
                $("#{{ $statsBash[1] }}").css('visibility', 'visible');
                $("#{{ $statsBash[2] }}").css('visibility', 'hidden');
            }
            if (option1 == '{{ $statsBash[2] }}') {
                $("#{{ $statsBash[0] }}").css('visibility', 'hidden');
                $("#{{ $statsBash[1] }}").css('visibility', 'hidden');
                $("#{{ $statsBash[2] }}").css('visibility', 'visible');
            }
        });

    </script>
    <script>

        $(document).ready( function () {
            $.extend( $.fn.dataTable.defaults, {
                responsive: true
            } );

            $('#data1').DataTable({
                dom: 't',
                ordering: false,
                paging: false,
                responsive: true,

                keys: true, //enable KeyTable extension
            });

            $('#data2').DataTable({
                dom: 't',
                ordering: false,
                paging: false,
                responsive: true,

                keys: true, //enable KeyTable extension
            });

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
                "ajax": "{{ route('api.allyPlayer', [$worldData->server->code, $worldData->name, $allyData->allyID]) }}",
                "columns": [
                    { "data": "rank" },
                    { "data": "name", "render": function (value, type, row) {return "<a href='{{ route('world', [$worldData->server->code, $worldData->name]) }}/player/"+ row.playerID +"'>"+ value +'</a>'}},
                    { "data": "ally", "render": function (value, type, row) {return "<a href='{{ route('world', [$worldData->server->code, $worldData->name]) }}/ally/"+ row.ally_id +"'>"+ value +'</a>'}, "orderable": false},
                    { "data": "points", "render": function (value) {return numeral(value).format('0.[00] a')}},
                    { "data": "village_count", "render": function (value) {return numeral(value).format('0,0')}},
                    { "data": "village_points", "render": function (value) {return numeral(value).format('0,0')}, "orderable": false},
                    { "data": "gesBash" , "render": function (value) {return numeral(value).format('0.[00] a')}},
                    { "data": "offBash", "render": function (value) {return numeral(value).format('0.[00] a')} },
                    { "data": "defBash", "render": function (value) {return numeral(value).format('0.[00] a')} },
                    { "data": "utBash", "render": function (value) {return numeral(value).format('0.[00] a')}, "orderable": false},
                ],
                responsive: true,
                {!! \App\Util\Datatable::language() !!}
            });
        } );
    </script>
    {!! $chartJS !!}
@endsection
