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
                        <x-record.stat_elm_village :data='$villageData' :worldData='$worldData' :conquer='$conquer'/>
                    </div>
                    <div class="tab-pane fade" id="map" role="tabpanel" aria-labelledby="map-tab">
                    </div>
                </div>
            </div>
        </div>
        <!-- ENDE Informationen -->
        <!-- Besiegte Punkte Chart -->
        <div class="col-12 mt-3">
            <div class="card" style="height: 320px">
                <div class="card-body">
                    <h4 class="card-title">{{ __('chart.titel.points') }}:</h4>
                    <div id="chart-points"></div>
                </div>
            </div>
        </div>
        <!-- ENDE Besiegte Punkte Chart -->
        <!-- Dorfausbau Tabelle -->
        <div class="col-12 mt-3">
            <div class="card table-responsive">
                <div class="card-body">
                    <h4 class="card-title">{{ucfirst(__('ui.tabletitel.history'))}}</h4>
                    <table id="datahist" class="table table-bordered nowrap w-100">
                        <thead>
                        <tr>
                            <th>{{ ucfirst(__('ui.table.date')) }}</th>
                            <th>{{ ucfirst(__('ui.table.points')) }}</th>
                            <th>{{ ucfirst(__('ui.table.time')) }}</th>
                            <th>{{ ucfirst(__('ui.table.possibleChanges')) }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($villageHistory as $vilHist)
                            <tr>
                                <td>{{ $vilHist["date"] }}</td>
                                <td>
                                    {{ $vilHist["points"] }}
                                    @if($vilHist["pointChange"] > 0)
                                        (<span class="text-success"> +{{ $vilHist["pointChange"] }} </span>)
                                    @else
                                        (<span class="text-danger"> {{ $vilHist["pointChange"] }} </span>)
                                    @endif
                                </td>
                                <td>{{ $vilHist["time"] }}</td>
                                <td>
                                    @if($vilHist["possibleChanges"] == null)
                                        {{ __('ui.village.histUnknown') }}
                                    @else
                                        @if(count($vilHist["possibleChanges"]) <= 1)
                                            <img src='{{ \app\Util\BuildingUtils::getImage($vilHist["possibleChanges"][0][0], \app\Util\BuildingUtils::BUILDING_SIZE_SMALL, $vilHist["possibleChanges"][0][1]) }}'>
                                            {{ __('ui.buildings.' . $vilHist["possibleChanges"][0][0]) }} ({{ $vilHist["possibleChanges"][0][1] }})
                                        @else
                                            <a href="#" data-toggle="bs-tooltip" data-placement="right" data-html="true" title="
                                                @foreach($vilHist["possibleChanges"] as $data)
                                                    <img src='{{ \app\Util\BuildingUtils::getImage($data[0], \app\Util\BuildingUtils::BUILDING_SIZE_SMALL, $data[1]) }}'>
                                                    {{ __('ui.buildings.' . $data[0]) }} ({{ $data[1] }})<br>
                                                @endforeach
                                                    ">
                                                {{ count($vilHist["possibleChanges"]) }} {{ __('ui.village.histPossibilities') }}
                                            </a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- ENDE Dorfausbau Tabelle -->
    </div>
@endsection

@push('js')
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

        $(document).ready( function () {
            $('#datahist').DataTable({
                columnDefs: [
                    {"targets": 0, "className": 'text-right'},
                    {"targets": 1, "className": 'text-center'},
                    {"targets": 2, "className": 'text-center'},
                    {"targets": 3, "className": 'text-center', "orderable": false},
                ],
                dom: 't',
                ordering: true,
                order: [[ 0, "desc" ]],
                paging: false,
                keys: true, //enable KeyTable extension
            });
            
            $('[data-toggle="bs-tooltip"]').tlp();
            $('#datahist a[data-toggle="bs-tooltip"]').on('click', (e) => {
                e.preventDefault();
            });
        } );
    </script>
    {!! $chartJS !!}
@endpush
