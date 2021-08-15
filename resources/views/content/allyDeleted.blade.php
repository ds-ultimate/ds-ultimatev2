@extends('layouts.app')

@section('titel', ucfirst(__('ui.titel.ally')).': '.\App\Util\BasicFunctions::decodeName($allyTopData->name))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('ui.titel.ally')).': '.\App\Util\BasicFunctions::decodeName($allyTopData->name).' ['.\App\Util\BasicFunctions::decodeName($allyTopData->tag).']' }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('ui.titel.ally')).': ' }}
            </h1>
            <h4>
                {{ \App\Util\BasicFunctions::decodeName($allyTopData->name) }}
                <br>
                [{{ \App\Util\BasicFunctions::decodeName($allyTopData->tag) }}]
            </h4>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Informationen -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ucfirst(__('ui.tabletitel.info'))}}</h4>
                    <h5 class="card-subtitle">{{__('ui.tabletitel.general')}}</h5>
                    <table id="data_top1" class="table table-bordered no-wrap w-100">
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
                            <th>
                                {{ \App\Util\BasicFunctions::numberConv($allyTopData->rank_top) }}
                                <br><span class="small float-right">{{ $allyTopData->getDate("rank") }}</span>
                            </th>
                            <td>{{ \App\Util\BasicFunctions::decodeName($allyTopData->name) }}</td>
                            <td>{{ \App\Util\BasicFunctions::decodeName($allyTopData->tag) }}</td>
                            <td>
                                {{ \App\Util\BasicFunctions::numberConv($allyTopData->points_top) }}
                                <br><span class="small float-right">{{ $allyTopData->getDate("points") }}</span>
                            </td>
                            <td>
                                {{ \App\Util\BasicFunctions::numberConv($allyTopData->village_count_top) }}
                                <br><span class="small float-right">{{ $allyTopData->getDate("village_count") }}</span>
                            </td>
                            <td>
                                {{ \App\Util\BasicFunctions::numberConv($allyTopData->member_count_top) }}
                                <br><span class="small float-right">{{ $allyTopData->getDate("member_count") }}</span>
                            </td>
                            <td>{{ ($allyTopData->points_top != 0 && $allyTopData->member_count_top != 0)?\App\Util\BasicFunctions::numberConv($allyTopData->points_top/$allyTopData->member_count_top): '-' }}</td>
                            <td>{{ ($allyTopData->points_top != 0 && $allyTopData->village_count_top != 0)?\App\Util\BasicFunctions::numberConv($allyTopData->points_top/$allyTopData->village_count_top): '-' }}</td>
                            <td>{!! \App\Util\BasicFunctions::linkWinLoose($worldData, $allyTopData->allyID, $conquer, 'allyConquer') !!}</td>
                            <td>{!! \App\Util\BasicFunctions::linkWinLoose($worldData, $allyTopData->allyID, $allyChanges, 'allyAllyChanges') !!}</td>
                        </tr>
                        </tbody>
                    </table>
                    <br>
                    <h5 class="card-subtitle">{{__('ui.tabletitel.bashStats')}}</h5>
                    <table id="data_top2" class="table table-bordered no-wrap w-100">
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
                            <th>
                                {{ \App\Util\BasicFunctions::numberConv($allyTopData->gesBashRank_top) }}
                                <br><span class="small float-right">{{ $allyTopData->getDate("gesBashRank") }}</span>
                            </th>
                            <td>
                                {{ \App\Util\BasicFunctions::numberConv($allyTopData->gesBash_top) }}
                                <br><span class="small float-right">{{ $allyTopData->getDate("gesBash") }}</span>
                            </td>
                            <td>{{ ($allyTopData->points != 0)?(\App\Util\BasicFunctions::numberConv(($allyTopData->gesBash_top/$allyTopData->points_top)*100)):('-') }}%</td>
                            <th>
                                {{ \App\Util\BasicFunctions::numberConv($allyTopData->offBashRank_top) }}
                                <br><span class="small float-right">{{ $allyTopData->getDate("offBashRank") }}</span>
                            </th>
                            <td>
                                {{ \App\Util\BasicFunctions::numberConv($allyTopData->offBash_top) }}
                                <br><span class="small float-right">{{ $allyTopData->getDate("offBash") }}</span>
                            </td>
                            <th>
                                {{ \App\Util\BasicFunctions::numberConv($allyTopData->defBashRank_top) }}
                                <br><span class="small float-right">{{ $allyTopData->getDate("defBashRank") }}</span>
                            </th>
                            <td>
                                {{ \App\Util\BasicFunctions::numberConv($allyTopData->defBash_top) }}
                                <br><span class="small float-right">{{ $allyTopData->getDate("defBash") }}</span>
                            </td>
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
        } );
    </script>
    {!! $chartJS !!}
@endpush
