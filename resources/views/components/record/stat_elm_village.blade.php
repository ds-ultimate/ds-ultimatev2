@props(['data', 'worldData', 'conquer'])

<div class="row">
    <div class="col-12 col-md-6">
        <h4 class="card-title">{{ucfirst(__('ui.tabletitel.info'))}}</h4>
    </div>
    <div class="col-12 col-md-6">
        <span class="float-right">
            <a href="{{ $data->linkIngame($worldData, false) }}" target="_blank" class="btn btn-primary btn-sm">{{ __('ui.ingame.normal') }}</a>
            <a href="{{ $data->linkIngame($worldData, true) }}" target="_blank" class="btn btn-primary btn-sm">{{ __('ui.ingame.guest') }}</a>
        </span>
    </div>
    <div class="col-12 d-none d-lg-block mt-3">
        <h5 class="card-subtitle">{{__('ui.tabletitel.general')}}</h5>
        <table class="table table-bordered nowrap w-100">
            <thead><tr>
                <th>{{ ucfirst(__('ui.table.name')) }}</th>
                <th>{{ ucfirst(__('ui.table.points')) }}</th>
                <th>{{ ucfirst(__('ui.table.continent')) }}</th>
                <th>{{ ucfirst(__('ui.table.coordinates')) }}</th>
                <th>{{ ucfirst(__('ui.table.owner')) }}</th>
                <th>{{ ucfirst(__('ui.table.conquer')) }}</th>
                <th>{{ ucfirst(__('ui.table.bonusType')) }}</th>
                <th></th>
            </tr></thead>
            <tbody><tr>
                <td>{{ \App\Util\BasicFunctions::decodeName( $data->name) }}</td>
                <td>{{ \App\Util\BasicFunctions::numberConv( $data->points) }}</td>
                <td>{{ $data->continentString() }}</td>
                <td>{{ $data->coordinates() }}</td>
                <td>{!! ($data->owner != 0)?(($data->playerLatest == null)? ucfirst(__('ui.player.deleted')) : \App\Util\BasicFunctions::linkPlayer($worldData, $data->owner, \App\Util\BasicFunctions::outputName($data->playerLatest->name))) : ucfirst(__('ui.player.barbarian')) !!}</td>
                <td>{!! \App\Util\BasicFunctions::linkWinLoose($worldData,  $data->villageID, $conquer, 'villageConquer', tooltipSpace: 'ui.conquer.highlight') !!}</td>
                <td>{{ $data->bonusText() }}</td>
                <td><img src="{!! asset('images/'.$data->getVillageSkinImage('default')) !!}"></td>
            </tr></tbody>
        </table>
    </div>
    <div class="col-12 d-lg-none mt-3">
        <h5 class="card-subtitle">{{__('ui.tabletitel.general')}}</h5>
        <table class="table table-striped nowrap w-100 tbl-stretched">
            <tbody>
            <tr><th>{{ ucfirst(__('ui.table.name')) }}</th><td>{{ \App\Util\BasicFunctions::decodeName( $data->name) }}</td></tr>
            <tr><th>{{ ucfirst(__('ui.table.points')) }}</th><td>{{ \App\Util\BasicFunctions::numberConv( $data->points) }}</td></tr>
            <tr><th>{{ ucfirst(__('ui.table.continent')) }}</th><td>{{ $data->continentString() }}</td></tr>
            <tr><th>{{ ucfirst(__('ui.table.coordinates')) }}</th><td>{{ $data->coordinates() }}</td></tr>
            <tr><th>{{ ucfirst(__('ui.table.owner')) }}</th><td>{!! ($data->owner != 0)?(($data->playerLatest == null)? ucfirst(__('ui.player.deleted')) : \App\Util\BasicFunctions::linkPlayer($worldData, $data->owner, \App\Util\BasicFunctions::outputName($data->playerLatest->name))) : ucfirst(__('ui.player.barbarian')) !!}</td></tr>
            <tr><th>{{ ucfirst(__('ui.table.conquer')) }}</th><td>{!! \App\Util\BasicFunctions::linkWinLoose($worldData,  $data->villageID, $conquer, 'villageConquer', tooltipSpace: 'ui.conquer.highlight') !!}</td></tr>
            <tr><th>{{ ucfirst(__('ui.table.bonusType')) }}</th><td>{{ $data->bonusText() }}</td></tr>
            <tr><th></th><td><img src="{!! asset('images/'.$data->getVillageSkinImage('default')) !!}"></td></tr>
            </tbody>
        </table>
    </div>
</div>
