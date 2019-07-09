@extends('layouts.temp')

@section('titel', ucfirst(__('ui.titel.village')).': '.\App\Util\BasicFunctions::outputName($villageData->name))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('ui.titel.village')).': '.\App\Util\BasicFunctions::outputName($villageData->name) }}</h1>
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
                <div class="card-body">
                    <h4 class="card-title">{{ucfirst(__('ui.tabletitel.info'))}}</h4>
                    <table id="data1" class="table table-bordered no-wrap">
                        <thead>
                        <tr>
                            <th>{{ ucfirst(__('ui.table.name')) }}</th>
                            <th>{{ ucfirst(__('ui.table.points')) }}</th>
                            <th>{{ ucfirst(__('ui.table.continent')) }}</th>
                            <th>{{ ucfirst(__('ui.table.coordinates')) }}</th>
                            <th>{{ ucfirst(__('ui.table.owner')) }}</th>
                            <th>{{ ucfirst(__('ui.table.conquer')) }}</th>
                            <th>{{ ucfirst(__('ui.table.bonusType')) }}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{ \App\Util\BasicFunctions::outputName($villageData->name) }}</td>
                            <td>{{ \App\Util\BasicFunctions::numberConv($villageData->points) }}</td>
                            <th>{{ $villageData->continentString() }}</th>
                            <td>{{ $villageData->coordinates() }}</td>
                            <td>{!! ($villageData->owner != 0)?\App\Util\BasicFunctions::linkPlayer($worldData, $villageData->owner, \App\Util\BasicFunctions::outputName($villageData->playerLatest->name)) : ucfirst(__('Barbaren')) !!}</td>
                            <td>{{ $conquer->get('total') }}</td>
                            <th>{{ $villageData->bonusText() }}</th>
                            <td><img src="{!! asset('images/'.$villageData->getVillageSkinImage('default')) !!}"></td>
                        </tr>
                        </tbody>
                    </table>
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
