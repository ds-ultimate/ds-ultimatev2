@extends('layouts.temp')

@section('titel', $worldData->displayName())

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="col-md-5 p-lg-5 mx-auto my-1 text-center">
                <h1 class="font-weight-normal">{{ $worldData->displayName() }}</h1>
            </div>
        </div>
        <div class="col-12 col-md-6 mt-2">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">{{ __('ui.tabletitel.top10').' '.__('ui.tabletitel.player') }}</h2>
                    <table class="table table-striped"  id="t10Player">
                        <thead>
                        <tr>
                            <th>{{ ucfirst(__('ui.table.rank')) }}</th>
                            <th>{{ ucfirst(__('ui.table.name')) }}</th>
                            <th>{{ ucfirst(__('ui.table.points')) }}</th>
                            <th>{{ ucfirst(__('ui.table.villages')) }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($playerArray as $player)
                            <tr>
                                <th>{{ $player->rank }}</th>
                                <td class="text-truncate" style="max-width: 200px">{!! \App\Util\BasicFunctions::linkPlayer($worldData, $player->playerID, \App\Util\BasicFunctions::outputName($player->name)) !!}</td>
                                <td>{{ \App\Util\BasicFunctions::numberConv($player->points) }}</td>
                                <td>{{ \App\Util\BasicFunctions::numberConv($player->village_count) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 mt-2">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">{{ __('ui.tabletitel.top10').' '.__('ui.tabletitel.ally') }}</h2>
                    <table class="table table-striped" id="t10Ally">
                        <thead>
                        <tr>
                            <th>{{ ucfirst(__('ui.table.rank')) }}</th>
                            <th>{{ ucfirst(__('ui.table.name')) }}</th>
                            <th>{{ ucfirst(__('ui.table.tag')) }}</th>
                            <th>{{ ucfirst(__('ui.table.points')) }}</th>
                            <th>{{ ucfirst(__('ui.table.members')) }}</th>
                            <th>{{ ucfirst(__('ui.table.villages')) }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($allyArray as $ally)
                            <tr>
                                <th>{{ $ally->rank }}</th>
                                <td class="text-truncate" style="max-width: 130px">{!! \App\Util\BasicFunctions::linkAlly($worldData, $ally->allyID, \App\Util\BasicFunctions::outputName($ally->name))!!}</td>
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
