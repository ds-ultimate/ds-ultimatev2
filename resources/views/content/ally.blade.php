@extends('layouts.app')

@section('titel', ucfirst(__('ui.titel.ally')).': '.\App\Util\BasicFunctions::decodeName($allyData->name))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('ui.titel.ally')).': '.\App\Util\BasicFunctions::decodeName($allyData->name).' ['.\App\Util\BasicFunctions::decodeName($allyData->tag).']' }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('ui.titel.ally')).': ' }}
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
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="stats-tab" data-toggle="tab" href="#stats" role="tab" aria-controls="stats" aria-selected="true">{{ __('ui.nav.stats') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="map-tab" data-toggle="tab" href="#map" role="tab" aria-controls="map" aria-selected="false">{{ __('ui.nav.map') }}</a>
                    </li>
                </ul>
                <div class="card-body tab-content">
                    <div class="tab-pane fade show active" id="stats" role="tabpanel" aria-labelledby="stats-tab">
                        <h4 class="card-title">{{ucfirst(__('ui.tabletitel.info'))}}
                            <span class="float-right">
                                <a href="{{ $allyData->linkIngame($worldData, false) }}" target="_blank" class="btn btn-primary btn-sm">{{ __('ui.ingame.normal') }}</a>
                                <a href="{{ $allyData->linkIngame($worldData, true) }}" target="_blank" class="btn btn-primary btn-sm">{{ __('ui.ingame.guest') }}</a>
                            </span>
                        </h4>
                        <h5 class="card-subtitle">{{__('ui.tabletitel.general')}}</h5>
                        <table id="data1" class="table table-bordered no-wrap w-100">
                            <thead>
                            <tr>
                                <th class="all">{{ ucfirst(__('ui.table.rank')) }}</th>
                                <th class="all">{{ ucfirst(__('ui.table.name')) }}</th>
                                <th class="desktop">{{ ucfirst(__('ui.table.tag')) }}</th>
                                <th class="desktop">{{ ucfirst(__('ui.table.points')) }}</th>
                                <th class="desktop">{{ ucfirst(__('ui.table.villages')) }}</th>
                                <th class="desktop">{{ ucfirst(__('ui.table.members')) }}</th>
                                <th class="desktop">{{ ucfirst(__('ui.table.avgPlayer')) }}</th>
                                <th class="desktop">{{ ucfirst(__('ui.table.avgVillage')) }}</th>
                                <th class="desktop">{{ ucfirst(__('ui.table.conquer')) }}</th>
                                <th class="desktop">{{ ucfirst(__('ui.table.allyChanges')) }}</th>
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
                                <td>{!! \App\Util\BasicFunctions::linkWinLoose($worldData, $allyData->allyID, $conquer, 'allyConquer') !!}</td>
                                <td>{!! \App\Util\BasicFunctions::linkWinLoose($worldData, $allyData->allyID, $allyChanges, 'allyAllyChanges') !!}</td>
                            </tr>
                            </tbody>
                        </table>
                        <br>
                        <h5 class="card-subtitle">{{__('ui.tabletitel.bashStats')}}</h5>
                        <table id="data2" class="table table-bordered no-wrap w-100">
                            <thead>
                            <tr>
                                <th class="all">{{ ucfirst(__('ui.table.rank')) }} ({{__('ui.table.bashGes') }})</th>
                                <th class="all">{{ ucfirst(__('ui.table.points')) }} ({{__('ui.table.bashGes') }})</th>
                                <th class="desktop">{{ ucfirst(__('ui.table.bashPointsRatio')) }}</th>
                                <th class="desktop">{{ ucfirst(__('ui.table.rank')) }} ({{__('ui.table.bashOff') }})</th>
                                <th class="desktop">{{ ucfirst(__('ui.table.points')) }} ({{__('ui.table.bashOff') }})</th>
                                <th class="desktop">{{ ucfirst(__('ui.table.rank')) }} ({{__('ui.table.bashDeff') }})</th>
                                <th class="desktop">{{ ucfirst(__('ui.table.points')) }} ({{__('ui.table.bashDeff') }})</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <th>{{ \App\Util\BasicFunctions::numberConv($allyData->gesBashRank) }}</th>
                                <td>{{ \App\Util\BasicFunctions::numberConv($allyData->gesBash) }}</td>
                                <td>{{ ($allyData->points != 0)?(\App\Util\BasicFunctions::numberConv(($allyData->gesBash/$allyData->points)*100)):('-') }}%</td>
                                <th>{{ \App\Util\BasicFunctions::numberConv($allyData->offBashRank) }}</th>
                                <td>{{ \App\Util\BasicFunctions::numberConv($allyData->offBash) }}</td>
                                <th>{{ \App\Util\BasicFunctions::numberConv($allyData->defBashRank) }}</th>
                                <td>{{ \App\Util\BasicFunctions::numberConv($allyData->defBash) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="map" role="tabpanel" aria-labelledby="map-tab">
                    </div>
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
                    <h2 class="card-title">{{ ucfirst(__('ui.tabletitel.player')) }}</h2>
                    <table id="table_id" class="table table-hover table-sm w-100">
                        <thead>
                        <tr class="d-none d-md-table-row">
                            <th colspan="6">{{ ucfirst(__('ui.tabletitel.general')) }}</th>
                            <th colspan="4">{{ ucfirst(__('ui.tabletitel.bashStats')) }}</th>
                        </tr>
                        <tr>
                            <th>{{ ucfirst(__('ui.table.rank')) }}</th>
                            <th>{{ ucfirst(__('ui.table.name')) }}</th>
                            <th>{{ ucfirst(__('ui.table.ally')) }}</th>
                            <th>{{ ucfirst(__('ui.table.points')) }}</th>
                            <th>{{ ucfirst(__('ui.table.villages')) }}</th>
                            <th>{{ ucfirst(__('ui.table.avgVillage')) }}</th>
                            <th>{{ ucfirst(__('ui.table.bashGes')) }}</th>
                            <th>{{ ucfirst(__('ui.table.bashOff')) }}</th>
                            <th>{{ ucfirst(__('ui.table.bashDeff')) }}</th>
                            <th>{{ ucfirst(__('ui.table.bashUt')) }}</th>
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

@push('js')
    <script>
        $('#map-tab').click(function (e) {
            if($('#map-img').length > 0) return;
            $.ajax({
                type: "GET",
                url:"{{ route('api.map.overview.sized', [$worldData->server->code, $worldData->name, 'a', $allyData->allyID, '500', '500', 'base64']) }}",
                contentType: "image/png",
                success: function(data){
                $('#map').html('<img id="map-img" class="container-fluid p-0" src="' + data + '" />'); },
            });
        });
    </script>
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
@endpush
