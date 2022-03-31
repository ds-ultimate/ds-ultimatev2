@extends('layouts.app')

@section('titel', ucfirst(__('ui.titel.player')).': '.\App\Util\BasicFunctions::decodeName($playerData->name))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="p-lg-3 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('ui.titel.player')).': '.\App\Util\BasicFunctions::decodeName($playerData->name) }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-3 mx-auto my-1 text-center d-lg-none truncate">
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
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="stats-tab" data-toggle="tab" href="#stats" role="tab" aria-controls="stats" aria-selected="true">{{ __('ui.nav.stats') }}</a>
                    </li>
                    @isset($playerTopData)
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
                        <x-record.stat_elm_player :data='$playerData' :worldData='$worldData' :conquer='$conquer' :allyChanges='$allyChanges' :playerOtherServers='$playerOtherServers'/>
                    </div>
                    
                    @isset($playerTopData)
                    <div class="tab-pane fade" id="tops" role="tabpanel" aria-labelledby="tops-tab">
                        <x-record.stat_elm_player_top :data='$playerTopData' :worldData='$worldData' :conquer='$conquer' :allyChanges='$allyChanges' :playerOtherServers='$playerOtherServers' exists="true"/>
                    </div>
                    @endisset
                    
                    <!-- BEGIN HIST Table -->
                    <div class="tab-pane fade" id="hist" role="tabpanel" aria-labelledby="hist-tab">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <h4 class="card-title">{{ucfirst(__('ui.tabletitel.playerHist'))}}</h4>
                            </div>
                            <div class="col-12 col-md-6">
                                <span class="float-right">
                                    <a href="{{ $playerData->linkIngame($worldData, false) }}" target="_blank" class="btn btn-primary btn-sm">{{ __('ui.ingame.normal') }}</a>
                                    <a href="{{ $playerData->linkIngame($worldData, true) }}" target="_blank" class="btn btn-primary btn-sm">{{ __('ui.ingame.guest') }}</a>
                                </span>
                            </div>
                            <div class="col-12 mt-3 cust-responsive">
                                <table id="history_table" class="table table-hover table-sm w-100 nowrap">
                                    <thead>
                                    <tr>
                                        <th class="all">{{ ucfirst(__('ui.table.date')) }}</th>
                                        <th class="desktop">{{ ucfirst(__('ui.table.player')) }}</th>
                                        <th class="desktop">{{ ucfirst(__('ui.table.ally')) }}</th>
                                        <th class="all">{{ ucfirst(__('ui.table.rank')) }}</th>
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
        <div class="col-12 col-lg-6 mt-3">
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
        <div class="col-12 mt-3">
            <div class="card">
                <div class="card-body cust-responsive">
                    <h2 class="card-title">{{ ucfirst(__('ui.tabletitel.villages')) }}</h2>
                    <table id="table_id" class="table table-hover table-sm w-100 nowrap">
                        <thead><tr>
                            <th>{{ ucfirst(__('ui.table.id')) }}</th>
                            <th>{{ ucfirst(__('ui.table.name')) }}</th>
                            <th>{{ ucfirst(__('ui.table.points')) }}</th>
                            <th>{{ ucfirst(__('ui.table.coordinates')) }}</th>
                            <th>{{ ucfirst(__('ui.table.continent')) }}</th>
                            <th>{{ ucfirst(__('ui.table.bonusType')) }}</th>
                        </tr></thead>
                        <tbody></tbody>
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
                url:"{{ route('api.map.overview.sized', [$worldData->server->code, $worldData->name, 'p', $playerData->playerID, '500', '500', 'base64']) }}",
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
                "ajax": "{{ route('api.playerHistory', [$worldData->server->code, $worldData->name, $playerData->playerID]) }}",
                "columns": [
                    { "data": "created_at"},
                    { "data": "name", "render": function (value, type, row) {
                        var ref = "{{ route('player', [$worldData->server->code, $worldData->name, '%playerID%']) }}";
                        ref = ref.replace('%playerID%', row.playerID);
                        return "<a href='"+ ref +"'>"+ value +'</a>'
                    }},
                    { "data": "allyTag", "render": function (value, type, row) {
                        var ref = "{{ route('ally', [$worldData->server->code, $worldData->name, '%allyID%']) }}";
                        ref = ref.replace('%allyID%', row.ally_id);
                        return "<a href='"+ ref +"'>"+ value +'</a>'
                    }},
                    { "data": "rank", "orderable": false},
                    { "data": "points"},
                    { "data": "village_count"},
                    { "data": "gesBash"},
                    { "data": "offBash"},
                    { "data": "defBash"},
                ],
                responsive: true,
                {!! \App\Util\Datatable::language() !!}
            });
        });

        function copy(type) {
            /* Get the text field */
            var copyText = $("#" + type);
            /* Select the text field */
            copyText.select();
            /* Copy the text inside the text field */
            document.execCommand("copy");
        }
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
                    { "data": "name", "render": function (value, type, row) {
                        var ref = "{{ route('village', [$worldData->server->code, $worldData->name, '%villageID%']) }}";
                        ref = ref.replace('%villageID%', row.villageID);
                        return "<a href='"+ ref +"'>"+ value +'</a>'
                    }},
                    { "data": "points", "render": function (value) {return numeral(value).format('0,0')}},
                    { "data": "coordinates", "orderable": false},
                    { "data": "continent", "orderable": false},
                    { "data": "bonus_id", "render": function (value, type, row) {return row.bonus}},
                ],
                {!! \App\Util\Datatable::language() !!}
            });
        });
        
        @isset($playerOtherServers)
            $(".otherworld").hover(function(e) {
                if(e.type == "mouseenter") {
                    $('.otherworld-popup', this).removeClass("d-none").addClass("show");
                    //popover-body
                    if(! $('.otherworld-popup', this).hasClass("data-loaded")) {
                        $('.otherworld-popup', this).addClass("data-loaded");
                        var url = "{{ route('api.worldPopup', ['worldId', $playerData->playerID]) }}";
                        axios.get(url.replace("worldId", $(this).data("worldid")), {
                        })
                        .then((response) => {
                            $('.popover-body', this).html(response.data);
                            var lOffset = ($(this).width() - $('.otherworld-popup', this).width()) / 2;
                            $('.otherworld-popup', this)[0].style.left = lOffset + "px";
                        })
                        .catch((error) => {
                            $('.popover-body', this).html("-");
                        });
                    }
                } else {
                    $('.otherworld-popup', this).addClass("d-none").removeClass("show");
                }
            })
        @endisset

        @auth
        @can('discord_bot_beta')
        function changeFollow() {
            var icon = $('#follow-icon');
            axios.post('{{ route('web.follow') }}',{
                model: 'Player',
                id: '{{ $playerData->playerID }}',
                world: '{{ $worldData->id }}',
            })
                .then((response) => {
                    if(icon.hasClass('far')){
                        icon.removeClass('far text-muted').addClass('fas text-warning').attr('style','cursor:pointer; text-shadow: 0 0 15px #000;');
                    }else {
                        icon.removeClass('fas text-warning').addClass('far text-muted').attr('style', 'cursor:pointer;');
                    }
                })
                .catch((error) => {

                });
        }
        @endcan('discord_bot_beta')
        @endauth
    </script>
    {!! $chartJS !!}
@endpush
