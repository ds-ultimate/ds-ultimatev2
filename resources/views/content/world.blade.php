@extends('layouts.temp')

@section('titel', ucfirst(__('Welt')).':'.$worldData->get('name'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5 p-lg-5 mx-auto my-1 text-center">
            <h1 class="font-weight-normal">{{ ucfirst(__('Welt')).' '.$worldData->get('world') }}</h1>
        </div>
        <div class="col-12">
            <div class="row">
                <div class="col-12 col-md-6">
                    <h2>{{ __('Top 10 Spieler') }}:</h2>
                    <table class="table table-striped"  id="t10Player">
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
                                <th>{{ $player->rank }}</th>
                                <td>{!! \App\Util\BasicFunctions::linkPlayer($worldData, $player->playerID, \App\Util\BasicFunctions::outputName($player->name)) !!}</td>
                                <td>{{ \App\Util\BasicFunctions::numberConv($player->points) }}</td>
                                <td>{{ \App\Util\BasicFunctions::numberConv($player->village_count) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-12 col-md-6">
                    <h2>{{ __('Top 10 Stämme') }}:</h2>
                    <table class="table table-striped" id="t10Ally">
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
                                <th>{{ $ally->rank }}</th>
                                <td class="text-truncate">{!! \App\Util\BasicFunctions::linkAlly($worldData, $ally->allyID, \App\Util\BasicFunctions::outputName($ally->name))!!}</td>
                                <td>{{ \App\Util\BasicFunctions::outputName($ally->tag) }}</td>
                                <td class="text-right">{{ \App\Util\BasicFunctions::numberConv($ally->points) }}</td>
                                <td class="text-right">{{ \App\Util\BasicFunctions::numberConv($ally->member_count) }}</td>
                                <td class="text-right">{{ \App\Util\BasicFunctions::numberConv($ally->village_count) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready( function () {
            $('#t10Player').DataTable({
                "responsive": true,
                "searching": false,
                "paging": false,
                "ordering": false,
                "info": false,
                {!! \App\Util\Datatable::language() !!}
            });

            $('#t10Ally').DataTable({
                "responsive": true,
                "searching": false,
                "paging": false,
                "ordering": false,
                "info": false,
                {!! \App\Util\Datatable::language() !!}
            });
        } );
    </script>
@endsection
