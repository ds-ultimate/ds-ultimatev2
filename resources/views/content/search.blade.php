@extends('layouts.app')

@section('titel', __('ui.titel.search'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="col-md-5 p-lg-5 mx-auto my-1 text-center">
                <h1 class="font-weight-normal">{{ __('ui.titel.search') }}: {!! ucfirst(($type == 'player')? __('ui.tabletitel.player'): (($type == 'ally')? __('ui.tabletitel.allys'): __('ui.tabletitel.villages'))) !!}</h1>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ucfirst(__('ui.titel.searchResults'))}}: {{ count($result) }}</h4>
                    @if (count($result) >= App\Http\Controllers\SearchController::$limit)
                    {{ str_replace('$limit', App\Http\Controllers\SearchController::$limit, __('ui.titel.searchLimited')) }}<br><br>
                    @endif
                    @if ($type == 'player')
                        <table id="table_id" class="table table-striped table-hover table-sm w-100">
                            <thead><tr>
                                <th>{{ ucfirst(__('ui.table.world')) }}</th>
                                <th>{{ ucfirst(__('ui.table.name')) }}</th>
                                <th>{{ ucfirst(__('ui.table.points')) }}</th>
                                <th>{{ ucfirst(__('ui.table.villages')) }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($result as $player)
                                <tr>
                                    <th>{{$player['world']->display_name}}</th>
                                    <td>{!! \App\Util\BasicFunctions::linkPlayer($player['world'],$player['player']->playerID,\App\Util\BasicFunctions::outputName($player['player']->name)) !!}</td>
                                    <td>{{\App\Util\BasicFunctions::numberConv($player['player']->points_top)}}</td>
                                    <td>{{\App\Util\BasicFunctions::numberConv($player['player']->village_count_top)}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @elseif ($type == 'ally')
                        <table id="table_id" class="table table-hover table-sm w-100">
                            <thead><tr>
                                <th>{{ ucfirst(__('ui.table.world')) }}</th>
                                <th>{{ ucfirst(__('ui.table.name')) }}</th>
                                <th>{{ ucfirst(__('ui.table.tag')) }}</th>
                                <th>{{ ucfirst(__('ui.table.points')) }}</th>
                                <th>{{ ucfirst(__('ui.table.villages')) }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($result as $ally)
                                <tr>
                                    <th>{{$ally['world']->display_name}}</th>
                                    <td>{!! \App\Util\BasicFunctions::linkAlly($ally['world'],$ally['ally']->allyID,\App\Util\BasicFunctions::outputName($ally['ally']->name)) !!}</td>
                                    <td>{!! \App\Util\BasicFunctions::linkAlly($ally['world'],$ally['ally']->allyID,\App\Util\BasicFunctions::outputName($ally['ally']->tag)) !!}</td>
                                    <td>{{\App\Util\BasicFunctions::numberConv($ally['ally']->points_top)}}</td>
                                    <td>{{\App\Util\BasicFunctions::numberConv($ally['ally']->village_count_top)}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <table id="table_id" class="table table-hover table-sm w-100">
                            <thead><tr>
                                <th>{{ ucfirst(__('ui.table.world')) }}</th>
                                <th>{{ ucfirst(__('ui.table.name')) }}</th>
                                <th>{{ ucfirst(__('ui.table.points')) }}</th>
                                <th>{{ ucfirst(__('ui.table.continent')) }}</th>
                                <th>{{ ucfirst(__('ui.table.coordinates')) }}</th>
                                <th>{{ ucfirst(__('ui.table.bonusType')) }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($result as $village)
                                <tr>
                                    <th>{{$village['world']->display_name}}</th>
                                    <td>{!! \App\Util\BasicFunctions::linkVillage($village['world'],$village['village']->villageID,\App\Util\BasicFunctions::outputName($village['village']->name)) !!}</td>
                                    <td>{{\App\Util\BasicFunctions::numberConv($village['village']->points)}}</td>
                                    <td>{{$village['village']->continentString()}}</td>
                                    <td>{{$village['village']->coordinates()}}</td>
                                    <td>{{$village['village']->bonusText()}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready( function () {
            $('#table_id').DataTable({
                ordering: false,
                responsive: true,
                {!! \App\Util\Datatable::language() !!}
            });
        } );
    </script>
@endpush
