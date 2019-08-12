@extends('layouts.temp')

@section('titel', $worldData->displayName(),': '.__('ui.tool.distCalc.title'))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="col-12 p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('ui.tool.distCalc.title')).' ['.$worldData->displayName().']' }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('ui.tool.distCalc.title')).' ' }}
            </h1>
            <h4>
                {{ '['.$worldData->displayName().']' }}
            </h4>
        </div>
        <!-- ENDE Titel für Mobile Geräte -->
        <!-- Village Card -->
        <div class="col-12 col-md-6 mt-2">
            <div class="card" style="height: 500px">
                <div class="card-body">
                    <h4 class="card-title">{{ __('ui.tabletitel.general') }}:</h4>
                    <form id="villageForm" method="POST" action="">
                        <table class="table table-bordered table-striped no-wrap">
                            <tr>
                                <th>{{ __('ui.tool.distCalc.startVillage') }}</th>
                                <td>
                                    <div class="form-inline">
                                        <input id="xStart" class="form-control mx-auto" type="text" placeholder="500" style="width: 70px" maxlength="3"> | <input id="yStart" class="form-control mx-auto" type="text" placeholder="500" style="width: 70px" maxlength="3">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('ui.tool.distCalc.targetVillage') }}</th>
                                <td>
                                    <div class="form-inline">
                                        <input id="xTarget" class="form-control mx-auto" type="text" placeholder="500" style="width: 70px" maxlength="3"> | <input id="yTarget" class="form-control mx-auto" type="text" placeholder="500" style="width: 70px" maxlength="3">
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <div class="form-inline justify-content-center">
                            <input class="form-control btn btn-primary" style="width: 250px" type="submit" value="{{ __('global.calculate') }}">
                        </div>
                    </form>
                    <br>
                    <table id="startVillage" class="table table-striped table-bordered no-wrap">
                        <tr>
                            <th width="150">{{ __('ui.tool.distCalc.startVillage') }}</th>
                            <td>
                                -
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('ui.table.points') }}</th>
                            <td>
                                -
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('ui.table.owner') }}</th>
                            <td>
                                -
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('ui.table.ally') }}</th>
                            <td>
                                -
                            </td>
                        </tr>
                    </table>
                    <br>
                    <table id="targetVillage" class="table table-striped table-bordered no-wrap">
                        <tr>
                            <th width="150">{{ __('ui.tool.distCalc.targetVillage') }}</th>
                            <td>
                                -
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('ui.table.points') }}</th>
                            <td>
                                -
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('ui.table.owner') }}</th>
                            <td>
                                -
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('ui.table.ally') }}</th>
                            <td>
                                -
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <!-- ENDE Village Card -->
        <!-- Unit Card -->
        <div class="col-12 col-md-6 mt-2">
            <div class="card" style="height: 500px">
                <div class="card-body">
                    <h4 class="card-title">{{ __('global.units') }}:</h4>
                    <table id="targetVillage" class="table table-striped table-bordered no-wrap">
                        <tr>
                            <th>{{ __('global.unit') }}</th>
                            <th>{{ __('global.time') }}</th>
                        </tr>
                        <tr>
                            <td width="200">
                                <img src="{{ asset('images/ds_images/unit/unit_spear.png') }}">
                                {{ __('ui.unit.spear') }}
                            </td>
                            <td id="spearTime">
                                {{ \App\Util\BasicFunctions::convertTime(round(floatval($unitConfig->spear->speed))*60*1000) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="{{ asset('images/ds_images/unit/unit_sword.png') }}">
                                {{ __('ui.unit.sword') }}
                            </td>
                            <td id="swordTime">
                                {{ \App\Util\BasicFunctions::convertTime(round(floatval($unitConfig->sword->speed))*60*1000) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="{{ asset('images/ds_images/unit/unit_axe.png') }}">
                                {{ __('ui.unit.axe') }}
                            </td>
                            <td id="axeTime">
                                {{ \App\Util\BasicFunctions::convertTime(round(floatval($unitConfig->axe->speed))*60*1000) }}
                            </td>
                        </tr>
                        @if ($config->game->archer == 1)
                            <tr>
                                <td>
                                    <img src="{{ asset('images/ds_images/unit/unit_archer.png') }}">
                                    {{ __('ui.unit.archer') }}
                                </td>
                                <td id="archerTime">
                                    {{ \App\Util\BasicFunctions::convertTime(round(floatval($unitConfig->archer->speed))*60*1000) }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td>
                                <img src="{{ asset('images/ds_images/unit/unit_spy.png') }}">
                                {{ __('ui.unit.spy') }}
                            </td>
                            <td id="spyTime">
                                {{ \App\Util\BasicFunctions::convertTime(round(floatval($unitConfig->spy->speed))*60*1000) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="{{ asset('images/ds_images/unit/unit_light.png') }}">
                                {{ __('ui.unit.light') }}
                            </td>
                            <td id="lightTime">
                                {{ \App\Util\BasicFunctions::convertTime(round(floatval($unitConfig->light->speed))*60*1000) }}
                            </td>
                        </tr>
                        @if ($config->game->archer == 1)
                            <tr>
                                <td>
                                    <img src="{{ asset('images/ds_images/unit/unit_marcher.png') }}">
                                    {{ __('ui.unit.marcher') }}
                                </td>
                                <td id="marcherTime">
                                    {{ \App\Util\BasicFunctions::convertTime(round(floatval($unitConfig->marcher->speed))*60*1000) }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td>
                                <img src="{{ asset('images/ds_images/unit/unit_heavy.png') }}">
                                {{ __('ui.unit.heavy') }}
                            </td>
                            <td id="heavyTime">
                                {{ \App\Util\BasicFunctions::convertTime(round(floatval($unitConfig->heavy->speed))*60*1000) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="{{ asset('images/ds_images/unit/unit_ram.png') }}">
                                {{ __('ui.unit.ram') }}
                            </td>
                            <td id="ramTime">
                                {{ \App\Util\BasicFunctions::convertTime(round(floatval($unitConfig->ram->speed))*60*1000) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="{{ asset('images/ds_images/unit/unit_catapult.png') }}">
                                {{ __('ui.unit.catapult') }}
                            </td>
                            <td id="catapultTime">
                                {{ \App\Util\BasicFunctions::convertTime(round(floatval($unitConfig->catapult->speed))*60*1000) }}
                            </td>
                        </tr>
                        @if ($config->game->knight > 0)
                            <tr>
                                <td>
                                    <img src="{{ asset('images/ds_images/unit/unit_knight.png') }}">
                                    {{ __('ui.unit.knight') }}
                                </td>
                                <td id="knightTime">
                                    {{ \App\Util\BasicFunctions::convertTime(round(floatval($unitConfig->knight->speed))*60*1000) }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td>
                                <img src="{{ asset('images/ds_images/unit/unit_snob.png') }}">
                                {{ __('ui.unit.snob') }}
                            </td>
                            <td id="snobTime">
                                {{ \App\Util\BasicFunctions::convertTime(round(floatval($unitConfig->snob->speed))*60*1000) }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <!-- ENDE Unit Card -->
    </div>
@endsection

@section('js')
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
        function pad(d) {
            return (d < 10) ? '0' + d.toString() : d.toString();
        }

        function convertTime(input){
            var input1 = Math.floor( input * 60);
            var round = (input % 1000 >= 500) ? 1 : 0;
            var seconds = (input1 % 60) + round;
            var input2 = Math.floor(input1 / 60);
            var minutes = input2 % 60;
            var input3 = Math.floor(input2 / 60);
            var hour = input3 % 24;
            var day = Math.floor(input3 / 24);
            return day + '{{ __('ui.tool.distCalc.days') }}' + '&nbsp;' + pad(hour) + ':' + pad(minutes) + ':' + pad(seconds);
        }

        String.prototype.trunc = String.prototype.trunc ||
            function(n){
                return (this.length > n) ? this.substr(0, n-1) + '&hellip;' : this;
            };

        $(document).ready(function () {

            $('input:text').on("keypress keyup blur",function (event) {
                $(this).val($(this).val().replace(/[^\d].+/, ""));
                if ((event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            });

            $("#xStart").keyup(function () {
                if (this.value.length == this.maxLength) {
                    $(this).next('#yStart').focus();
                }
            });

            $("#xStart").bind('paste', function(e) {
                var pastedData = e.originalEvent.clipboardData.getData('text');
                var coords = pastedData.split("|");
                $("#yStart").val(coords[1].substring(0, 3));
            });

            $("#xTarget").keyup(function () {
                if (this.value.length == this.maxLength) {
                    $(this).next('#yTarget').focus();
                }
            });

            $("#xTarget").bind('paste', function(e) {
                var pastedData = e.originalEvent.clipboardData.getData('text');
                var coords = pastedData.split("|");
                $("#yTarget").val(coords[1].substring(0, 3));
            });

            // TODO: calculate with press "ENTER"

            $(document).on('submit', '#villageForm', function (e) {
                e.preventDefault();
                var xStart = $(this).find('#xStart');
                var yStart = $(this).find('#yStart');
                var xTarget = $(this).find('#xTarget');
                var yTarget = $(this).find('#yTarget');
                axios.get('{{ route('index') }}/api/{{ $worldData->server->code }}/{{ $worldData->name }}/villageCoords/'+ xStart.val() + '/' + yStart.val(), {

                })
                    .then((response) =>{
                        var start = $('#startVillage');
                        const data = response.data.data;
                        start.find('tr:nth-child(1) td').html(data['name'].trunc(25) + ' <b>' + xStart.val() + '|' + yStart.val() + '</b>  [' + data['continent'] + ']');
                        start.find('tr:nth-child(2) td').html(data['points']);
                        start.find('tr:nth-child(3) td').html(data['ownerName']);
                        start.find('tr:nth-child(4) td').html(data['ownerAlly']);
                    })
                    .catch((error) =>{
                        var start = $('#startVillage');
                        start.find('tr:nth-child(1) td').html('{{ __('ui.villageNotExist') }} ' + xStart.val() + '|' + yStart.val());
                        start.find('tr:nth-child(2) td').html('-');
                        start.find('tr:nth-child(3) td').html('-');
                        start.find('tr:nth-child(4) td').html('-');
                    });

                axios.get('{{ route('index') }}/api/{{ $worldData->server->code }}/{{ $worldData->name }}/villageCoords/'+ xTarget.val() + '/' + yTarget.val(), {

                })
                    .then((response) =>{
                        var start = $('#targetVillage');
                        const data = response.data.data;
                        start.find('tr:nth-child(1) td').html(data['name'].trunc(25) + ' <b>' + xTarget.val() + '|' + yTarget.val() + '</b>  [' + data['continent'] + ']');
                        start.find('tr:nth-child(2) td').html(data['points']);
                        start.find('tr:nth-child(3) td').html(data['ownerName']);
                        start.find('tr:nth-child(4) td').html(data['ownerAlly']);
                    })
                    .catch((error) =>{
                        var start = $('#targetVillage');
                        start.find('tr:nth-child(1) td').html('{{ __('ui.villageNotExist') }} ' + xTarget.val() + '|' + yTarget.val());
                        start.find('tr:nth-child(2) td').html('-');
                        start.find('tr:nth-child(3) td').html('-');
                        start.find('tr:nth-child(4) td').html('-');
                    });


                var dis = Math.sqrt(Math.pow(xStart.val() - xTarget.val(), 2) + Math.pow(yStart.val() - yTarget.val(), 2));
                $('#spearTime').html(convertTime('{{ round((float)$unitConfig->spear->speed) }}' * dis));
                $('#swordTime').html(convertTime('{{ round((float)$unitConfig->sword->speed) }}' * dis));
                $('#axeTime').html(convertTime('{{ round((float)$unitConfig->axe->speed) }}' * dis));
                @if ($config->game->archer == 1)
                $('#archerTime').html(convertTime('{{ round((float)$unitConfig->archer->speed) }}' * dis));
                @endif
                $('#spyTime').html(convertTime('{{ round((float)$unitConfig->spy->speed) }}' * dis));
                $('#lightTime').html(convertTime('{{ round((float)$unitConfig->light->speed) }}' * dis));
                @if ($config->game->archer == 1)
                $('#marcherTime').html(convertTime('{{ round((float)$unitConfig->marcher->speed) }}' * dis));
                @endif
                $('#heavyTime').html(convertTime('{{ round((float)$unitConfig->heavy->speed) }}' * dis));
                $('#ramTime').html(convertTime('{{ round((float)$unitConfig->ram->speed) }}' * dis));
                $('#catapultTime').html(convertTime('{{ round((float)$unitConfig->catapult->speed) }}' * dis));
                @if ($config->game->knight > 0)
                $('#knightTime').html(convertTime('{{ round((float)$unitConfig->knight->speed) }}' * dis));
                @endif
                $('#snobTime').html(convertTime('{{ round((float)$unitConfig->snob->speed) }}' * dis));
            });
        })
    </script>
@endsection
