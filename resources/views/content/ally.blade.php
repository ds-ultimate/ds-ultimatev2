@extends('layouts.app')

@section('titel', ucfirst(__('ui.titel.ally')).': '.\App\Util\BasicFunctions::decodeName($allyData->name))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="p-lg-3 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('ui.titel.ally')).': '.\App\Util\BasicFunctions::decodeName($allyData->name).' ['.\App\Util\BasicFunctions::decodeName($allyData->tag).']' }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-3 mx-auto my-1 text-center d-lg-none truncate">
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
                    @isset($allyTopData)
                    <li class="nav-item">
                        <a class="nav-link" id="tops-tab" data-toggle="tab" href="#tops" role="tab" aria-controls="tops" aria-selected="false">{{ __('ui.nav.tops') }}</a>
                    </li>
                    @endisset
                    <li class="nav-item">
                        <a class="nav-link" id="hist-tab" data-toggle="tab" href="#hist" role="tab" aria-controls="hist" aria-selected="false">{{ __('ui.nav.history') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="map-tab" data-toggle="tab" href="#map" role="tab" aria-controls="map" aria-selected="false">{{ __('ui.nav.map') }}</a>
                    </li>
                </ul>
                <div class="card-body tab-content">
                    <div class="tab-pane fade show active" id="stats" role="tabpanel" aria-labelledby="stats-tab">
                        <x-record.stat_elm_ally :data='$allyData' :worldData='$worldData' :conquer='$conquer' :allyChanges='$allyChanges'/>
                    </div>
                    
                    @isset($allyTopData)
                    <div class="tab-pane fade" id="tops" role="tabpanel" aria-labelledby="tops-tab">
                        <x-record.stat_elm_ally_top :data='$allyTopData' :worldData='$worldData' :conquer='$conquer' :allyChanges='$allyChanges' exists="true"/>
                    </div>
                    @endisset
                    
                    <!-- BEGIN HIST Table -->
                    <div class="tab-pane fade" id="hist" role="tabpanel" aria-labelledby="hist-tab">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <h4 class="card-title">{{ucfirst(__('ui.tabletitel.allyHist'))}}</h4>
                            </div>
                            <div class="col-12 col-md-6">
                                <span class="float-right">
                                    <a href="{{ $allyData->linkIngame($worldData, false) }}" target="_blank" class="btn btn-primary btn-sm">{{ __('ui.ingame.normal') }}</a>
                                    <a href="{{ $allyData->linkIngame($worldData, true) }}" target="_blank" class="btn btn-primary btn-sm">{{ __('ui.ingame.guest') }}</a>
                                </span>
                            </div>
                            <div class="col-12 mt-3 cust-responsive">
                                <table id="history_table" class="table table-hover table-sm w-100 nowrap">
                                    <thead>
                                    <tr>
                                        <th class="all">{{ ucfirst(__('ui.table.date')) }}</th>
                                        <th class="desktop">{{ ucfirst(__('ui.table.ally')) }}</th>
                                        <th class="all">{{ ucfirst(__('ui.table.rank')) }}</th>
                                        <th class="all">{{ ucfirst(__('ui.table.members')) }}</th>
                                        <th class="all">{{ ucfirst(__('ui.table.points')) }}</th>
                                        <th class="all">{{ ucfirst(__('ui.table.villages')) }}</th>
                                        <th class="all">{{ ucfirst(__('ui.table.bashAllS')) }}</th>
                                        <th class="all">{{ ucfirst(__('ui.table.bashAttS')) }}</th>
                                        <th class="all">{{ ucfirst(__('ui.table.bashDefS')) }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- END HIST Table -->
                    <div class="tab-pane fade" id="map" role="tabpanel" aria-labelledby="map-tab">
                    </div>
                </div>
            </div>
        </div>
        <!-- ENDE Informationen -->
        <!-- Allgemein Chart -->
        <div class="col-12 col-lg-6 mt-3">
            <div class="card" style=" height: 320px">
                <div class="card-body">
                    <h4 class="card-title">{{ __('ui.tabletitel.general') }}:</h4>
                    <select id="statsGeneral" class="form-control form-control-sm">
                        @for($i = 0; $i < count($statsGeneral); $i++)
                            <option value="{{ $statsGeneral[$i] }}" @selected(($i == 0)) >{{ __('chart.titel.'.$statsGeneral[$i]) }}</option>
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
        <div class="col-12 col-lg-6 mt-3">
            <div class="card" style="height: 320px">
                <div class="card-body">
                    <h4 class="card-title">{{ __('ui.tabletitel.bashStats') }}:</h4>
                    <select id="statsBash" class="form-control form-control-sm">
                        @for($i = 0; $i < count($statsBash); $i++)
                            <option value="{{ $statsBash[$i] }}" @selected(($i == 0)) >{{ __('chart.titel.'.$statsBash[$i]) }}</option>
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
        <div class="col-12 mt-3">
            <div class="card">
                <div class="card-body cust-responsive">
                    <h2 class="card-title">{{ ucfirst(__('ui.tabletitel.player')) }}</h2>
                    <table id="table_id" class="table table-hover table-sm w-100 nowrap">
                        <thead>
                        <tr class="d-none d-md-table-row">
                            <th colspan="5">{{ ucfirst(__('ui.tabletitel.general')) }}</th>
                            <th colspan="4">{{ ucfirst(__('ui.tabletitel.bashStats')) }}</th>
                        </tr>
                        <tr>
                            <th>{{ ucfirst(__('ui.table.rank')) }}</th>
                            <th>{{ ucfirst(__('ui.table.name')) }}</th>
                            <th>{{ ucfirst(__('ui.table.points')) }}</th>
                            <th>{{ ucfirst(__('ui.table.villages')) }}</th>
                            <th>{{ ucfirst(__('ui.table.avgVillage')) }}</th>
                            <th>{{ ucfirst(__('ui.table.bashGes')) }}</th>
                            <th>{{ ucfirst(__('ui.table.bashOff')) }}</th>
                            <th>{{ ucfirst(__('ui.table.bashDeff')) }}</th>
                            <th>{{ ucfirst(__('ui.table.bashSup')) }}</th>
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
        $('#map-tab').on('click', function (e) {
            if($('#map-img').length > 0) return;
            $.ajax({
                type: "GET",
                url:"{{ route('api.map.overview.sized', [$worldData->server->code, $worldData->name, 'a', $allyData->allyID, '500', '500', 'base64']) }}",
                contentType: "image/png",
                success: function(data){
                $('#map').html('<img id="map-img" class="container-fluid p-0" src="' + data + '" />'); },
            });
        });
        
        var initializedHistTable = false
        $('#hist-tab').on('click', function() {
            if(initializedHistTable) return
            initializedHistTable = true
            $('#history_table').DataTable({
                "order": [[ 0, "desc" ]],
                "ajax": "{{ route('api.allyHistory', [$worldData->id, $allyData->allyID]) }}",
                "columns": [
                    { "data": "created_at"},
                    { "data": "tag", "render": function (value, type, row) {
                        var ref = "{{ route('ally', [$worldData->server->code, $worldData->name, '%allyID%']) }}";
                        ref = ref.replace('%allyID%', row.allyID);
                        return "<a href='"+ ref +"'>"+ value +'</a>'
                    }},
                    { "data": "rank", "orderable": false},
                    { "data": "member_count", "orderable": false},
                    { "data": "points"},
                    { "data": "village_count"},
                    { "data": "gesBash"},
                    { "data": "offBash"},
                    { "data": "defBash"},
                ],
                "columnDefs": [
                    {"targets": [0, 1, 2, 3, 4, 5, 6, 7, 8], "className": 'dt-left'},
                ],
                responsive: true,
                stateSave: true,
                customName: "allyHistory",
                {!! \App\Util\Datatable::language() !!}
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $("#statsGeneral").trigger('change');
            $("#statsBash").trigger('change');
        });

        $("#statsGeneral").change(() => {
            $("#statsGeneral option").each(function() {
                $('#' + this.value).css('visibility', this.selected?'visible':'hidden');
            });
        });

        $("#statsBash").change(() => {
            $("#statsBash option").each(function() {
                $('#' + this.value).css('visibility', this.selected?'visible':'hidden');
            });
        });
    </script>
    <script>

        $(document).ready( function () {
            var tbl = $('#table_id').DataTable({
                "columnDefs": [
                    {"targets": [0, 1], "className": 'dt-left'},
                    {"targets": [2, 3, 4, 5, 6, 7, 8], "className": 'dt-right'},
                ],
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('api.allyPlayer', [$worldData->id, $allyData->allyID]) }}",
                "columns": [
                    { "data": "rank" },
                    { "data": "name", "render": function (value, type, row) {return "<a href='{{ route('world', [$worldData->server->code, $worldData->name]) }}/player/"+ row.playerID +"'>"+ value +'</a>'}},
                    { "data": "points", "render": function (value) {return numeral(value).format('0.[00] a')}},
                    { "data": "village_count", "render": function (value) {return numeral(value).format('0,0')}},
                    { "data": "village_points", "render": function (value) {return numeral(value).format('0,0')}, "orderable": false},
                    { "data": "gesBash" , "render": function (value) {return numeral(value).format('0.[00] a')}},
                    { "data": "offBash", "render": function (value) {return numeral(value).format('0.[00] a')} },
                    { "data": "defBash", "render": function (value) {return numeral(value).format('0.[00] a')} },
                    { "data": "supBash", "render": function (value) {return numeral(value).format('0.[00] a')}},
                ],
                stateSave: true,
                customName: "allyPlayer",
                {!! \App\Util\Datatable::language() !!}
            });
        });
    </script>
    {!! $chartJS !!}
@endpush
