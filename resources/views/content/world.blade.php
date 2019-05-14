@extends('layouts.temp')

@section('content')
    <div class="row justify-content-center">
        <div class="col-10">
            <div class="col-12">
                <ul id = "lang_menu">
                    <li class = "language{{ App::isLocale('de') ? ' active' : '' }}"><a href="{{ route('locale', 'de') }}">Deutsch</a></li>
                    <li class = "language{{ App::isLocale('en') ? ' active' : '' }}"><a href="{{ route('locale', 'en') }}">English</a></li>
                </ul>
            </div>
            <div class="row">
                <div class="col">
                    <h2>{{ __('Top 10 Spieler') }}:</h2>
                    <table class="table table-striped no-wrap">
                        <thead>
                        <tr>
                            <th>{{ ucfirst(__('Rang')) }}</th>
                            <th>{{ ucfirst(__('Spieler')) }}</th>
                            <th>{{ ucfirst(__('Punkte')) }}</th>
                            <th>{{ ucfirst(__('Dörfer')) }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($playerArray as $player)
                            <tr>
                                <td>{{ $player->rank }}</td>
                                <td>{!! \App\Util\BasicFunctions::linkPlayer($worldData, $player->playerID, \App\Util\BasicFunctions::outputName($player->name)) !!}</td>
                                <td>{{ \App\Util\BasicFunctions::numberConv($player->points) }}</td>
                                <td>{{ \App\Util\BasicFunctions::numberConv($player->village_count) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col">
                    <h2>{{ __('Top 10 Stämme') }}:</h2>
                    <table class="table table-striped no-wrap">
                        <thead>
                        <tr>
                            <th>{{ ucfirst(__('Rang')) }}</th>
                            <th>{{ ucfirst(__('Name')) }}</th>
                            <th>{{ ucfirst(__('Stammes_Tag')) }}</th>
                            <th>{{ ucfirst(__('Punkte')) }}</th>
                            <th>{{ ucfirst(__('Mitglieder')) }}</th>
                            <th>{{ ucfirst(__('Dörfer')) }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($allyArray as $ally)
                            <tr>
                                <td>{{ $ally->rank }}</td>
                                <td>{!! \App\Util\BasicFunctions::linkAlly($worldData, $ally->allyID, \App\Util\BasicFunctions::outputName($ally->name))!!}</td>
                                <td>{{ \App\Util\BasicFunctions::outputName($ally->tag) }}</td>
                                <td>{{ \App\Util\BasicFunctions::numberConv($ally->points) }}</td>
                                <td>{{ \App\Util\BasicFunctions::numberConv($ally->member_count) }}</td>
                                <td>{{ \App\Util\BasicFunctions::numberConv($ally->village_count) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
