@extends('layouts.app')

@section('titel', ucfirst(__('ui.titel.player')).': '.\App\Util\BasicFunctions::decodeName($playerTopData->name))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('ui.titel.player')).': '.\App\Util\BasicFunctions::decodeName($playerTopData->name) }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('ui.titel.player')).': ' }}
            </h1>
            <h4>
                {{ \App\Util\BasicFunctions::decodeName($playerTopData->name) }}
            </h4>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Informationen -->
        <div class="col-12">
            <div class="card">
                <div class="card-body tab-content">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="card-title">{{ucfirst(__('ui.tabletitel.info'))}}</h4>
                        </div>
                        <div class="col-12 mt-3">
                            <h5 class="card-subtitle">{{__('ui.tabletitel.general')}}</h5>
                            <table id="data_top1" class="table table-bordered no-wrap w-100">
                                <thead>
                                <tr>
                                    <th class="all">{{ ucfirst(__('ui.table.rank')) }}</th>
                                    <th class="all">{{ ucfirst(__('ui.table.name')) }}</th>
                                    <th class="desktop">{{ ucfirst(__('ui.table.ally')) }}</th>
                                    <th class="desktop">{{ ucfirst(__('ui.table.points')) }}</th>
                                    <th class="desktop">{{ ucfirst(__('ui.table.villages')) }}</th>
                                    <th class="desktop">{{ ucfirst(__('ui.table.avgVillage')) }}</th>
                                    <th class="desktop">{{ ucfirst(__('ui.table.conquer')) }}</th>
                                    <th class="desktop">{{ ucfirst(__('ui.table.allyChanges')) }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th>
                                        {{ \App\Util\BasicFunctions::numberConv($playerTopData->rank_top) }}
                                        <br><span class="small float-right">{{ $playerTopData->getDate("rank") }}</span>
                                    </th>
                                    <td>{{ \App\Util\BasicFunctions::decodeName($playerTopData->name) }}</td>
                                    <td>{!! ($playerTopData->ally_id != 0 && $playerTopData->allyTop !== null)?\App\Util\BasicFunctions::linkAlly($worldData, $playerTopData->ally_id, \App\Util\BasicFunctions::outputName($playerTopData->allyTop->tag)) : '-' !!}</td>
                                    <td>
                                        {{ \App\Util\BasicFunctions::numberConv($playerTopData->points_top) }}
                                        <br><span class="small float-right">{{ $playerTopData->getDate("points") }}</span>
                                    </td>
                                    <td>
                                        {{ \App\Util\BasicFunctions::numberConv($playerTopData->village_count_top) }}
                                        <br><span class="small float-right">{{ $playerTopData->getDate("village_count") }}</span>
                                    </td>
                                    <td>{{ ($playerTopData->village_count_top != 0)?\App\Util\BasicFunctions::numberConv($playerTopData->points_top/$playerTopData->village_count_top): '-' }}</td>
                                    <td>{!! \App\Util\BasicFunctions::linkWinLoose($worldData, $playerTopData->playerID, $conquer, 'playerConquer') !!}</td>
                                    <td>{!! \App\Util\BasicFunctions::linkWinLoose($worldData, $playerTopData->playerID, $allyChanges, 'playerAllyChanges') !!}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-12 mt-3">
                            <h5 class="card-subtitle">{{__('ui.tabletitel.bashStats')}}</h5>
                            <table id="data_top2" class="table table-bordered no-wrap w-100" style="border: 1px solid #b1b1b1">
                                <thead>
                                <tr>
                                    <th class="all" style="border-bottom:1px solid #dee2e6" colspan="3" width="50%">{{ ucfirst(__('ui.tabletitel.bashStats')) }} - {{__('ui.table.bashGes') }}</th>
                                    <th class="desktop" style="border-bottom:1px solid #dee2e6; border-left: 1px solid #b1b1b1" colspan="3">{{ ucfirst(__('ui.tabletitel.bashStats')) }} - {{__('ui.table.bashOff') }}</th>
                                </tr>
                                <tr>
                                    <th>{{ ucfirst(__('ui.table.rank')) }}</th>
                                    <th>{{ ucfirst(__('ui.table.points')) }}</th>
                                    <th>{{ ucfirst(__('ui.table.bashPointsRatio')) }}</th>
                                    <th style="border-left: 1px solid #b1b1b1">{{ ucfirst(__('ui.table.rank')) }}</th>
                                    <th>{{ ucfirst(__('ui.table.points')) }}</th>
                                    <th>{{ ucfirst(__('ui.table.bashPointsRatio')) }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        {{ \App\Util\BasicFunctions::numberConv($playerTopData->gesBashRank_top) }}
                                        <br><span class="small float-right">{{ $playerTopData->getDate("gesBashRank") }}</span>
                                    </td>
                                    <td>
                                        {{ \App\Util\BasicFunctions::numberConv($playerTopData->gesBash_top) }}
                                        <br><span class="small float-right">{{ $playerTopData->getDate("gesBash") }}</span>
                                    </td>
                                    <td>{{ ($playerTopData->points_top != 0)?\App\Util\BasicFunctions::numberConv(($playerTopData->gesBash_top/$playerTopData->points_top)*100): ('-') }}%</td>
                                    <td style="border-left: 1px solid #b1b1b1">
                                        {{ \App\Util\BasicFunctions::numberConv($playerTopData->offBashRank_top) }}
                                        <br><span class="small float-right">{{ $playerTopData->getDate("offBashRank") }}</span>
                                    </td>
                                    <td>
                                        {{ \App\Util\BasicFunctions::numberConv($playerTopData->offBash_top) }}
                                        <br><span class="small float-right">{{ $playerTopData->getDate("offBash") }}</span>
                                    </td>
                                    <td>{{ ($playerTopData->points_top != 0)?\App\Util\BasicFunctions::numberConv(($playerTopData->offBash_top/$playerTopData->points_top)*100): ('-') }}%</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-12 mt-3">
                            <table id="data_top3" class="table table-bordered no-wrap w-100" style="border: 1px solid #b1b1b1">
                                <thead>
                                <tr>
                                    <th class="all" style="border-bottom:1px solid #dee2e6" colspan="3" width="50%">{{ ucfirst(__('ui.tabletitel.bashStats')) }} - {{__('ui.table.bashDeff') }}</th>
                                    <th class="desktop" style="border-bottom:1px solid #dee2e6; border-left: 1px solid #b1b1b1" colspan="3">{{ ucfirst(__('ui.tabletitel.bashStats')) }} - {{__('ui.table.supDeff') }}</th>
                                </tr>
                                <tr>
                                    <th>{{ ucfirst(__('ui.table.rank')) }}</th>
                                    <th>{{ ucfirst(__('ui.table.points')) }}</th>
                                    <th>{{ ucfirst(__('ui.table.bashPointsRatio')) }}</th>
                                    <th style="border-left: 1px solid #b1b1b1">{{ ucfirst(__('ui.table.rank')) }}</th>
                                    <th>{{ ucfirst(__('ui.table.points')) }}</th>
                                    <th>{{ ucfirst(__('ui.table.bashPointsRatio')) }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        {{ \App\Util\BasicFunctions::numberConv($playerTopData->defBashRank_top) }}
                                        <br><span class="small float-right">{{ $playerTopData->getDate("defBashRank") }}</span>
                                    </td>
                                    <td>
                                        {{ \App\Util\BasicFunctions::numberConv($playerTopData->defBash_top) }}
                                        <br><span class="small float-right">{{ $playerTopData->getDate("defBash") }}</span>
                                    </td>
                                    <td>{{ ($playerTopData->points_top != 0)?\App\Util\BasicFunctions::numberConv(($playerTopData->defBash_top/$playerTopData->points_top)*100): ('-') }}%</td>
                                    <td style="border-left: 1px solid #b1b1b1">{{ \App\Util\BasicFunctions::numberConv($playerTopData->supBashRank_top) }}</td>
                                    <td>
                                        {{ \App\Util\BasicFunctions::numberConv($playerTopData->supBash_top) }}
                                        <br><span class="small float-right">{{ $playerTopData->getDate("supBashRank") }}</span>
                                    </td>
                                    <td>{{ ($playerTopData->points_top != 0)?\App\Util\BasicFunctions::numberConv(($playerTopData->supBash_top/$playerTopData->points_top)*100): ('-') }}%</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!--<div class="col">
                            <a href="javascript:void(0)" class="text-secondary font-weight-bold" onclick="$('#signatureContent').toggle()">{{ ucfirst(__('ui.signature')) }}</a>
                        </div>-->
                    </div>
                    <!--<div id="signatureContent" class="input-group mt-2 float-right" style="display: none;">
                        <input id="signature" type="text" class="form-control" value="[url={{ route('player', [$server, $worldData->name, $playerTopData->playerID]) }}][img]{{ route('api.signature', [$server, $worldData->name, 'player', $playerTopData->playerID]) }}[/img][/url]" aria-label="Recipient's username" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <span class="input-group-text" style="cursor:pointer" id="basic-addon2" onclick="copy('signature')"><i class="far fa-copy"></i></span>
                        </div>
                    </div>-->
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

@push('js')
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
            $('#data_top1').DataTable({
                dom: 't',
                ordering: false,
                paging: false,
                responsive: true,

                keys: true, //enable KeyTable extension
            });

            $('#data_top2').DataTable({
                dom: 't',
                ordering: false,
                paging: false,
                responsive: true,

                keys: true, //enable KeyTable extension
            });

            $('#data_top3').DataTable({
                dom: 't',
                ordering: false,
                paging: false,
                responsive: true,

                keys: true, //enable KeyTable extension
            });
        });
    </script>
    {!! $chartJS !!}
@endpush
