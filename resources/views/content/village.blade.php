@extends('layouts.app')

@section('titel', ucfirst(__('ui.titel.village')).': '.\App\Util\BasicFunctions::decodeName($villageData->name))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('ui.titel.village')).': '.\App\Util\BasicFunctions::decodeName($villageData->name) }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('ui.titel.village')).': ' }}
            </h1>
            <h4>
                {{ \App\Util\BasicFunctions::decodeName($villageData->name) }}
            </h4>
        </div>
        <!-- ENDE Titel für Mobile Geräte -->
        <!-- Informationen -->
        <div class="col-12">
            <div class="card">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="stats-tab" data-toggle="tab" href="#stats" role="tab" aria-controls="stats" aria-selected="true">{{ __('ui.nav.stats') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="map-tab" data-toggle="tab" href="#map" role="tab" aria-controls="map" aria-selected="false">{{ __('ui.nav.map') }}</a>
                    </li>
                </ul>
                <div class="card-body tab-content">
                    <div class="tab-pane fade show active" id="stats" role="tabpanel" aria-labelledby="stats-tab">
                        <h4 class="card-title">{{ucfirst(__('ui.tabletitel.info'))}}
                            <span class="float-right">
                                <a href="{{ $villageData->linkIngame($worldData, false) }}" target="_blank" class="btn btn-primary btn-sm">{{ __('ui.ingame.normal') }}</a>
                                <a href="{{ $villageData->linkIngame($worldData, true) }}" target="_blank" class="btn btn-primary btn-sm">{{ __('ui.ingame.guest') }}</a>
                            </span>
                        </h4>
                        <table id="data1" class="table table-bordered no-wrap w-100">
                            <thead>
                            <tr>
                                <th class="all">{{ ucfirst(__('ui.table.name')) }}</th>
                                <th class="all">{{ ucfirst(__('ui.table.points')) }}</th>
                                <th class="desktop">{{ ucfirst(__('ui.table.continent')) }}</th>
                                <th class="desktop">{{ ucfirst(__('ui.table.coordinates')) }}</th>
                                <th class="desktop">{{ ucfirst(__('ui.table.owner')) }}</th>
                                <th class="desktop">{{ ucfirst(__('ui.table.conquer')) }}</th>
                                <th class="desktop">{{ ucfirst(__('ui.table.bonusType')) }}</th>
                                <th class="desktop"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>{{ \App\Util\BasicFunctions::decodeName($villageData->name) }}</td>
                                <td>{{ \App\Util\BasicFunctions::numberConv($villageData->points) }}</td>
                                <th>{{ $villageData->continentString() }}</th>
                                <td>{{ $villageData->coordinates() }}</td>
                                <td>{!! ($villageData->owner != 0)?\App\Util\BasicFunctions::linkPlayer($worldData, $villageData->owner, \App\Util\BasicFunctions::outputName($villageData->playerLatest->name)) : ucfirst(__('ui.player.barbarian')) !!}</td>
                                <td>{!! \App\Util\BasicFunctions::linkWinLoose($worldData, $villageData->villageID, $conquer, 'villageConquer') !!}</td>
                                <th>{{ $villageData->bonusText() }}</th>
                                <td><img src="{!! asset('images/'.$villageData->getVillageSkinImage('default')) !!}"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="map" role="tabpanel" aria-labelledby="map-tab">
                    </div>
                </div>
            </div>
        </div>
        <!-- ENDE Informationen -->
        <!-- Besiegte Punkte Chart -->
        <div class="col-12 mt-2">
            <div class="card" style="height: 320px">
                <div class="card-body">
                    <h4 class="card-title">{{ __('chart.titel.points') }}:</h4>
                    <div id="chart-points"></div>
                </div>
            </div>
        </div>
        <!-- ENDE Besiegte Punkte Chart -->
    </div>
@endsection

@section('js')
    <script>
        $('#map-tab').click(function (e) {
            if($('#map-img').length > 0) return;
            $.ajax({
                type: "GET",
                url:"{{ route('api.map.overview.sized', [$worldData->server->code, $worldData->name, 'v', $villageData->villageID, '500', '500', 'base64']) }}",
                contentType: "image/png",
                success: function(data){
                $('#map').html('<img id="map-img" class="container-fluid p-0" src="' + data + '" />'); },
            });
        });
    </script>
    <script>

        $(document).ready( function () {
            $.extend( $.fn.dataTable.defaults, {
                responsive: true
            } );

            $('#data1').DataTable({
                dom: 't',
                ordering: false,
                paging: false,
                responsive: true,

                keys: true, //enable KeyTable extension
            });
        } );
    </script>
    {!! $chartJS !!}
@endsection
