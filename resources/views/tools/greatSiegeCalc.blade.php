@extends('layouts.app')

@section('titel', $worldData->getDistplayName(),': '.__('tool.distCalc.title'))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="col-12 p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('tool.greatSiegeCalc.title')).' ['.$worldData->getDistplayName().']' }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('tool.greatSiegeCalc.title')).' ' }}
            </h1>
            <h4>
                {{ '['.$worldData->getDistplayName().']' }}
            </h4>
        </div>
        <!-- ENDE Titel für Mobile Geräte -->
        <!-- Input Card -->
        <div class="col-3 d-none d-lg-block"></div>
        <div class="col-12 col-lg-6 mt-2">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group input-group-sm mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{{ __('ui.table.date') }}</span>
                                </div>
                                <input id="dayInput" type="date" class="form-control form-control-sm" value="{{ date('Y-m-d', time()) }}" data-toggle="tooltip" data-placement="top" title="{{ __('tool.attackPlanner.date_helper') }}"/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-sm mb-3">
                                <div class="input-group-prepend">
                                    <button type="button" class="btn input-group-text dropdown-toggle dropdown-toggle-split time-title" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        {{ __('tool.attackPlanner.arrivalTime') }} <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item time-switcher" value="-1">{{ __('tool.attackPlanner.arrivalTime') }}</a>
                                        <a class="dropdown-item time-switcher" value="1">{{ __('tool.attackPlanner.sendTime') }}</a>
                                    </div>
                                    <input id="time_type" type="hidden" class="time-type" value="-1">
                                </div>
                                <input id="timeInput" type="time" step="0.001" class="form-control form-control-sm" value="{{ date('H:i:s', time()+3600) }}" data-toggle="tooltip" data-placement="top" title="{{ __('tool.attackPlanner.time_helper') }}"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3 d-none d-lg-block"></div>
        <!-- ENDE Input Card -->
        <!-- Unit Card -->
        <div class="col-3 d-none d-lg-block"></div>
        <div class="col-12 col-lg-6 mt-2">
            <div class="card" style="height: 500px">
                <div class="card-body">
                    <h4 class="card-title">{{ __('global.units') }}:</h4>
                    <table id="targetVillage" class="table table-striped table-bordered nowrap">
                        <tr>
                            <th width="200">{{ __('global.unit') }}</th>
                            <th>{{ __('global.time') }}</th>
                        </tr>
                        <tr>
                            <td><img src="{{ \App\Util\Icon::icons(0) }}">{{ __('ui.unit.spear') }}</td>
                            <td id="spearTime"></td>
                        </tr>
                        <tr>
                            <td><img src="{{ \App\Util\Icon::icons(1) }}">{{ __('ui.unit.sword') }}</td>
                            <td id="swordTime"></td>
                        </tr>
                        <tr>
                            <td><img src="{{ \App\Util\Icon::icons(2) }}">{{ __('ui.unit.axe') }}</td>
                            <td id="axeTime"></td>
                        </tr>
                        @if ($config->game->archer == 1)
                            <tr>
                                <td><img src="{{ \App\Util\Icon::icons(3) }}">{{ __('ui.unit.archer') }}</td>
                                <td id="archerTime"></td>
                            </tr>
                        @endif
                        <tr>
                            <td><img src="{{ \App\Util\Icon::icons(4) }}">{{ __('ui.unit.spy') }}</td>
                            <td id="spyTime"></td>
                        </tr>
                        <tr>
                            <td><img src="{{ \App\Util\Icon::icons(5) }}">{{ __('ui.unit.light') }}</td>
                            <td id="lightTime"></td>
                        </tr>
                        @if ($config->game->archer == 1)
                            <tr>
                                <td><img src="{{ \App\Util\Icon::icons(6) }}">{{ __('ui.unit.marcher') }}</td>
                                <td id="marcherTime"></td>
                            </tr>
                        @endif
                        <tr>
                            <td><img src="{{ \App\Util\Icon::icons(7) }}">{{ __('ui.unit.heavy') }}</td>
                            <td id="heavyTime"></td>
                        </tr>
                        <tr>
                            <td><img src="{{ \App\Util\Icon::icons(8) }}">{{ __('ui.unit.ram') }}</td>
                            <td id="ramTime"></td>
                        </tr>
                        <tr>
                            <td><img src="{{ \App\Util\Icon::icons(9) }}">{{ __('ui.unit.catapult') }}</td>
                            <td id="catapultTime"></td>
                        </tr>
                        @if ($config->game->knight > 0)
                            <tr>
                                <td><img src="{{ \App\Util\Icon::icons(10) }}">{{ __('ui.unit.knight') }}</td>
                                <td id="knightTime"></td>
                            </tr>
                        @endif
                        <tr>
                            <td><img src="{{ \App\Util\Icon::icons(11) }}">{{ __('ui.unit.snob') }}</td>
                            <td id="snobTime"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-3 d-none d-lg-block"></div>
        <!-- ENDE Unit Card -->
    </div>
@endsection

@push('js')
    <script>
        function pad(d) {
            return (d < 10) ? '0' + d.toString() : d.toString();
        }

        function convertTime(runtime, base){
            var rt = Math.round(runtime * 60)
            var d = new Date(base + rt * 1000)
            //console.log(d.getDate(), d.getMonth(), d.getFullYear(), d.getHours(), d.getMinutes(), d.getSeconds())
            return pad(d.getDate()) + "." + pad(d.getMonth()) + "." + d.getFullYear() + " "
                    + pad(d.getHours()) + ":" + pad(d.getMinutes()) + ":" + pad(d.getSeconds());
        }

        function calc(){
            var date = new Date($('#dayInput').val() + " " + $('#timeInput').val())
            var time = date.getTime()
            var mod = $('#time_type').val();
            
            $('#spearTime').html(convertTime({{ (float)$unitConfig->spear->speed }} * 15 * mod, time))
            $('#swordTime').html(convertTime({{ (float)$unitConfig->sword->speed }} * 15 * mod, time))
            $('#axeTime').html(convertTime({{ (float)$unitConfig->axe->speed }} * 15 * mod, time))
            @if ($config->game->archer == 1)
            $('#archerTime').html(convertTime({{ (float)$unitConfig->archer->speed }} * 15 * mod, time))
            @endif
            $('#spyTime').html(convertTime({{ (float)$unitConfig->spy->speed }} * 3 * mod, time))
            $('#lightTime').html(convertTime({{ (float)$unitConfig->light->speed }} * 15 * mod, time))
            @if ($config->game->archer == 1)
            $('#marcherTime').html(convertTime({{ (float)$unitConfig->marcher->speed }} * 15 * mod, time))
            @endif
            $('#heavyTime').html(convertTime({{ (float)$unitConfig->heavy->speed }} * 15 * mod, time))
            $('#ramTime').html(convertTime({{ (float)$unitConfig->ram->speed }} * 15 * mod, time))
            $('#catapultTime').html(convertTime({{ (float)$unitConfig->catapult->speed }} * 15 * mod, time))
            @if ($config->game->knight > 0)
            $('#knightTime').html(convertTime({{ (float)$unitConfig->knight->speed }} * 15 * mod, time))
            @endif
            $('#snobTime').html(convertTime({{ (float)$unitConfig->snob->speed }} * 15 * mod, time))
        }

        String.prototype.trunc = String.prototype.trunc ||
            function(n){
                return (this.length > n) ? this.substr(0, n-1) + '&hellip;' : this;
            };
        
        $(function () {
            $('#dayInput, #timeInput').on('change', calc);

            $('.time-switcher').click(function(e) {
                var time_id = $(this).attr("value");
                $(".time-title", $(this).parent().parent()).html($(this).html());
                $(".time-type", $(this).parent().parent()).val(time_id);
                calc();
            });
            
            $('.time-switcher[value=-1]').click();
        })
    </script>
@endpush
