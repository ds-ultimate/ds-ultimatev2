@extends('layouts.temp')

@section('titel', __('Suche'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5 p-lg-5 mx-auto my-1 text-center">
            <h1 class="font-weight-normal">{{ __('Suche') }}: {!! ucfirst(($type == 'player')? __('Spieler'): (($type == 'ally')? __('Stämme'): __('Dörfer'))) !!}</h1>
        </div>
        <div class="col-10">
            @if ($type == 'player')
            <table id="table_id" class="table table-hover table-sm w-100">
                <thead><tr>
                    <th>{{ ucfirst(__('Welt')) }}</th>
                    <th>{{ ucfirst(__('Name')) }}</th>
                    <th>{{ ucfirst(__('Punkte')) }}</th>
                    <th>{{ ucfirst(__('Dörfer')) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($result as $player)
                <tr>
                    <th>{{$player->get('world')->displayName()}}</th>
                    <td>{!! \App\Util\BasicFunctions::linkPlayer($player->get('world'),$player->get('player')->playerID,\App\Util\BasicFunctions::outputName($player->get('player')->name)) !!}</td>
                    <td>{{$player->get('player')->points}}</td>
                    <td>{{$player->get('player')->village_count}}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @elseif ($type == 'ally')
            <table id="table_id" class="table table-hover table-sm w-100">
                <thead><tr>
                    <th>{{ ucfirst(__('Welt')) }}</th>
                    <th>{{ ucfirst(__('Name')) }}</th>
                    <th>{{ ucfirst(__('Tag')) }}</th>
                    <th>{{ ucfirst(__('Punkte')) }}</th>
                    <th>{{ ucfirst(__('Dörfer')) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($result as $ally)
                    <tr>
                        <th>{{$ally->get('world')->displayName()}}</th>
                        <td>{!! \App\Util\BasicFunctions::linkAlly($ally->get('world'),$ally->get('ally')->allyID,\App\Util\BasicFunctions::outputName($ally->get('ally')->name)) !!}</td>
                        <td>{!! \App\Util\BasicFunctions::linkAlly($ally->get('world'),$ally->get('ally')->allyID,\App\Util\BasicFunctions::outputName($ally->get('ally')->tag)) !!}</td>
                        <td>{{$ally->get('ally')->points}}</td>
                        <td>{{$ally->get('ally')->village_count}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @else
            Dörfer
            {{-- // FIXME: implement village search --}}
            @endif
        </div>
    </div>
@endsection

@section('js')

@endsection
