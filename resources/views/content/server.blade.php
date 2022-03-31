@extends('layouts.app')

@section('titel', __('ui.titel.worldOverview'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="col-md-5 p-lg-5 mx-auto my-1 text-center">
                <h1 class="font-weight-normal">{{ ucfirst(__('ui.titel.worldOverview')) }}</h1>
            </div>
        </div>
        <!-- Normale Welten -->
        <div class="col-12 col-md-6 mt-2">
            <div class="card">
                <div class="card-body">
                    @if(isset($worldsActive['world']) && count($worldsActive['world']) > 0)
                    <h2 class="card-title">{{ __('ui.tabletitel.normalWorlds') }}:</h2>
                    <table class="table table-hover table-striped nowrap w-100">
                        <thead>
                        <tr>
                            <th>{{ ucfirst(__('ui.table.world')) }}</th>
                            <th>{{ ucfirst(__('ui.table.player')) }}</th>
                            <th>{{ ucfirst(__('ui.table.ally')) }}</th>
                            <th>{{ ucfirst(__('ui.table.village')) }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($worldsActive['world'] as $world)
                            <tr>
                                <td><span class="flag-icon flag-icon-{{ $world->server->flag }}"></span> {!! \App\Util\BasicFunctions::linkWorld($world, $world->display_name) !!}
                                    <small class="text-muted">({{ $world->server->code.$world->name }})</small>
                                    @auth
                                        @can('world_access')
                                            {!! \App\Util\BasicFunctions::worldStatus($world->active) !!}
                                        @endcan
                                    @endauth
                                </td>
                                <td>{!! \App\Util\BasicFunctions::linkWorldPlayer($world, \App\Util\BasicFunctions::numberConv($world->player_count)) !!}</td>
                                <td>{!! \App\Util\BasicFunctions::linkWorldAlly($world, \App\Util\BasicFunctions::numberConv($world->ally_count)) !!}</td>
                                <td>{{ \App\Util\BasicFunctions::numberConv($world->village_count) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                    @if (isset($worldsInactive['world']) && count($worldsInactive['world']) > 0)
                        <div class="w-100 text-center my-3">
                            <button class="btn btn-secondary btn-sm" data-toggle="collapse" data-target="#inactive1" aria-expanded="false" aria-controls="inactive1" type="button">
                                {{__('ui.showMoreWorlds')}}</button>
                        </div>
                        <div class="collapse inactive" id="inactive1">
                            <h2 class="card-title">{{ __('ui.tabletitel.normalWorlds').' '.__('ui.archive') }}:</h2>
                            <table class="table table-hover table-striped nowrap w-100">
                                <thead>
                                <tr>
                                    <th>{{ ucfirst(__('ui.table.world')) }}</th>
                                    <th>{{ ucfirst(__('ui.table.player')) }}</th>
                                    <th>{{ ucfirst(__('ui.table.ally')) }}</th>
                                    <th>{{ ucfirst(__('ui.table.village')) }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($worldsInactive['world'] as $world)
                                    <tr>
                                        <td><span class="flag-icon flag-icon-{{ $world->server->flag }}"></span> {!! \App\Util\BasicFunctions::linkWorld($world, $world->display_name) !!}
                                            <small class="text-muted">({{ $world->server->code.$world->name }})</small>
                                            @auth
                                                @can('world_access')
                                                    {!! \App\Util\BasicFunctions::worldStatus($world->active) !!}
                                                @endcan
                                            @endauth
                                        </td>
                                        <td>{!! \App\Util\BasicFunctions::linkWorldPlayer($world, \App\Util\BasicFunctions::numberConv($world->player_count)) !!}</td>
                                        <td>{!! \App\Util\BasicFunctions::linkWorldAlly($world, \App\Util\BasicFunctions::numberConv($world->ally_count)) !!}</td>
                                        <td>{{ \App\Util\BasicFunctions::numberConv($world->village_count) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                </div>
            </div>
        </div>
        <!-- ENDE Normale Welten -->
        <!-- Spezial Welten -->
        <div class="col-12 col-md-6 mt-2">
            <div class="card">
                <div class="card-body">
                    @if ((isset($worldsActive['casual']) && count($worldsActive['casual']) > 0) || (isset($worldsActive['speed']) && count($worldsActive['speed']) > 0) || (isset($worldsActive['classic']) && count($worldsActive['classic']) > 0))
                    <h2 class="card-title">{{ __('ui.tabletitel.specialWorlds') }}:</h2>
                    <table class="table table-hover table-striped nowrap w-100">
                        <thead>
                        <tr>
                            <th>{{ ucfirst(__('ui.table.world')) }}</th>
                            <th>{{ ucfirst(__('ui.table.player')) }}</th>
                            <th>{{ ucfirst(__('ui.table.ally')) }}</th>
                            <th>{{ ucfirst(__('ui.table.village')) }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($worldsActive['casual']) && count($worldsActive['casual']) > 0)
                            @foreach($worldsActive['casual'] as $world)
                                <tr>
                                    <td><span class="flag-icon flag-icon-{{ $world->server->flag }}"></span> {!! \App\Util\BasicFunctions::linkWorld($world, $world->display_name) !!}
                                        <small class="text-muted">({{ $world->server->code.$world->name }})</small>
                                        @auth
                                            @can('world_access')
                                                {!! \App\Util\BasicFunctions::worldStatus($world->active) !!}
                                            @endcan
                                        @endauth
                                    </td>
                                    <td>{!! \App\Util\BasicFunctions::linkWorldPlayer($world, \App\Util\BasicFunctions::numberConv($world->player_count)) !!}</td>
                                    <td>{!! \App\Util\BasicFunctions::linkWorldAlly($world, \App\Util\BasicFunctions::numberConv($world->ally_count)) !!}</td>
                                    <td>{{ \App\Util\BasicFunctions::numberConv($world->village_count) }}</td>
                                </tr>
                            @endforeach
                        @endif
                        @if(isset($worldsActive['classic']) && count($worldsActive['classic']) > 0)
                            @foreach($worldsActive['classic'] as $world)
                                <tr>
                                    <td><span class="flag-icon flag-icon-{{ $world->server->flag }}"></span> {!! \App\Util\BasicFunctions::linkWorld($world, $world->display_name) !!}
                                        <small class="text-muted">({{ $world->server->code.$world->name }})</small>
                                        @auth
                                            @can('world_access')
                                                {!! \App\Util\BasicFunctions::worldStatus($world->active) !!}
                                            @endcan
                                        @endauth
                                    </td>
                                    <td>{!! \App\Util\BasicFunctions::linkWorldPlayer($world, \App\Util\BasicFunctions::numberConv($world->player_count)) !!}</td>
                                    <td>{!! \App\Util\BasicFunctions::linkWorldAlly($world, \App\Util\BasicFunctions::numberConv($world->ally_count)) !!}</td>
                                    <td>{{ \App\Util\BasicFunctions::numberConv($world->village_count) }}</td>
                                </tr>
                            @endforeach
                        @endif
                        @if(isset($worldsActive['speed']) && count($worldsActive['speed']) > 0)
                            @foreach($worldsActive['speed'] as $world)
                                <tr>
                                    <td><span class="flag-icon flag-icon-{{ $world->server->flag }}"></span> {!! \App\Util\BasicFunctions::linkWorld($world, $world->display_name) !!}
                                        <small class="text-muted">({{ $world->server->code.$world->name }})</small>
                                        @auth
                                            @can('world_access')
                                                {!! \App\Util\BasicFunctions::worldStatus($world->active) !!}
                                            @endcan
                                        @endauth
                                    </td>
                                    <td>{!! \App\Util\BasicFunctions::linkWorldPlayer($world, \App\Util\BasicFunctions::numberConv($world->player_count)) !!}</td>
                                    <td>{!! \App\Util\BasicFunctions::linkWorldAlly($world, \App\Util\BasicFunctions::numberConv($world->ally_count)) !!}</td>
                                    <td>{{ \App\Util\BasicFunctions::numberConv($world->village_count) }}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                    @endif
                    @if ((isset($worldsInactive['casual']) && count($worldsInactive['casual']) > 0) || (isset($worldsInactive['speed']) && count($worldsInactive['speed']) > 0) || (isset($worldsInactive['classic']) && count($worldsInactive['classic']) > 0))
                        <div class="w-100 text-center my-3">
                            <button class="btn btn-secondary btn-sm" data-toggle="collapse" data-target="#inactive2" aria-expanded="false" aria-controls="inactive2" type="button">
                                {{__('ui.showMoreWorlds')}}</button>
                        </div>
                        <div class="collapse inactive" id="inactive2">
                            <h2 class="card-title">{{ __('ui.tabletitel.specialWorlds').' '.__('ui.archive') }}:</h2>
                            <table class="table table-hover table-striped nowrap w-100">
                                <thead>
                                <tr>
                                    <th>{{ ucfirst(__('ui.table.world')) }}</th>
                                    <th>{{ ucfirst(__('ui.table.player')) }}</th>
                                    <th>{{ ucfirst(__('ui.table.ally')) }}</th>
                                    <th>{{ ucfirst(__('ui.table.village')) }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($worldsInactive['casual']) && count($worldsInactive['casual']) > 0)
                                    @foreach($worldsInactive['casual'] as $world)
                                        <tr>
                                            <td><span class="flag-icon flag-icon-{{ $world->server->flag }}"></span> {!! \App\Util\BasicFunctions::linkWorld($world, $world->display_name) !!}
                                                <small class="text-muted">({{ $world->server->code.$world->name }})</small>
                                                @auth
                                                    @can('world_access')
                                                        {!! \App\Util\BasicFunctions::worldStatus($world->active) !!}
                                                    @endcan
                                                @endauth
                                            </td>
                                            <td>{!! \App\Util\BasicFunctions::linkWorldPlayer($world, \App\Util\BasicFunctions::numberConv($world->player_count)) !!}</td>
                                            <td>{!! \App\Util\BasicFunctions::linkWorldAlly($world, \App\Util\BasicFunctions::numberConv($world->ally_count)) !!}</td>
                                            <td>{{ \App\Util\BasicFunctions::numberConv($world->village_count) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                @if(isset($worldsInactive['classic']) && count($worldsInactive['classic']) > 0)
                                    @foreach($worldsInactive['classic'] as $world)
                                        <tr>
                                            <td><span class="flag-icon flag-icon-{{ $world->server->flag }}"></span> {!! \App\Util\BasicFunctions::linkWorld($world, $world->display_name) !!}
                                                <small class="text-muted">({{ $world->server->code.$world->name }})</small>
                                                @auth
                                                    @can('world_access')
                                                        {!! \App\Util\BasicFunctions::worldStatus($world->active) !!}
                                                    @endcan
                                                @endauth
                                            </td>
                                            <td>{!! \App\Util\BasicFunctions::linkWorldPlayer($world, \App\Util\BasicFunctions::numberConv($world->player_count)) !!}</td>
                                            <td>{!! \App\Util\BasicFunctions::linkWorldAlly($world, \App\Util\BasicFunctions::numberConv($world->ally_count)) !!}</td>
                                            <td>{{ \App\Util\BasicFunctions::numberConv($world->village_count) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                @if(isset($worldsInactive['speed']) && count($worldsInactive['speed']) > 0)
                                    @foreach($worldsInactive['speed'] as $world)
                                        <tr>
                                            <td><span class="flag-icon flag-icon-{{ $world->server->flag }}"></span> {!! \App\Util\BasicFunctions::linkWorld($world, $world->display_name) !!}
                                                <small class="text-muted">({{ $world->server->code.$world->name }})</small>
                                                @auth
                                                    @can('world_access')
                                                        {!! \App\Util\BasicFunctions::worldStatus($world->active) !!}
                                                    @endcan
                                                @endauth
                                            </td>
                                            <td>{!! \App\Util\BasicFunctions::linkWorldPlayer($world, \App\Util\BasicFunctions::numberConv($world->player_count)) !!}</td>
                                            <td>{!! \App\Util\BasicFunctions::linkWorldAlly($world, \App\Util\BasicFunctions::numberConv($world->ally_count)) !!}</td>
                                            <td>{{ \App\Util\BasicFunctions::numberConv($world->village_count) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- ENDE Spezial Welten -->
    </div>
@endsection

@push('js')
<script>
    $('.inactive').on('show.bs.collapse', function (e) {
        $('button[aria-controls=' + $(e.currentTarget).attr('id') + ']').html('{{__('ui.showLessWorlds')}}')
    })
    $('.inactive').on('hide.bs.collapse', function (e) {
        $('button[aria-controls=' + $(e.currentTarget).attr('id') + ']').html('{{__('ui.showMoreWorlds')}}')
    })
</script>
@endpush
