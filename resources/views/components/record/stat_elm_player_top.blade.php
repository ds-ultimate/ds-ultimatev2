@props(['data', 'worldData', 'conquer', 'allyChanges', 'playerOtherServers', 'exists'])

<div class="row">
    <div class="col-12 col-md-6">
        <h4 class="card-title">{{ucfirst(__('ui.tabletitel.info'))}}</h4>
    </div>
    <div class="col-12 col-md-6">
        @if($exists == "true")
        <span class="float-right">
            <a href="{{ $data->linkIngame($worldData, false) }}" target="_blank" class="btn btn-primary btn-sm">{{ __('ui.ingame.normal') }}</a>
            <a href="{{ $data->linkIngame($worldData, true) }}" target="_blank" class="btn btn-primary btn-sm">{{ __('ui.ingame.guest') }}</a>
        </span>
        @endif
    </div>
    <div class="col-12 d-none d-lg-block mt-3">
        <h5 class="card-subtitle">{{__('ui.tabletitel.general')}}</h5>
        <table class="table table-bordered nowrap w-100">
            <thead><tr>
                <th>{{ ucfirst(__('ui.table.rank')) }}</th>
                <th>{{ ucfirst(__('ui.table.name')) }}</th>
                <th>{{ ucfirst(__('ui.table.ally')) }}</th>
                <th>{{ ucfirst(__('ui.table.points')) }}</th>
                <th>{{ ucfirst(__('ui.table.villages')) }}</th>
                <th>{{ ucfirst(__('ui.table.avgVillage')) }}</th>
                <th>{{ ucfirst(__('ui.table.conquer')) }}</th>
                <th>{{ ucfirst(__('ui.table.allyChanges')) }}</th>
            </tr></thead>
            <tbody><tr>
                <th>
                    {{ \App\Util\BasicFunctions::numberConv($data->rank_top) }}
                    <br><span class="small float-right">{{ $data->getDate("rank") }}</span>
                </th>
                <td>{{ \App\Util\BasicFunctions::decodeName($data->name) }}</td>
                <td>{!! ($data->ally_id != 0 && $data->allyLatest !== null)?\App\Util\BasicFunctions::linkAlly($worldData, $data->ally_id, \App\Util\BasicFunctions::outputName($data->allyLatest->tag)) : '-' !!}</td>
                <td>
                    {{ \App\Util\BasicFunctions::numberConv($data->points_top) }}
                    <br><span class="small float-right">{{ $data->getDate("points") }}</span>
                </td>
                <td>
                    {{ \App\Util\BasicFunctions::numberConv($data->village_count_top) }}
                    <br><span class="small float-right">{{ $data->getDate("village_count") }}</span>
                </td>
                <td>{{ ($data->village_count_top != 0)?\App\Util\BasicFunctions::numberConv($data->points_top/$data->village_count_top): '-' }}</td>
                <td>{!! \App\Util\BasicFunctions::linkWinLoose($worldData, $data->playerID, $conquer, 'playerConquer', tooltipSpace: 'ui.conquer.highlight') !!}</td>
                <td>{!! \App\Util\BasicFunctions::linkWinLoose($worldData, $data->playerID, $allyChanges, 'playerAllyChanges') !!}</td>
            </tr></tbody>
        </table>
    </div>
    <div class="col-12 d-none d-lg-block mt-3">
        <h5 class="card-subtitle">{{__('ui.tabletitel.bashStats')}}</h5>
        <table class="table table-bordered nowrap w-100">
            <thead><tr>
                <th colspan="3" width="50%">{{ ucfirst(__('ui.tabletitel.bashStats')) }} - {{__('ui.table.bashGes') }}</th>
                <th colspan="3">{{ ucfirst(__('ui.tabletitel.bashStats')) }} - {{__('ui.table.bashOff') }}</th>
            </tr>
            <tr>
                <th>{{ ucfirst(__('ui.table.rank')) }}</th>
                <th>{{ ucfirst(__('ui.table.points')) }}</th>
                <th>{{ ucfirst(__('ui.table.bashPointsRatio')) }}</th>
                <th>{{ ucfirst(__('ui.table.rank')) }}</th>
                <th>{{ ucfirst(__('ui.table.points')) }}</th>
                <th>{{ ucfirst(__('ui.table.bashPointsRatio')) }}</th>
            </tr></thead>
            <tbody><tr>
                <td>
                    {{ \App\Util\BasicFunctions::numberConv($data->gesBashRank_top) }}
                    <br><span class="small float-right">{{ $data->getDate("gesBashRank") }}</span>
                </td>
                <td>
                    {{ \App\Util\BasicFunctions::numberConv($data->gesBash_top) }}
                    <br><span class="small float-right">{{ $data->getDate("gesBash") }}</span>
                </td>
                <td>{{ ($data->points_top != 0)?\App\Util\BasicFunctions::numberConv(($data->gesBash_top/$data->points_top)*100): ('-') }}%</td>
                <td>
                    {{ \App\Util\BasicFunctions::numberConv($data->offBashRank_top) }}
                    <br><span class="small float-right">{{ $data->getDate("offBashRank") }}</span>
                </td>
                <td>
                    {{ \App\Util\BasicFunctions::numberConv($data->offBash_top) }}
                    <br><span class="small float-right">{{ $data->getDate("offBash") }}</span>
                </td>
                <td>{{ ($data->points_top != 0)?\App\Util\BasicFunctions::numberConv(($data->offBash_top/$data->points_top)*100): ('-') }}%</td>
            </tr></tbody>
        </table>
    </div>
    <div class="col-12 d-none d-lg-block mt-3">
        <table class="table table-bordered nowrap w-100">
            <thead><tr>
                <th colspan="3" width="50%">{{ ucfirst(__('ui.tabletitel.bashStats')) }} - {{__('ui.table.bashDeff') }}</th>
                <th colspan="3">{{ ucfirst(__('ui.tabletitel.bashStats')) }} - {{__('ui.table.supDeff') }}</th>
            </tr>
            <tr>
                <th>{{ ucfirst(__('ui.table.rank')) }}</th>
                <th>{{ ucfirst(__('ui.table.points')) }}</th>
                <th>{{ ucfirst(__('ui.table.bashPointsRatio')) }}</th>
                <th>{{ ucfirst(__('ui.table.rank')) }}</th>
                <th>{{ ucfirst(__('ui.table.points')) }}</th>
                <th>{{ ucfirst(__('ui.table.bashPointsRatio')) }}</th>
            </tr></thead>
            <tbody><tr>
                <td>
                    {{ \App\Util\BasicFunctions::numberConv($data->defBashRank_top) }}
                    <br><span class="small float-right">{{ $data->getDate("defBashRank") }}</span>
                </td>
                <td>
                    {{ \App\Util\BasicFunctions::numberConv($data->defBash_top) }}
                    <br><span class="small float-right">{{ $data->getDate("defBash") }}</span>
                </td>
                <td>{{ ($data->points_top != 0)?\App\Util\BasicFunctions::numberConv(($data->defBash_top/$data->points_top)*100): ('-') }}%</td>
                <td>
                    {{ \App\Util\BasicFunctions::numberConv($data->supBashRank_top) }}
                    <br><span class="small float-right">{{ $data->getDate("supBashRank") }}</span>
                </td>
                <td>
                    {{ \App\Util\BasicFunctions::numberConv($data->supBash_top) }}
                    <br><span class="small float-right">{{ $data->getDate("supBash") }}</span>
                </td>
                <td>{{ ($data->points_top != 0)?\App\Util\BasicFunctions::numberConv(($data->supBash_top/$data->points_top)*100): ('-') }}%</td>
            </tr></tbody>
        </table>
    </div>
    <div class="col-12 d-lg-none mt-3">
        <h5 class="card-subtitle">{{__('ui.tabletitel.general')}}</h5>
        <table class="table table-striped nowrap w-100 tbl-stretched">
            <tbody>
            <tr><th>{{ ucfirst(__('ui.table.rank')) }}</th><td>
                {{ \App\Util\BasicFunctions::numberConv($data->rank_top) }} {{ $data->getDate("rank") }}
            </td></tr>
            <tr><th>{{ ucfirst(__('ui.table.name')) }}</th><td>{{ \App\Util\BasicFunctions::decodeName($data->name) }}</td></tr>
            <tr><th>{{ ucfirst(__('ui.table.ally')) }}</th><td>{!! ($data->ally_id != 0 && $data->allyLatest !== null)?\App\Util\BasicFunctions::linkAlly($worldData, $data->ally_id, \App\Util\BasicFunctions::outputName($data->allyLatest->tag)) : '-' !!}</td></tr>
            <tr><th>{{ ucfirst(__('ui.table.points')) }}</th><td>
                {{ \App\Util\BasicFunctions::numberConv($data->points_top) }} {{ $data->getDate("points") }}
            </td></tr>
            <tr><th>{{ ucfirst(__('ui.table.villages')) }}</th><td>
                {{ \App\Util\BasicFunctions::numberConv($data->village_count_top) }} {{ $data->getDate("village_count") }}
            </td></tr>
            <tr><th>{{ ucfirst(__('ui.table.avgVillage')) }}</th><td>{{ ($data->village_count_top != 0)?\App\Util\BasicFunctions::numberConv($data->points_top/$data->village_count_top): '-' }}</td></tr>
            <tr><th>{{ ucfirst(__('ui.table.conquer')) }}</th><td>{!! \App\Util\BasicFunctions::linkWinLoose($worldData, $data->playerID, $conquer, 'playerConquer', tooltipSpace: 'ui.conquer.highlight') !!}</td></tr>
            <tr><th>{{ ucfirst(__('ui.table.allyChanges')) }}</th><td>{!! \App\Util\BasicFunctions::linkWinLoose($worldData, $data->playerID, $allyChanges, 'playerAllyChanges') !!}</td></tr>
            </tbody>
        </table>
        <h5 class="card-subtitle mt-4">{{__('ui.tabletitel.bashStats')}}</h5>
        <table class="table table-bordered nowrap w-100 tbl-stretched">
            <thead><tr>
                <th colspan="3">{{__('ui.table.bashGes') }}</th>
            </tr><tr>
                <th>{{ ucfirst(__('ui.table.rank')) }}</th>
                <th>{{ ucfirst(__('ui.table.points')) }}</th>
                <th>{{ ucfirst(__('ui.table.bashPointsRatio')) }}</th>
            </tr></thead>
            <tbody><tr>
                <td>
                    {{ \App\Util\BasicFunctions::numberConv($data->gesBashRank_top) }}
                    <br><span class="small float-right">{{ $data->getDate("gesBashRank") }}</span>
                </td>
                <td>
                    {{ \App\Util\BasicFunctions::numberConv($data->gesBash_top) }}
                    <br><span class="small float-right">{{ $data->getDate("gesBash") }}</span>
                </td>
                <td>{{ ($data->points_top != 0)?\App\Util\BasicFunctions::numberConv(($data->gesBash_top/$data->points_top)*100): ('-') }}%</td>
            </tr></tbody>
        </table>
        <table class="table table-bordered nowrap w-100 tbl-stretched mt-3">
            <thead><tr>
                <th colspan="3">{{__('ui.table.bashOff') }}</th>
            </tr><tr>
                <th>{{ ucfirst(__('ui.table.rank')) }}</th>
                <th>{{ ucfirst(__('ui.table.points')) }}</th>
                <th>{{ ucfirst(__('ui.table.bashPointsRatio')) }}</th>
            </tr></thead>
            <tbody><tr>
                <td>
                    {{ \App\Util\BasicFunctions::numberConv($data->offBashRank_top) }}
                    <br><span class="small float-right">{{ $data->getDate("offBashRank") }}</span>
                </td>
                <td>
                    {{ \App\Util\BasicFunctions::numberConv($data->offBash_top) }}
                    <br><span class="small float-right">{{ $data->getDate("offBash") }}</span>
                </td>
                <td>{{ ($data->points_top != 0)?\App\Util\BasicFunctions::numberConv(($data->offBash_top/$data->points_top)*100): ('-') }}%</td>
            </tr></tbody>
        </table>
        <table class="table table-bordered nowrap w-100 tbl-stretched mt-3">
            <thead><tr>
                <th colspan="3">{{__('ui.table.bashDeff') }}</th>
            </tr><tr>
                <th>{{ ucfirst(__('ui.table.rank')) }}</th>
                <th>{{ ucfirst(__('ui.table.points')) }}</th>
                <th>{{ ucfirst(__('ui.table.bashPointsRatio')) }}</th>
            </tr></thead>
            <tbody><tr>
                <td>
                    {{ \App\Util\BasicFunctions::numberConv($data->defBashRank_top) }}
                    <br><span class="small float-right">{{ $data->getDate("defBashRank") }}</span>
                </td>
                <td>
                    {{ \App\Util\BasicFunctions::numberConv($data->defBash_top) }}
                    <br><span class="small float-right">{{ $data->getDate("defBash") }}</span>
                </td>
                <td>{{ ($data->points_top != 0)?\App\Util\BasicFunctions::numberConv(($data->defBash_top/$data->points_top)*100): ('-') }}%</td>
            </tr></tbody>
        </table>
        <table class="table table-bordered nowrap w-100 tbl-stretched mt-3">
            <thead><tr>
                <th colspan="3">{{__('ui.table.supDeff') }}</th>
            </tr><tr>
                <th>{{ ucfirst(__('ui.table.rank')) }}</th>
                <th>{{ ucfirst(__('ui.table.points')) }}</th>
                <th>{{ ucfirst(__('ui.table.bashPointsRatio')) }}</th>
            </tr></thead>
            <tbody><tr>
                <td>
                    {{ \App\Util\BasicFunctions::numberConv($data->supBashRank_top) }}
                    <br><span class="small float-right">{{ $data->getDate("supBashRank") }}</span>
                </td>
                <td>
                    {{ \App\Util\BasicFunctions::numberConv($data->supBash_top) }}
                    <br><span class="small float-right">{{ $data->getDate("supBash") }}</span>
                </td>
                <td>{{ ($data->points_top != 0)?\App\Util\BasicFunctions::numberConv(($data->supBash_top/$data->points_top)*100): ('-') }}%</td>
            </tr></tbody>
        </table>
    </div>
    @isset($playerOtherServers)
        <div class="col-12 mt-3 mb-3">
            <h4 class="card-title">{{ __('ui.otherWorldsPlayer')}}</h4>
            @foreach($playerOtherServers->getWorlds() as $worldModel)
                <div class="otherworld d-inline-block mt-1 position-relative" data-worldid="{{ $worldModel->id }}">
                    {!! \App\Util\BasicFunctions::linkPlayer($worldModel, $data->playerID, \App\Util\BasicFunctions::escape($worldModel->shortName()), 'btn btn-primary btn-sm' . (($worldModel->name == $worldData->name)?(' active'):('')), true) !!}
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
</div>
<div id="signatureContentTop" class="input-group mt-2 float-right" style="display: none;">
    <div class="input-group-prepend">
        <a class="btn btn-primary" target="_blank" href="{{ route('api.signature', [$worldData->server->code, $worldData->name, 'player', $data->playerID]) }}">{{ __('ui.sigPreview') }}</a>
    </div>
    <input id="signatureTop" type="text" class="form-control" value="[url={{ route('player', [$worldData->server->code, $worldData->name, $data->playerID]) }}][img]{{ route('api.signature', [$worldData->server->code, $worldData->name, 'player', $data->playerID]) }}[/img][/url]">
    <div class="input-group-append">
        <span class="input-group-text" style="cursor:pointer" onclick="copy('signatureTop')"><i class="far fa-copy"></i></span>
    </div>
</div>
