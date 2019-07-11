@extends('layouts.temp')

@section('titel', ucfirst(__('ui.titel.player')).': '.\App\Util\BasicFunctions::outputName($playerData->name))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('ui.titel.player')).': '.\App\Util\BasicFunctions::decodeName($playerData->name) }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('ui.titel.player')).': ' }}
            </h1>
            <h4>
                {{ \App\Util\BasicFunctions::decodeName($playerData->name) }}
            </h4>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Informationen -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ucfirst(__('ui.tabletitel.info'))}}</h4>
                    <h5 class="card-subtitle">{{__('ui.tabletitel.general')}}</h5>
                    <table id="data1" class="table table-bordered no-wrap">
                        <thead>
                        <tr>
                            <th>{{ ucfirst(__('ui.table.rank')) }}</th>
                            <th>{{ ucfirst(__('ui.table.name')) }}</th>
                            <th>{{ ucfirst(__('ui.table.ally')) }}</th>
                            <th>{{ ucfirst(__('ui.table.points')) }}</th>
                            <th>{{ ucfirst(__('ui.table.villages')) }}</th>
                            <th>{{ ucfirst(__('ui.table.avgVillage')) }}</th>
                            <th>{{ ucfirst(__('ui.table.conquer')) }}</th>
                            <th>{{ ucfirst(__('ui.table.allyChanges')) }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th>{{ \App\Util\BasicFunctions::numberConv($playerData->rank) }}</th>
                            <td>{{ \App\Util\BasicFunctions::decodeName($playerData->name) }}</td>
                            <td>{!! ($playerData->ally_id != 0)?\App\Util\BasicFunctions::linkAlly($worldData, $playerData->ally_id, \App\Util\BasicFunctions::outputName($playerData->allyLatest->tag)) : '-' !!}</td>
                            <td>{{ \App\Util\BasicFunctions::numberConv($playerData->points) }}</td>
                            <td>{{ \App\Util\BasicFunctions::numberConv($playerData->village_count) }}</td>
                            <td>{{ ($playerData->village_count != 0)?\App\Util\BasicFunctions::numberConv($playerData->points/$playerData->village_count): '-' }}</td>
                            <td>{!! \App\Util\BasicFunctions::linkPlayerConquer($worldData, $playerData->playerID, $conquer) !!}</td>
                            <td>{!! \App\Util\BasicFunctions::linkPlayerAllyChanges($worldData, $playerData->playerID, $allyChanges) !!}</td>
                        </tr>
                        </tbody>
                    </table>
                    <br>
                    <h5 class="card-subtitle">{{__('ui.tabletitel.bashStats')}}</h5>
                    <table id="data2" class="table table-bordered no-wrap">
                        <thead>
                        <tr>
                            <th>{{ ucfirst(__('ui.table.rank')) }} ({{__('ui.table.bashGes') }})</th>
                            <th>{{ ucfirst(__('ui.table.points')) }} ({{__('ui.table.bashGes') }})</th>
                            <th>{{ ucfirst(__('ui.table.bashPointsRatio')) }}</th>
                            <th>{{ ucfirst(__('ui.table.rank')) }} ({{__('ui.table.bashOff') }})</th>
                            <th>{{ ucfirst(__('ui.table.points')) }} ({{__('ui.table.bashOff') }})</th>
                            <th>{{ ucfirst(__('ui.table.rank')) }} ({{__('ui.table.bashDeff') }})</th>
                            <th>{{ ucfirst(__('ui.table.points')) }} ({{__('ui.table.bashDeff') }})</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th>{{ \App\Util\BasicFunctions::numberConv($playerData->gesBashRank) }}</th>
                            <td>{{ \App\Util\BasicFunctions::numberConv($playerData->gesBash) }}</td>
                            <td>{{ ($playerData->points != 0)?\App\Util\BasicFunctions::numberConv(($playerData->gesBash/$playerData->points)*100): ('-') }}%</td>
                            <th>{{ \App\Util\BasicFunctions::numberConv($playerData->offBashRank) }}</th>
                            <td>{{ \App\Util\BasicFunctions::numberConv($playerData->offBash) }}</td>
                            <th>{{ \App\Util\BasicFunctions::numberConv($playerData->defBashRank) }}</th>
                            <td>{{ \App\Util\BasicFunctions::numberConv($playerData->defBash) }}</td>
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
                    <h4 class="card-title">{{ __('ui.tabletitel.general') }}:</h4>
                    <select id="statsGeneral" class="form-control form-control-sm">
                        @for($i = 0; $i < count($statsGeneral); $i++)
                            <option value="{{ $statsGeneral[$i] }}" {{ ($i == 0)? 'selected=""' : null }}>{{ __('chart.titel.'.$statsGeneral[$i]) }}</option>
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
                    <h4 class="card-title">{{ __('ui.tabletitel.bashStats') }}:</h4>
                    <select id="statsBash" class="form-control form-control-sm">
                        @for($i = 0; $i < count($statsBash); $i++)
                            <option value="{{ $statsBash[$i] }}" {{ ($i == 0)? 'selected=""' : null }}>{{ __('chart.titel.'.$statsBash[$i]) }}</option>
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
                    <h2 class="card-title">{{ ucfirst(__('ui.tabletitel.villages')) }}</h2>
                    <table id="table_id" class="table table-hover table-sm w-100">
                        <thead>
                        <tr>
                            <th>{{ ucfirst(__('ui.table.id')) }}</th>
                            <th>{{ ucfirst(__('ui.table.name')) }}</th>
                            <th>{{ ucfirst(__('ui.table.points')) }}</th>
                            <th>{{ ucfirst(__('ui.table.continent')) }}</th>
                            <th>{{ ucfirst(__('ui.table.coordinates')) }}</th>
                            <th>{{ ucfirst(__('ui.table.bonusType')) }}</th>
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
            $("#{{ $statsBash[3] }}").css('visibility', 'hidden');
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
            {{--$(location).attr("href", "{{ URL::route('troopForm') }}/" + option1 + "/" + option2);--}}
            if (option1 == '{{ $statsBash[0] }}') {
                $("#{{ $statsBash[0] }}").css('visibility', 'visible');
                $("#{{ $statsBash[1] }}").css('visibility', 'hidden');
                $("#{{ $statsBash[2] }}").css('visibility', 'hidden');
                $("#{{ $statsBash[3] }}").css('visibility', 'hidden');
            }
            if (option1 == '{{ $statsBash[1] }}') {
                $("#{{ $statsBash[0] }}").css('visibility', 'hidden');
                $("#{{ $statsBash[1] }}").css('visibility', 'visible');
                $("#{{ $statsBash[2] }}").css('visibility', 'hidden');
                $("#{{ $statsBash[3] }}").css('visibility', 'hidden');
            }
            if (option1 == '{{ $statsBash[2] }}') {
                $("#{{ $statsBash[0] }}").css('visibility', 'hidden');
                $("#{{ $statsBash[1] }}").css('visibility', 'hidden');
                $("#{{ $statsBash[2] }}").css('visibility', 'visible');
                $("#{{ $statsBash[3] }}").css('visibility', 'hidden');
            }
            if (option1 == '{{ $statsBash[3] }}') {
                $("#{{ $statsBash[0] }}").css('visibility', 'hidden');
                $("#{{ $statsBash[1] }}").css('visibility', 'hidden');
                $("#{{ $statsBash[2] }}").css('visibility', 'hidden');
                $("#{{ $statsBash[3] }}").css('visibility', 'visible');
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
                    {"targets": 2, "className": 'text-right'},
                    {"targets": 3, "className": 'text-right'},
                    {"targets": 4, "className": 'text-right'},
                    {"targets": 5, "className": 'text-right'},
                ],
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('api.playerVillage', [$worldData->server->code, $worldData->name, $playerData->playerID]) }}",
                "columns": [
                    { "data": "villageID"},
                    { "data": "name", "render": function (value, type, row) {return "<a href='{{ route('world', [$worldData->server->code, $worldData->name]) }}/village/"+ row.villageID +"'>"+ value +'</a>'}},
                    { "data": "points", "render": function (value) {return numeral(value).format('0,0')}},
                    { "data": "continent", "orderable": false},
                    { "data": "coordinates", "orderable": false},
                    { "data": "bonus"},
                ],
                responsive: true,
                {!! \App\Util\Datatable::language() !!}
            });
        } );
    </script>
    {!! $chartJS !!}
@endsection
