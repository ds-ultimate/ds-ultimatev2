@props(['data', 'worldData', 'conquer', 'allyChanges', 'exists'])

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
                <th>{{ ucfirst(__('ui.table.tag')) }}</th>
                <th>{{ ucfirst(__('ui.table.points')) }}</th>
                <th>{{ ucfirst(__('ui.table.villages')) }}</th>
                <th>{{ ucfirst(__('ui.table.members')) }}</th>
                <th>{{ ucfirst(__('ui.table.avgPlayer')) }}</th>
                <th>{{ ucfirst(__('ui.table.avgVillage')) }}</th>
                <th>{{ ucfirst(__('ui.table.conquer')) }}</th>
                <th>{{ ucfirst(__('ui.table.allyChanges')) }}</th>
            </tr></thead>
            <tbody><tr>
                <th>
                    {{ \App\Util\BasicFunctions::numberConv( $data->rank_top) }}
                    <br><span class="small float-right">{{ $data->getDate("rank") }}</span>
                </th>
                <td>{{ \App\Util\BasicFunctions::decodeName( $data->name) }}</td>
                <td>{{ \App\Util\BasicFunctions::decodeName( $data->tag) }}</td>
                <td>
                    {{ \App\Util\BasicFunctions::numberConv( $data->points_top) }}
                    <br><span class="small float-right">{{ $data->getDate("points") }}</span>
                </td>
                <td>
                    {{ \App\Util\BasicFunctions::numberConv( $data->village_count_top) }}
                    <br><span class="small float-right">{{ $data->getDate("village_count") }}</span>
                </td>
                <td>
                    {{ \App\Util\BasicFunctions::numberConv( $data->member_count_top) }}
                    <br><span class="small float-right">{{ $data->getDate("member_count") }}</span>
                </td>
                <td>{{ ($data->member_count != 0)?\App\Util\BasicFunctions::numberConv( $data->points/ $data->member_count): '-' }}</td>
                <td>{{ ($data->village_count != 0)?\App\Util\BasicFunctions::numberConv( $data->points/ $data->village_count): '-' }}</td>
                <td>{!! \App\Util\BasicFunctions::linkWinLoose($worldData,  $data->allyID, $conquer, 'allyConquer', tooltipSpace: 'ui.conquer.highlight') !!}</td>
                <td>{!! \App\Util\BasicFunctions::linkWinLoose($worldData,  $data->allyID, $allyChanges, 'allyAllyChanges') !!}</td>
            </tr></tbody>
        </table>
    </div>
    <div class="col-12 d-none d-lg-block mt-3">
        <h5 class="card-subtitle">
            {{__('ui.tabletitel.bashStats')}}
            @if($exists == "true")
            <span class="float-right">
                <a class="h6" href="{{ route('allyBashRanking', [$worldData->server->code, $worldData->name,  $data->allyID]) }}">{{ __('ui.tabeltitel.allyBashRanking') }}</a>
            </span>
            @endif
        </h5>
        <table class="table table-bordered nowrap w-100">
            <thead><tr>
                <th>{{ ucfirst(__('ui.table.rank')) }} ({{__('ui.table.bashGes') }})</th>
                <th>{{ ucfirst(__('ui.table.points')) }} ({{__('ui.table.bashGes') }})</th>
                <th>{{ ucfirst(__('ui.table.bashPointsRatio')) }}</th>
                <th>{{ ucfirst(__('ui.table.rank')) }} ({{__('ui.table.bashOff') }})</th>
                <th>{{ ucfirst(__('ui.table.points')) }} ({{__('ui.table.bashOff') }})</th>
                <th>{{ ucfirst(__('ui.table.rank')) }} ({{__('ui.table.bashDeff') }})</th>
                <th>{{ ucfirst(__('ui.table.points')) }} ({{__('ui.table.bashDeff') }})</th>
            </tr></thead>
            <tbody><tr>
                <th>
                    {{ \App\Util\BasicFunctions::numberConv($data->gesBashRank_top) }}
                    <br><span class="small float-right">{{ $data->getDate("gesBashRank") }}</span>
                </th>
                <td>
                    {{ \App\Util\BasicFunctions::numberConv($data->gesBash_top) }}
                    <br><span class="small float-right">{{ $data->getDate("gesBash") }}</span>
                </td>
                <td>{{ ($data->points != 0)?(\App\Util\BasicFunctions::numberConv(($data->gesBash_top/$data->points_top)*100)):('-') }}%</td>
                <th>
                    {{ \App\Util\BasicFunctions::numberConv($data->offBashRank_top) }}
                    <br><span class="small float-right">{{ $data->getDate("offBashRank") }}</span>
                </th>
                <td>
                    {{ \App\Util\BasicFunctions::numberConv($data->offBash_top) }}
                    <br><span class="small float-right">{{ $data->getDate("offBash") }}</span>
                </td>
                <th>
                    {{ \App\Util\BasicFunctions::numberConv($data->defBashRank_top) }}
                    <br><span class="small float-right">{{ $data->getDate("defBashRank") }}</span>
                </th>
                <td>
                    {{ \App\Util\BasicFunctions::numberConv($data->defBash_top) }}
                    <br><span class="small float-right">{{ $data->getDate("defBash") }}</span>
                </td>
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
            <tr><th>{{ ucfirst(__('ui.table.tag')) }}</th><td>{{ \App\Util\BasicFunctions::decodeName($data->tag) }}</td></tr>
            <tr><th>{{ ucfirst(__('ui.table.points')) }}</th><td>
                {{ \App\Util\BasicFunctions::numberConv($data->points_top) }} {{ $data->getDate("points") }}
            </td></tr>
            <tr><th>{{ ucfirst(__('ui.table.villages')) }}</th><td>
                {{ \App\Util\BasicFunctions::numberConv($data->village_count_top) }} {{ $data->getDate("village_count") }}
            </td></tr>
            <tr><th>{{ ucfirst(__('ui.table.members')) }}</th><td>
                {{ \App\Util\BasicFunctions::numberConv($data->member_count_top) }} {{ $data->getDate("member_count") }}
            </td></tr>
            <tr><th>{{ ucfirst(__('ui.table.avgPlayer')) }}</th><td>{{ ($data->member_count_top != 0)?\App\Util\BasicFunctions::numberConv( $data->points_top/$data->member_count_top): '-' }}</td></tr>
            <tr><th>{{ ucfirst(__('ui.table.avgVillage')) }}</th><td>{{ ($data->village_count_top != 0)?\App\Util\BasicFunctions::numberConv($data->points_top/$data->village_count_top): '-' }}</td></tr>
            <tr><th>{{ ucfirst(__('ui.table.conquer')) }}</th><td>{!! \App\Util\BasicFunctions::linkWinLoose($worldData, $data->allyID, $conquer, 'allyConquer', tooltipSpace: 'ui.conquer.highlight') !!}</td></tr>
            <tr><th>{{ ucfirst(__('ui.table.allyChanges')) }}</th><td>{!! \App\Util\BasicFunctions::linkWinLoose($worldData, $data->allyID, $allyChanges, 'allyAllyChanges') !!}</td></tr>
            </tbody>
        </table>
        <h5 class="card-subtitle">
            {{__('ui.tabletitel.bashStats')}}
            <span class="float-right">
                <a class="h6" href="{{ route('allyBashRanking', [$worldData->server->code, $worldData->name,  $data->allyID]) }}">{{ __('ui.tabeltitel.allyBashRanking') }}</a>
            </span>
        </h5>
        <table class="table table-striped nowrap w-100 tbl-stretched">
            <tbody>
            <tr><th>{{ ucfirst(__('ui.table.rank')) }} ({{__('ui.table.bashGes') }})</th><td>
                {{ \App\Util\BasicFunctions::numberConv($data->gesBashRank_top) }} {{ $data->getDate("gesBashRank") }}
            </td></tr>
            <tr><th>{{ ucfirst(__('ui.table.points')) }} ({{__('ui.table.bashGes') }})</th><td>
                {{ \App\Util\BasicFunctions::numberConv($data->gesBash_top) }} {{ $data->getDate("gesBash") }}
            </td></tr>
            <tr><th>{{ ucfirst(__('ui.table.bashPointsRatio')) }}</th><td>{{ ($data->points != 0)?\App\Util\BasicFunctions::numberConv(( $data->gesBash/$data->points)*100): '-' }}</td></tr>
            <tr><th>{{ ucfirst(__('ui.table.rank')) }} ({{__('ui.table.bashOff') }})</th><td>
                {{ \App\Util\BasicFunctions::numberConv($data->offBashRank_top) }} {{ $data->getDate("offBashRank") }}
            </td></tr>
            <tr><th>{{ ucfirst(__('ui.table.points')) }} ({{__('ui.table.bashOff') }})</th><td>
                {{ \App\Util\BasicFunctions::numberConv($data->offBash_top) }} {{ $data->getDate("offBash") }}
            </td></tr>
            <tr><th>{{ ucfirst(__('ui.table.rank')) }} ({{__('ui.table.bashDeff') }})</th><td>
                {{ \App\Util\BasicFunctions::numberConv($data->defBashRank_top) }} {{ $data->getDate("defBashRank") }}
            </td></tr>
            <tr><th>{{ ucfirst(__('ui.table.points')) }} ({{__('ui.table.bashDeff') }})</th><td>
                {{ \App\Util\BasicFunctions::numberConv($data->defBash_top) }} {{ $data->getDate("defBash") }}
            </td></tr>
            </tbody>
        </table>
    </div>
</div>
