@extends('layouts.app')

@section('titel', $worldData->getDistplayName())

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="col-md-5 p-lg-5 mx-auto my-1 text-center">
                <h1 class="font-weight-normal">{{ $worldData->getDistplayName() }}</h1>
            </div>
        </div>
        <div class="col-12 col-lg-6 mt-2">
            <div class="card">
                <div class="card-body table-responsive">
                    <h2 class="card-title">{{ __('ui.tabletitel.top10').' '.__('ui.tabletitel.player') }}</h2>
                    <table class="table table-striped w-100" id="t10Player">
                        <thead>
                        <tr>
                            <th class="all">{{ ucfirst(__('ui.table.rank')) }}</th>
                            <th class="all">{{ ucfirst(__('ui.table.name')) }}</th>
                            <th class="all text-right">{{ ucfirst(__('ui.table.points')) }}</th>
                            <th class="min-tablet-p text-right">{{ ucfirst(__('ui.table.villages')) }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($playerArray as $player)
                            <tr>
                                <th>{{ $player->rank }}</th>
                                <td class="text-truncate" style="max-width: 200px">
                                    {!! \App\Util\BasicFunctions::linkPlayer($worldData, $player->playerID, \App\Util\BasicFunctions::outputName($player->name)) !!}
                                    @if($player->ally_id != 0 && $player->allyLatest != null)
                                    {!! \App\Util\BasicFunctions::linkAlly($worldData, $player->ally_id, "[" . \App\Util\BasicFunctions::outputName($player->allyLatest->tag) . "]")!!}
                                    @endif
                                </td>
                                <td class="text-right">{{ \App\Util\BasicFunctions::numberConv($player->points) }}</td>
                                <td class="text-right">{{ \App\Util\BasicFunctions::numberConv($player->village_count) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6 mt-2">
            <div class="card">
                <div class="card-body table-responsive">
                    <h2 class="card-title">{{ __('ui.tabletitel.top10').' '.__('ui.tabletitel.allys') }}</h2>
                    <table class="table table-striped w-100" id="t10Ally">
                        <thead>
                        <tr>
                            <th class="all">{{ ucfirst(__('ui.table.rank')) }}</th>
                            <th class="all">{{ ucfirst(__('ui.table.name')) }}</th>
                            <th class="all">{{ ucfirst(__('ui.table.tag')) }}</th>
                            <th class="all text-right">{{ ucfirst(__('ui.table.points')) }}</th>
                            <th class="desktop text-right">{{ ucfirst(__('ui.table.members')) }}</th>
                            <th class="desktop text-right">{{ ucfirst(__('ui.table.villages')) }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($allyArray as $ally)
                            <tr>
                                <th>{{ $ally->rank }}</th>
                                <td class="text-truncate" style="max-width: 130px">{!! \App\Util\BasicFunctions::linkAlly($worldData, $ally->allyID, \App\Util\BasicFunctions::outputName($ally->name))!!}</td>
                                <td>{!! \App\Util\BasicFunctions::linkAlly($worldData, $ally->allyID, \App\Util\BasicFunctions::outputName($ally->tag))!!}</td>
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

@push('js')
    <script>
        $(function () {
            $('#t10Player').DataTable({
                "responsive": true,
                "searching": false,
                "paging": false,
                "ordering": false,
                "info": false,
            });

            $('#t10Ally').DataTable({
                "responsive": true,
                "searching": false,
                "paging": false,
                "ordering": false,
                "info": false,
            });
        } );
    </script>
@endpush
