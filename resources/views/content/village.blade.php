@extends('layouts.temp')

@section('titel', ucfirst(__('Dorf')).': '.\App\Util\BasicFunctions::outputName($villageData->name))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('Dorf')).': '.\App\Util\BasicFunctions::outputName($villageData->name) }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('Dorf')).': ' }}
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
                    <h4 class="card-title">{{ucfirst(__('Informationen'))}}</h4>
                    <table id="data1" class="table table-bordered no-wrap">
                        <thead>
                        <tr>
                            <th>{{ ucfirst(__('Name')) }}</th>
                            <th>{{ ucfirst(__('Punkte')) }}</th>
                            <th>{{ ucfirst(__('Kontinent')) }}</th>
                            <th>{{ ucfirst(__('Koordinaten')) }}</th>
                            <th>{{ ucfirst(__('Besitzer')) }}</th>
                            <th>{{ ucfirst(__('Eroberungen')) }}</th>
                            <th>{{ ucfirst(__('Bonus')) }}</th>
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
                    <h4 class="card-title">{{ __('Punkte') }}:</h4>
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
