@extends('layouts.app')

@section('titel', $worldData->displayName(),': '.__('tool.distCalc.title'))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="col-12 p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('tool.distCalc.title')).' ['.$worldData->displayName().']' }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('tool.distCalc.title')).' ' }}
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
                                <th>{{ __('tool.distCalc.startVillage') }}</th>
                                <td>
                                    <div class="form-inline">
                                        <input id="xStart" class="form-control mx-auto" type="text" placeholder="500" style="width: 70px" maxlength="3"> | <input id="yStart" class="form-control mx-auto" type="text" placeholder="500" style="width: 70px" maxlength="3">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('tool.distCalc.targetVillage') }}</th>
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
                            <th width="150">{{ __('tool.distCalc.startVillage') }}</th>
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
                            <th width="150">{{ __('tool.distCalc.targetVillage') }}</th>
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
                                <img src="{{ \App\Util\Icon::icons(0) }}">
                                {{ __('ui.unit.spear') }}
                            </td>
                            <td id="spearTime">
                                {{ \App\Util\BasicFunctions::convertTime(floatval($unitConfig->spear->speed)*60*1000) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="{{ \App\Util\Icon::icons(1) }}">
                                {{ __('ui.unit.sword') }}
                            </td>
                            <td id="swordTime">
                                {{ \App\Util\BasicFunctions::convertTime(floatval($unitConfig->sword->speed)*60*1000) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="{{ \App\Util\Icon::icons(2) }}">
                                {{ __('ui.unit.axe') }}
                            </td>
                            <td id="axeTime">
                                {{ \App\Util\BasicFunctions::convertTime(floatval($unitConfig->axe->speed)*60*1000) }}
                            </td>
                        </tr>
                        @if ($config->game->archer == 1)
                            <tr>
                                <td>
                                    <img src="{{ \App\Util\Icon::icons(3) }}">
                                    {{ __('ui.unit.archer') }}
                                </td>
                                <td id="archerTime">
                                    {{ \App\Util\BasicFunctions::convertTime(floatval($unitConfig->archer->speed)*60*1000) }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td>
                                <img src="{{ \App\Util\Icon::icons(4) }}">
                                {{ __('ui.unit.spy') }}
                            </td>
                            <td id="spyTime">
                                {{ \App\Util\BasicFunctions::convertTime(floatval($unitConfig->spy->speed)*60*1000) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="{{ \App\Util\Icon::icons(5) }}">
                                {{ __('ui.unit.light') }}
                            </td>
                            <td id="lightTime">
                                {{ \App\Util\BasicFunctions::convertTime(floatval($unitConfig->light->speed)*60*1000) }}
                            </td>
                        </tr>
                        @if ($config->game->archer == 1)
                            <tr>
                                <td>
                                    <img src="{{ \App\Util\Icon::icons(6) }}">
                                    {{ __('ui.unit.marcher') }}
                                </td>
                                <td id="marcherTime">
                                    {{ \App\Util\BasicFunctions::convertTime(floatval($unitConfig->marcher->speed)*60*1000) }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td>
                                <img src="{{ \App\Util\Icon::icons(7) }}">
                                {{ __('ui.unit.heavy') }}
                            </td>
                            <td id="heavyTime">
                                {{ \App\Util\BasicFunctions::convertTime(floatval($unitConfig->heavy->speed)*60*1000) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="{{ \App\Util\Icon::icons(8) }}">
                                {{ __('ui.unit.ram') }}
                            </td>
                            <td id="ramTime">
                                {{ \App\Util\BasicFunctions::convertTime(floatval($unitConfig->ram->speed)*60*1000) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="{{ \App\Util\Icon::icons(9) }}">
                                {{ __('ui.unit.catapult') }}
                            </td>
                            <td id="catapultTime">
                                {{ \App\Util\BasicFunctions::convertTime(floatval($unitConfig->catapult->speed)*60*1000) }}
                            </td>
                        </tr>
                        @if ($config->game->knight > 0)
                            <tr>
                                <td>
                                    <img src="{{ \App\Util\Icon::icons(10) }}">
                                    {{ __('ui.unit.knight') }}
                                </td>
                                <td id="knightTime">
                                    {{ \App\Util\BasicFunctions::convertTime(floatval($unitConfig->knight->speed)*60*1000) }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td>
                                <img src="{{ \App\Util\Icon::icons(11) }}">
                                {{ __('ui.unit.snob') }}
                            </td>
                            <td id="snobTime">
                                {{ \App\Util\BasicFunctions::convertTime(floatval($unitConfig->snob->speed)*60*1000) }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <!-- ENDE Unit Card -->
    </div>
@endsection

@push('js')
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
        function pad(d) {
            return (d < 10) ? '0' + d.toString() : d.toString();
        }

        function convertTime(input){
            var input1 = Math.ceil( input * 60);
            var round = (input % 1000 >= 500) ? 1 : 0;
            var seconds = (input1 % 60) + round;
            var input2 = Math.floor(input1 / 60);
            var minutes = input2 % 60;
            var input3 = Math.floor(input2 / 60);
            var hour = input3 % 24;
            var day = Math.floor(input3 / 24);
            return day + '{{ __('tool.distCalc.days') }}' + '&nbsp;' + pad(hour) + ':' + pad(minutes) + ':' + pad(seconds);
        }

        function calc(){
            var xStart = $('#villageForm').find('#xStart');
            var yStart = $('#villageForm').find('#yStart');
            var xTarget = $('#villageForm').find('#xTarget');
            var yTarget = $('#villageForm').find('#yTarget');
            axios.get('{{ route('index') }}/api/{{ $worldData->server->code }}/{{ $worldData->name }}/villageCoords/'+ xStart.val() + '/' + yStart.val(), {

            })
                .then((response) =>{
                    var start = $('#startVillage');
                    const data = response.data.data;
                    start.find('tr:nth-child(1) td').html(data['name'].trunc(25) + ' <b>' + xStart.val() + '|' + yStart.val() + '</b>  [' + data['continent'] + ']');
                    start.find('tr:nth-child(2) td').html(numeral(data['points']).format('0,0'));
                    start.find('tr:nth-child(3) td').html(data['ownerName']);
                    start.find('tr:nth-child(4) td').html(data['ownerAllyName']);
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
                    start.find('tr:nth-child(2) td').html(numeral(data['points']).format('0,0'));
                    start.find('tr:nth-child(3) td').html(data['ownerName']);
                    start.find('tr:nth-child(4) td').html(data['ownerAllyName']);
                })
                .catch((error) =>{
                    var start = $('#targetVillage');
                    start.find('tr:nth-child(1) td').html('{{ __('ui.villageNotExist') }} ' + xTarget.val() + '|' + yTarget.val());
                    start.find('tr:nth-child(2) td').html('-');
                    start.find('tr:nth-child(3) td').html('-');
                    start.find('tr:nth-child(4) td').html('-');
                });


            var dis = (Math.round(Math.sqrt(Math.pow(xStart.val() - xTarget.val(), 2) + Math.pow(yStart.val() - yTarget.val(), 2)) * 1000) / 1000);
            $('#spearTime').html(convertTime({{ round((float)$unitConfig->spear->speed, 3) }} * dis));
            $('#swordTime').html(convertTime({{ round((float)$unitConfig->sword->speed, 3) }} * dis));
            $('#axeTime').html(convertTime({{ round((float)$unitConfig->axe->speed, 3) }} * dis));
            @if ($config->game->archer == 1)
            $('#archerTime').html(convertTime({{ round((float)$unitConfig->archer->speed, 3) }} * dis));
            @endif
            $('#spyTime').html(convertTime({{ round((float)$unitConfig->spy->speed, 3) }} * dis));
            $('#lightTime').html(convertTime({{ round((float)$unitConfig->light->speed, 3) }} * dis));
            @if ($config->game->archer == 1)
            $('#marcherTime').html(convertTime({{ round((float)$unitConfig->marcher->speed, 3) }} * dis));
            @endif
            $('#heavyTime').html(convertTime({{ round((float)$unitConfig->heavy->speed, 3) }} * dis));
            $('#ramTime').html(convertTime({{ round((float)$unitConfig->ram->speed, 3) }} * dis));
            $('#catapultTime').html(convertTime({{ round((float)$unitConfig->catapult->speed, 3) }} * dis));
            @if ($config->game->knight > 0)
            $('#knightTime').html(convertTime({{ round((float)$unitConfig->knight->speed, 3) }} * dis));
            @endif
            $('#snobTime').html(convertTime({{ round((float)$unitConfig->snob->speed, 3) }} * dis));
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
                if (event.keyCode == 13) {
                    calc();
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
                $("#xStart").val(coords[0].substring(0, 3));
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
                $("#xTarget").val(coords[0].substring(0, 3));
                $("#yTarget").val(coords[1].substring(0, 3));
            });

            $(document).on('submit', '#villageForm', function (e) {
                e.preventDefault();
                calc();
            });
        })
    </script>
@endpush
