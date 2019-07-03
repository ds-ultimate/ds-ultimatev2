@extends('layouts.temp')

@section('titel', __('Übersicht Welten'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5 p-lg-5 mx-auto my-1 text-center">
            <h1 class="font-weight-normal">{{ __('Übersicht Welten') }}</h1>
        </div>
        <div class="col-10">
            <div class="row">
            {{-- FIXME much redundant code --}}
            @if(count($worldsArray->get('world')) > 0)
                <div class="col">
                    <h2>{{ __('Normale Welten') }}:</h2>
                    <table class="table table-hover no-wrap">
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
                                <td><span class="flag-icon flag-icon-de"></span> {!! \App\Util\BasicFunctions::linkWorld($world, $world->get('display_name')) !!}</td>
                                <td>{!! \App\Util\BasicFunctions::linkWorldPlayer($world, \App\Util\BasicFunctions::numberConv($world->get('player_count'))) !!}</td>
                                <td>{!! \App\Util\BasicFunctions::linkWorldAlly($world, \App\Util\BasicFunctions::numberConv($world->get('ally_count'))) !!}</td>
                                <td>{{ \App\Util\BasicFunctions::numberConv($world->get('village_count')) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            @if (count($worldsArray->get('casual')) > 0 || count($worldsArray->get('speed')) > 0)
                <div class="col">
                    <h2>{{ __('Spezial Welten') }}:</h2>
                    <table class="table table-hover no-wrap">
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
                                    <td>{!! \App\Util\BasicFunctions::linkWorld($world, $world->get('display_name')) !!}</td>
                                    <td>{!! \App\Util\BasicFunctions::linkWorldPlayer($world, \App\Util\BasicFunctions::numberConv($world->get('player_count'))) !!}</td>
                                    <td>{!! \App\Util\BasicFunctions::linkWorldAlly($world, \App\Util\BasicFunctions::numberConv($world->get('ally_count'))) !!}</td>
                                    <td>{{ \App\Util\BasicFunctions::numberConv($world->get('village_count')) }}</td>
                                </tr>
                            @endforeach
                        @endif
                        @if(count($worldsArray->get('casual')) > 0)
                            @foreach($worldsArray->get('casual') as $world)
                                <tr>
                                    <td>{!! \App\Util\BasicFunctions::linkWorld($world, $world->get('display_name')) !!}</td>
                                    <td>{!! \App\Util\BasicFunctions::linkWorldPlayer($world, \App\Util\BasicFunctions::numberConv($world->get('player_count'))) !!}</td>
                                    <td>{!! \App\Util\BasicFunctions::linkWorldAlly($world, \App\Util\BasicFunctions::numberConv($world->get('ally_count'))) !!}</td>
                                    <td>{{ \App\Util\BasicFunctions::numberConv($world->get('village_count')) }}</td>
                                </tr>
                            @endforeach
                        @endif
                        @if(count($worldsArray->get('classic')) > 0)
                            @foreach($worldsArray->get('classic') as $world)
                                <tr>
                                    <td>{!! \App\Util\BasicFunctions::linkWorld($world, $world->get('display_name')) !!}</td>
                                    <td>{!! \App\Util\BasicFunctions::linkWorldPlayer($world, \App\Util\BasicFunctions::numberConv($world->get('player_count'))) !!}</td>
                                    <td>{!! \App\Util\BasicFunctions::linkWorldAlly($world, \App\Util\BasicFunctions::numberConv($world->get('ally_count'))) !!}</td>
                                    <td>{{ \App\Util\BasicFunctions::numberConv($world->get('village_count')) }}</td>
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
@endsection
