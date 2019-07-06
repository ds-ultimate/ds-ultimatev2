@extends('layouts.temp')

@section('titel', ucfirst(__('Dorf')).': '.\App\Util\BasicFunctions::outputName($villageData->name))

@section('content')
    <div class="row">
        <div class="p-lg-5 mx-auto my-1 text-center">
            <h1 class="font-weight-normal">{{ ucfirst(__('Dorf')).': '.\App\Util\BasicFunctions::outputName($villageData->name) }}</h1>
        </div>
        <div class="col-12 mx-2">
            <div class="card">
                <table class="table table-bordered no-wrap">
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
                        <th>{{ \App\Util\BasicFunctions::getContinentString($villageData) }}</th>
                        <td>{{ $villageData->x.'|'.$villageData->y }}</td>
                        <td>{!! ($villageData->owner != 0)?\App\Util\BasicFunctions::linkPlayer($worldData, $villageData->owner, \App\Util\BasicFunctions::outputName($villageData->playerLatest->name)) : ucfirst(__('Barbaren')) !!}</td>
                        <td>{{ $conquer->get('total') }}</td>
                        <th>{{ \App\Util\BasicFunctions::bonusIDtoHTML($villageData->bonus_id) }}</th>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-1"></div>
        <div class="col-6">
            <div class="col-12">
                Diagramm:  __('chart.titel_points')
            </div>
            <div class="col-12">
                <div id="points" class="col-12 position-absolute px-0">
                    <div class="card">
                        <div id="chart-points"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
        </div>
    </div>
@endsection

@section('js')
    {!! $chartJS !!}
@endsection
