@extends('layouts.app')

@section('titel', ucfirst(__('ui.titel.player')).': '.\App\Util\BasicFunctions::decodeName($playerTopData->name))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="p-lg-3 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('ui.titel.player')).': '.\App\Util\BasicFunctions::decodeName($playerTopData->name) }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-3 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('ui.titel.player')).': ' }}
            </h1>
            <h4>
                {{ \App\Util\BasicFunctions::decodeName($playerTopData->name) }}
            </h4>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <table id="history_table" class="table table-hover table-sm w-100 nowrap">
                        <thead>
                        <tr>
                            <th class="all">{{ ucfirst(__('ui.tabletitel.player')) }}</th>
                            <th class="all">{{ ucfirst(__('ui.table.bashOff')) }}</th>
                            <th class="all">{{ ucfirst(__('ui.table.bashDeff')) }}</th>
                            <th class="all">{{ ucfirst(__('ui.table.bashSup')) }}</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach(\App\AllySupport::getForPlayer($worldData, $playerTopData->playerID) as $sup)
                            <tr>
                                <td>{!! ($sup->ally_id == 0)?("Stammeslos"):(\App\Util\BasicFunctions::linkAlly($worldData, $sup->ally_id, \App\Util\BasicFunctions::outputName($sup->allyTop->tag))) !!}</td>
                                <td>{{ \App\Util\BasicFunctions::numberConv($sup->offBash) }}</td>
                                <td>{{ \App\Util\BasicFunctions::numberConv($sup->deffBash) }}</td>
                                <td>{{ \App\Util\BasicFunctions::numberConv($sup->supBash) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Informationen -->
        <div class="col-12">
            <div class="card">
                <div class="card-body tab-content">
                    <x-record.stat_elm_player_top :data='$playerTopData' :worldData='$worldData' :conquer='$conquer' :allyChanges='$allyChanges' :playerOtherServers='$playerOtherServers' exists="false"/>
                </div>
            </div>
        </div>
        <!-- ENDE Informationen -->
    </div>
@endsection

@push('js')
    <script>
        $(document).ready( function () {
            @isset($playerOtherServers)
                $(".otherworld").hover(function(e) {
                    if(e.type == "mouseenter") {
                        $('.otherworld-popup', this).removeClass("d-none").addClass("show");
                        //popover-body
                        if(! $('.otherworld-popup', this).hasClass("data-loaded")) {
                            $('.otherworld-popup', this).addClass("data-loaded");
                            var url = "{{ route('api.worldPopup', ['worldId', $playerTopData->playerID]) }}";
                            axios.get(url.replace("worldId", $(this).data("worldid")), {
                            })
                            .then((response) => {
                                $('.popover-body', this).html(response.data);
                                var lOffset = ($(this).width() - $('.otherworld-popup', this).width()) / 2;
                                $('.otherworld-popup', this)[0].style.left = lOffset + "px";
                            })
                            .catch((error) => {
                                $('.popover-body', this).html("-");
                            });
                        }
                    } else {
                        $('.otherworld-popup', this).addClass("d-none").removeClass("show");
                    }
                })
            @endisset
        });
    </script>
@endpush
