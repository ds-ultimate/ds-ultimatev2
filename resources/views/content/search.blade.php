@extends('layouts.temp')

@section('content')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4-4.1.1/dt-1.10.18/datatables.min.css"/>
    <link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css" rel="stylesheet">
    <div class="row justify-content-center">
        <div class="col-10">
            <div class="col-12">
                <ul id = "lang_menu">
                    <li class = "language{{ App::isLocale('de') ? ' active' : '' }}"><a href="{{ route('locale', 'de') }}">Deutsch</a></li>
                    <li class = "language{{ App::isLocale('en') ? ' active' : '' }}"><a href="{{ route('locale', 'en') }}">English</a></li>
                </ul>
            </div>
            <h2>{{__('Spieler')}}</h2>
            <table id="table_id" class="table table-hover table-sm w-100">
                <thead><tr>
                    <th>{{ ucfirst(__('Welt')) }}</th>
                    <th>{{ ucfirst(__('Name')) }}</th>
                    <th>{{ ucfirst(__('Punkte')) }}</th>
                    <th>{{ ucfirst(__('Dörfer')) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($players as $player)
                <tr>
                    <th>{{$player->get('world')->name}}</th>
                    <th>{!! \App\Util\BasicFunctions::outputName($player->get('player')->name) !!}</th>
                    <th>{{$player->get('player')->points}}</th>
                    <th>{{$player->get('player')->village_count}}</th>
                </tr>
                @endforeach
                </tbody>
            </table>
            <h2>{{__('Stamm')}}</h2>
            <table id="table_id" class="table table-hover table-sm w-100">
                <thead><tr>
                    <th>{{ ucfirst(__('Welt')) }}</th>
                    <th>{{ ucfirst(__('Stamm')) }}</th>
                    <th>{{ ucfirst(__('Punkte')) }}</th>
                    <th>{{ ucfirst(__('Dörfer')) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($allys as $ally)
                    <tr>
                        <th>{{$ally->get('world')->name}}</th>
                        <th>{!! \App\Util\BasicFunctions::outputName($ally->get('ally')->name) !!}</th>
                        <th>{{$ally->get('ally')->points}}</th>
                        <th>{{$ally->get('ally')->village_count}}</th>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
