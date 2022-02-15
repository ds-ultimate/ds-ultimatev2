@extends('layouts.app')

@section('titel', ucfirst(__('ui.titel.player')).': '.\App\Util\BasicFunctions::decodeName($playerData->name))

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
                    <!-- BEGIN NORMAL Table -->
                    <div class="tab-pane fade show active" id="stats" role="tabpanel" aria-labelledby="stats-tab">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <h4 class="card-title">{{ucfirst(__('ui.tabletitel.info'))}}</h4>
                            </div>
                            <div class="col-12 col-md-6">
                                <span class="float-right">
                                    <a href="{{ $playerData->linkIngame($worldData, false) }}" target="_blank" class="btn btn-primary btn-sm">{{ __('ui.ingame.normal') }}</a>
                                    <a href="{{ $playerData->linkIngame($worldData, true) }}" target="_blank" class="btn btn-primary btn-sm">{{ __('ui.ingame.guest') }}</a>
                                </span>
                            </div>
                            <div class="col-12 mt-3">
                                <h5 class="card-subtitle">{{__('ui.tabletitel.general')}}</h5>
                                <table id="data1" class="table table-bordered no-wrap w-100">
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
                                        <th>{{ \App\Util\BasicFunctions::numberConv($playerData->rank) }}</th>
                                        <td>{{ \App\Util\BasicFunctions::decodeName($playerData->name) }}</td>
                                        <td>{!! ($playerData->ally_id != 0 && $playerData->allyLatest !== null)?\App\Util\BasicFunctions::linkAlly($worldData, $playerData->ally_id, \App\Util\BasicFunctions::outputName($playerData->allyLatest->tag)) : '-' !!}</td>
                                        <td>{{ \App\Util\BasicFunctions::numberConv($playerData->points) }}</td>
                                        <td>{{ \App\Util\BasicFunctions::numberConv($playerData->village_count) }}</td>
                                        <td>{{ ($playerData->village_count != 0)?\App\Util\BasicFunctions::numberConv($playerData->points/$playerData->village_count): '-' }}</td>
                                        <td>{!! \App\Util\BasicFunctions::linkWinLoose($worldData, $playerData->playerID, $conquer, 'playerConquer') !!}</td>
                                        <td>{!! \App\Util\BasicFunctions::linkWinLoose($worldData, $playerData->playerID, $allyChanges, 'playerAllyChanges') !!}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-12 mt-3">
                                <h5 class="card-subtitle">{{__('ui.tabletitel.bashStats')}}</h5>
                                <table id="data2" class="table table-bordered no-wrap w-100" style="border: 1px solid #b1b1b1">
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
                                        <td>{{ \App\Util\BasicFunctions::numberConv($playerData->gesBashRank) }}</td>
                                        <td>{{ \App\Util\BasicFunctions::numberConv($playerData->gesBash) }}</td>
                                        <td>{{ ($playerData->points != 0)?\App\Util\BasicFunctions::numberConv(($playerData->gesBash/$playerData->points)*100): ('-') }}%</td>
                                        <td style="border-left: 1px solid #b1b1b1">{{ \App\Util\BasicFunctions::numberConv($playerData->offBashRank) }}</td>
                                        <td>{{ \App\Util\BasicFunctions::numberConv($playerData->offBash) }}</td>
                                        <td>{{ ($playerData->points != 0)?\App\Util\BasicFunctions::numberConv(($playerData->offBash/$playerData->points)*100): ('-') }}%</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-12 mt-3">
                                <table id="data3" class="table table-bordered no-wrap w-100" style="border: 1px solid #b1b1b1">
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
                                        <td>{{ \App\Util\BasicFunctions::numberConv($playerData->defBashRank) }}</td>
                                        <td>{{ \App\Util\BasicFunctions::numberConv($playerData->defBash) }}</td>
                                        <td>{{ ($playerData->points != 0)?\App\Util\BasicFunctions::numberConv(($playerData->defBash/$playerData->points)*100): ('-') }}%</td>
                                        <td style="border-left: 1px solid #b1b1b1">{{ \App\Util\BasicFunctions::numberConv($playerData->supBashRank) }}</td>
                                        <td>{{ \App\Util\BasicFunctions::numberConv($playerData->supBash) }}</td>
                                        <td>{{ ($playerData->points != 0)?\App\Util\BasicFunctions::numberConv(($playerData->supBash/$playerData->points)*100): ('-') }}%</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            @isset($playerOtherServers)
                                <div class="col-12 mt-3 mb-3">
                                    <h4 class="card-title">{{ __('ui.otherWorldsPlayer')}}</h4>
                                    @foreach($playerOtherServers->getWorlds() as $worldModel)
                                        <div class="otherworld d-inline-block mt-1 position-relative" data-worldid="{{ $worldModel->id }}">
                                            {!! \App\Util\BasicFunctions::linkPlayer($worldModel, $playerData->playerID, \App\Util\BasicFunctions::escape($worldModel->shortName()), 'btn btn-primary btn-sm' . (($worldModel->name == $worldData->name)?(' active'):('')), true) !!}
                                            <div class="otherworld-popup popover fade bs-popover-bottom d-none" style="top: 100%">
                                                <div class="arrow m-0" style="left: calc(50% - 0.5rem)"></div>
                                                <div class="popover-body text-nowrap">
                                                    <h1><i class="fas fa-spinner fa-spin"></i></h1>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endisset
                            <div class="col">
                                <a href="javascript:void(0)" class="text-secondary font-weight-bold" onclick="$('#signatureContent').toggle()">{{ ucfirst(__('ui.signature')) }}</a>
                            </div>
                            <div class="col">
                            @auth
                                @can('discord_bot_beta')
                                @if($playerData->follows()->where(['user_id' => Auth::user()->id, 'world_id' => $worldData->id])->count() > 0)
                                    <div class="float-right"><a id="follow-icon" style="cursor:pointer; text-shadow: 0 0 15px #000;" onclick="changeFollow()" class="fas fa-star text-warning">{{__('ui.player.discordNotification.addFollow')}}</a></div>
                                @else
                                    <div class="float-right"><a id="follow-icon" style="cursor:pointer" onclick="changeFollow()" class="far fa-star text-muted">{{__('ui.player.discordNotification.addFollow')}}</a></div>
                                @endif
                                @endcan
                            @endauth
                            </div>
                        </div>
                        <div id="signatureContent" class="input-group mt-2 float-right" style="display: none;">
                            <div class="input-group-prepend">
                                <a class="btn btn-primary" target="_blank" href="{{ route('api.signature', [$server, $worldData->name, 'player', $playerData->playerID]) }}">{{ __('ui.sigPreview') }}</a>
                            </div>
                            <input id="signature" type="text" class="form-control" value="[url={{ route('player', [$server, $worldData->name, $playerData->playerID]) }}][img]{{ route('api.signature', [$server, $worldData->name, 'player', $playerData->playerID]) }}[/img][/url]" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <span class="input-group-text" style="cursor:pointer" id="basic-addon2" onclick="copy('signature')"><i class="far fa-copy"></i></span>
                            </div>
                        </div>
                    </div>
                    
                    @isset($playerTopData)
                    <!-- BEGIN TOP Table -->
                    <div class="tab-pane fade" id="tops" role="tabpanel" aria-labelledby="tops-tab">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <h4 class="card-title">{{ucfirst(__('ui.tabletitel.info'))}}</h4>
                            </div>
                            <div class="col-12 col-md-6">
                                <span class="float-right">
                                    <a href="{{ $playerTopData->linkIngame($worldData, false) }}" target="_blank" class="btn btn-primary btn-sm">{{ __('ui.ingame.normal') }}</a>
                                    <a href="{{ $playerTopData->linkIngame($worldData, true) }}" target="_blank" class="btn btn-primary btn-sm">{{ __('ui.ingame.guest') }}</a>
                                </span>
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
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th>
                                            {{ \App\Util\BasicFunctions::numberConv($playerTopData->rank_top) }}
                                            <br><span class="small float-right">{{ $playerTopData->getDate("rank") }}</span>
                                        </th>
                                        <td>{{ \App\Util\BasicFunctions::decodeName($playerTopData->name) }}</td>
                                        <td>{!! ($playerData->ally_id != 0 && $playerData->allyTop !== null)?\App\Util\BasicFunctions::linkAlly($worldData, $playerData->ally_id, \App\Util\BasicFunctions::outputName($playerData->allyTop->tag)) : '-' !!}</td>
                                        <td>
                                            {{ \App\Util\BasicFunctions::numberConv($playerTopData->points_top) }}
                                            <br><span class="small float-right">{{ $playerTopData->getDate("points") }}</span>
                                        </td>
                                        <td>
                                            {{ \App\Util\BasicFunctions::numberConv($playerTopData->village_count_top) }}
                                            <br><span class="small float-right">{{ $playerTopData->getDate("village_count") }}</span>
                                        </td>
                                        <td>{{ ($playerTopData->village_count_top != 0)?\App\Util\BasicFunctions::numberConv($playerTopData->points_top/$playerTopData->village_count_top): '-' }}</td>
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
                            @isset($playerOtherServers)
                                <div class="col-12 mt-3 mb-3">
                                    <h4 class="card-title">{{ __('ui.otherWorldsPlayer')}}</h4>
                                    @foreach($playerOtherServers->getWorlds() as $worldModel)
                                        <div class="otherworld d-inline-block mt-1 position-relative" data-worldid="{{ $worldModel->id }}">
                                            {!! \App\Util\BasicFunctions::linkPlayer($worldModel, $playerData->playerID, \App\Util\BasicFunctions::escape($worldModel->shortName()), 'btn btn-primary btn-sm' . (($worldModel->name == $worldData->name)?(' active'):('')), true) !!}
                                            <div class="otherworld-popup popover fade bs-popover-bottom d-none" style="top: 100%">
                                                <div class="arrow m-0" style="left: calc(50% - 0.5rem)"></div>
                                                <div class="popover-body text-nowrap">
                                                    <h1><i class="fas fa-spinner fa-spin"></i></h1>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endisset
                            <div class="col">
                                <a href="javascript:void(0)" class="text-secondary font-weight-bold" onclick="$('#signatureContentTop').toggle()">{{ ucfirst(__('ui.signature')) }}</a>
                            </div>
                            <div class="col">
                            @auth
                                @can('discord_bot_beta')
                                @if($playerData->follows()->where(['user_id' => Auth::user()->id, 'world_id' => $worldData->id])->count() > 0)
                                    <div class="float-right"><a id="follow-icon" style="cursor:pointer; text-shadow: 0 0 15px #000;" onclick="changeFollow()" class="fas fa-star text-warning">{{__('ui.player.discordNotification.addFollow')}}</a></div>
                                @else
                                    <div class="float-right"><a id="follow-icon" style="cursor:pointer" onclick="changeFollow()" class="far fa-star text-muted">{{__('ui.player.discordNotification.addFollow')}}</a></div>
                                @endif
                                @endcan
                            @endauth
                            </div>
                        </div>
                        <div id="signatureContentTop" class="input-group mt-2 float-right" style="display: none;">
                            <div class="input-group-prepend">
                                <a class="btn btn-primary" target="_blank" href="{{ route('api.signature', [$server, $worldData->name, 'player', $playerData->playerID]) }}">{{ __('ui.sigPreview') }}</a>
                            </div>
                            <input id="signatureTop" type="text" class="form-control" value="[url={{ route('player', [$server, $worldData->name, $playerData->playerID]) }}][img]{{ route('api.signature', [$server, $worldData->name, 'player', $playerData->playerID]) }}[/img][/url]">
                            <div class="input-group-append">
                                <span class="input-group-text" style="cursor:pointer" onclick="copy('signatureTop')"><i class="far fa-copy"></i></span>
                            </div>
                        </div>
                    </div>
                    <!-- END TOP Table -->
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
                            <div class="col-12 mt-3">
                                <table id="history_table" class="table table-hover table-sm w-100">
                                    <thead>
                                    <tr>
                                        <th>{{ ucfirst(__('ui.table.date')) }}</th>
                                        <th>{{ ucfirst(__('ui.table.player')) }}</th>
                                        <th>{{ ucfirst(__('ui.table.ally')) }}</th>
                                        <th>{{ ucfirst(__('ui.table.rank')) }}</th>
                                        <th>{{ ucfirst(__('ui.table.points')) }}</th>
                                        <th>{{ ucfirst(__('ui.table.villages')) }}</th>
                                        <th>{{ ucfirst(__('ui.table.bashAllS')) }}</th>
                                        <th>{{ ucfirst(__('ui.table.bashAttS')) }}</th>
                                        <th>{{ ucfirst(__('ui.table.bashDefS')) }}</th>
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
        
        $('#hist-tab').on('click', function() {
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
            $.extend( $.fn.dataTable.defaults, {
                responsive: true
            });

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

            $('#data3').DataTable({
                dom: 't',
                ordering: false,
                paging: false,
                responsive: true,

                keys: true, //enable KeyTable extension
            });

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
                    { "data": "continent", "orderable": false},
                    { "data": "coordinates", "orderable": false},
                    { "data": "bonus_id", "render": function (value, type, row) {return row.bonus}},
                ],
                responsive: true,
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
