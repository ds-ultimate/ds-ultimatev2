@extends('layouts.temp')

@section('content')
    <div class="row justify-content-center">
        <div class="col-10">
            <div class="row">
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
                                <td>{!! \App\Util\BasicFunctions::linkWorld($world, ucfirst(__('Welt')).' '.$world->get('world')) !!}</td>
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
                            @if(count($worldsArray->get('casual')) > 0)
                                @foreach($worldsArray->get('casual') as $world)
                                    <tr>
                                        <td>{!! \App\Util\BasicFunctions::linkWorld($world, ucfirst(__('Casual')).' '.$world->get('world')) !!}</td>
                                        <td>{!! \App\Util\BasicFunctions::linkWorldPlayer($world, \App\Util\BasicFunctions::numberConv($world->get('player_count'))) !!}</td>
                                        <td>{!! \App\Util\BasicFunctions::linkWorldAlly($world, \App\Util\BasicFunctions::numberConv($world->get('ally_count'))) !!}</td>
                                        <td>{{ \App\Util\BasicFunctions::numberConv($world->get('village_count')) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            @if(count($worldsArray->get('speed')) > 0)
                                @foreach($worldsArray->get('speed') as $world)
                                    <tr>
                                        <td>{!! \App\Util\BasicFunctions::linkWorld($world, ucfirst(__('Speed')).' '.$world->get('world')) !!}</td>
                                        <td>{!! \App\Util\BasicFunctions::linkWorldPlayer($world, \App\Util\BasicFunctions::numberConv($world->get('player_count'))) !!}</td>
                                        <td>{!! \App\Util\BasicFunctions::linkWorldAlly($world, \App\Util\BasicFunctions::numberConv($world->get('ally_count'))) !!}</td>
                                        <td>{{ \App\Util\BasicFunctions::numberConv($world->get('village_count')) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            @if(count($worldsArray->get('classic')) > 0)
                                @foreach($worldsArray->get('classic') as $world)
                                    <tr>
                                        <td>{!! \App\Util\BasicFunctions::linkWorld($world, ucfirst(__('Classic')).' '.$world->get('world')) !!}</td>
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
