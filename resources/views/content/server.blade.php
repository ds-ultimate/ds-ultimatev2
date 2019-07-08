@extends('layouts.temp')

@section('titel', __('Weltenübersicht'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="col-md-5 p-lg-5 mx-auto my-1 text-center">
                <h1 class="font-weight-normal">{{ ucfirst(__('Weltenübersicht')) }}</h1>
            </div>
        </div>
        <!-- Normale Welten -->
        @if(count($worldsArray->get('world')) > 0)
        <div class="col-12 col-md-6 mt-2">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">{{ __('Normale Welten') }}:</h2>
                    <table class="table table-hover table-striped no-wrap">
                        <thead>
                        <tr>
                            <th>{{ ucfirst(__('Welt')) }}</th>
                            <th>{{ ucfirst(__('Spieler')) }}</th>
                            <th>{{ ucfirst(__('Stämme')) }}</th>
                            <th>{{ ucfirst(__('Dörfer')) }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($worldsArray->get('world') as $world)
                            <tr>
                                <td><span class="flag-icon flag-icon-{{ $world->server->flag }}"></span> {!! \App\Util\BasicFunctions::linkWorld($world, $world->displayName()) !!} <small class="text-muted">({{ $world->server->code.$world->name }})</small></td>
                                <td>{!! \App\Util\BasicFunctions::linkWorldPlayer($world, \App\Util\BasicFunctions::numberConv($world->player_count)) !!}</td>
                                <td>{!! \App\Util\BasicFunctions::linkWorldAlly($world, \App\Util\BasicFunctions::numberConv($world->ally_count)) !!}</td>
                                <td>{{ \App\Util\BasicFunctions::numberConv($world->village_count) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
        <!-- ENDE Normale Welten -->
        <!-- Spezial Welten -->
        @if (count($worldsArray->get('casual')) > 0 || count($worldsArray->get('speed')) > 0 || count($worldsArray->get('classic')) > 0)
        <div class="col-12 col-md-6 mt-2">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">{{ __('Spezial Welten') }}:</h2>
                    <table class="table table-hover table-striped no-wrap">
                        <thead>
                        <tr>
                            <th>{{ ucfirst(__('Welt')) }}</th>
                            <th>{{ ucfirst(__('Spieler')) }}</th>
                            <th>{{ ucfirst(__('Stämme')) }}</th>
                            <th>{{ ucfirst(__('Dörfer')) }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($worldsArray->get('speed')) > 0)
                            @foreach($worldsArray->get('speed') as $world)
                                <tr>
                                    <td><span class="flag-icon flag-icon-{{ $world->server->flag }}"></span> {!! \App\Util\BasicFunctions::linkWorld($world, $world->displayName()) !!} <small class="text-muted">({{ $world->server->code.$world->name }})</small></td>
                                    <td>{!! \App\Util\BasicFunctions::linkWorldPlayer($world, \App\Util\BasicFunctions::numberConv($world->player_count)) !!}</td>
                                    <td>{!! \App\Util\BasicFunctions::linkWorldAlly($world, \App\Util\BasicFunctions::numberConv($world->ally_count)) !!}</td>
                                    <td>{{ \App\Util\BasicFunctions::numberConv($world->village_count) }}</td>
                                </tr>
                            @endforeach
                        @endif
                        @if(count($worldsArray->get('casual')) > 0)
                            @foreach($worldsArray->get('casual') as $world)
                                <tr>
                                    <td><span class="flag-icon flag-icon-{{ $world->server->flag }}"></span> {!! \App\Util\BasicFunctions::linkWorld($world, $world->displayName()) !!} <small class="text-muted">({{ $world->server->code.$world->name }})</small></td>
                                    <td>{!! \App\Util\BasicFunctions::linkWorldPlayer($world, \App\Util\BasicFunctions::numberConv($world->player_count)) !!}</td>
                                    <td>{!! \App\Util\BasicFunctions::linkWorldAlly($world, \App\Util\BasicFunctions::numberConv($world->ally_count)) !!}</td>
                                    <td>{{ \App\Util\BasicFunctions::numberConv($world->village_count) }}</td>
                                </tr>
                            @endforeach
                        @endif
                        @if(count($worldsArray->get('classic')) > 0)
                            @foreach($worldsArray->get('classic') as $world)
                                <tr>
                                    <td><span class="flag-icon flag-icon-{{ $world->server->flag }}"></span> {!! \App\Util\BasicFunctions::linkWorld($world, $world->displayName()) !!} <small class="text-muted">({{ $world->server->code.$world->name }})</small></td>
                                    <td>{!! \App\Util\BasicFunctions::linkWorldPlayer($world, \App\Util\BasicFunctions::numberConv($world->player_count)) !!}</td>
                                    <td>{!! \App\Util\BasicFunctions::linkWorldAlly($world, \App\Util\BasicFunctions::numberConv($world->ally_count)) !!}</td>
                                    <td>{{ \App\Util\BasicFunctions::numberConv($world->village_count) }}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
        <!-- ENDE Spezial Welten -->
    </div>
@endsection
