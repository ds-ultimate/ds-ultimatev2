@extends('layouts.app')

@section('titel', $worldData->display_name,': '.__('tool.distCalc.title'))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="col-12 p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('tool.distCalc.title')).' ['.$worldData->display_name.']' }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('tool.distCalc.title')).' ' }}
            </h1>
            <h4>
                {{ '['.$worldData->display_name.']' }}
            </h4>
        </div>
        <!-- ENDE Titel für Mobile Geräte -->
        <!-- Village Card -->
        <div class="col-12 col-lg-6 mt-2">
            <div class="card" style="height: 500px">
                <div class="card-body">
                    <h4 class="card-title">{{ __('ui.tabletitel.general') }}:</h4>
                    <form id="villageForm" method="POST" action="">
                        <table class="table table-bordered table-striped nowrap">
                            <tr>
                                <th>{{ __('tool.distCalc.startVillage') }}</th>
                                <td>
                                    <div class="form-inline" data-writeTarget="#startVillage">
                                        <input id="xStart" class="form-control mx-auto col-4 coord-input x-coord" type="text" inputmode="numeric" placeholder="500" maxlength="3"> |
                                        <input id="yStart" class="form-control mx-auto col-4 coord-input y-coord" type="text" inputmode="numeric" placeholder="500" maxlength="3">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('tool.distCalc.targetVillage') }}</th>
                                <td>
                                    <div class="form-inline" data-writeTarget="#targetVillage">
                                        <input id="xTarget" class="form-control mx-auto col-4 coord-input x-coord" type="text" inputmode="numeric" placeholder="500" maxlength="3"> |
                                        <input id="yTarget" class="form-control mx-auto col-4 coord-input y-coord" type="text" inputmode="numeric" placeholder="500" maxlength="3">
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <br>
                    <table id="startVillage" class="table table-striped table-bordered nowrap">
                        <tr><th width="150">{{ __('tool.distCalc.startVillage') }}</th><td> - </td></tr>
                        <tr><th>{{ __('ui.table.points') }}</th><td> - </td></tr>
                        <tr><th>{{ __('ui.table.owner') }}</th><td> - </td></tr>
                        <tr><th>{{ __('ui.table.ally') }}</th><td> - </td></tr>
                    </table>
                    <br>
                    <table id="targetVillage" class="table table-striped table-bordered nowrap">
                        <tr><th width="150">{{ __('tool.distCalc.targetVillage') }}</th><td> - </td></tr>
                        <tr><th>{{ __('ui.table.points') }}</th><td> - </td></tr>
                        <tr><th>{{ __('ui.table.owner') }}</th><td> - </td></tr>
                        <tr><th>{{ __('ui.table.ally') }}</th><td> - </td></tr>
                    </table>
                </div>
            </div>
        </div>
        <!-- ENDE Village Card -->
        <!-- Unit Card -->
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
                            <td id="spearTime">
                                {{ \App\Util\BasicFunctions::convertTime(round(floatval($unitConfig->spear->speed)*60)*1000) }}
                            </td>
                        </tr>
                        <tr>
                            <td><img src="{{ \App\Util\Icon::icons(1) }}">{{ __('ui.unit.sword') }}</td>
                            <td id="swordTime">
                                {{ \App\Util\BasicFunctions::convertTime(round(floatval($unitConfig->sword->speed)*60)*1000) }}
                            </td>
                        </tr>
                        <tr>
                            <td><img src="{{ \App\Util\Icon::icons(2) }}">{{ __('ui.unit.axe') }}</td>
                            <td id="axeTime">
                                {{ \App\Util\BasicFunctions::convertTime(round(floatval($unitConfig->axe->speed)*60)*1000) }}
                            </td>
                        </tr>
                        @if ($config->game->archer == 1)
                            <tr>
                                <td><img src="{{ \App\Util\Icon::icons(3) }}">{{ __('ui.unit.archer') }}</td>
                                <td id="archerTime">
                                    {{ \App\Util\BasicFunctions::convertTime(round(floatval($unitConfig->archer->speed)*60)*1000) }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td><img src="{{ \App\Util\Icon::icons(4) }}">{{ __('ui.unit.spy') }}</td>
                            <td id="spyTime">
                                {{ \App\Util\BasicFunctions::convertTime(round(floatval($unitConfig->spy->speed)*60)*1000) }}
                            </td>
                        </tr>
                        <tr>
                            <td><img src="{{ \App\Util\Icon::icons(5) }}">{{ __('ui.unit.light') }}</td>
                            <td id="lightTime">
                                {{ \App\Util\BasicFunctions::convertTime(round(floatval($unitConfig->light->speed)*60)*1000) }}
                            </td>
                        </tr>
                        @if ($config->game->archer == 1)
                            <tr>
                                <td><img src="{{ \App\Util\Icon::icons(6) }}">{{ __('ui.unit.marcher') }}</td>
                                <td id="marcherTime">
                                    {{ \App\Util\BasicFunctions::convertTime(round(floatval($unitConfig->marcher->speed)*60)*1000) }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td><img src="{{ \App\Util\Icon::icons(7) }}">{{ __('ui.unit.heavy') }}</td>
                            <td id="heavyTime">
                                {{ \App\Util\BasicFunctions::convertTime(round(floatval($unitConfig->heavy->speed)*60)*1000) }}
                            </td>
                        </tr>
                        <tr>
                            <td><img src="{{ \App\Util\Icon::icons(8) }}">{{ __('ui.unit.ram') }}</td>
                            <td id="ramTime">
                                {{ \App\Util\BasicFunctions::convertTime(round(floatval($unitConfig->ram->speed)*60)*1000) }}
                            </td>
                        </tr>
                        <tr>
                            <td><img src="{{ \App\Util\Icon::icons(9) }}">{{ __('ui.unit.catapult') }}</td>
                            <td id="catapultTime">
                                {{ \App\Util\BasicFunctions::convertTime(round(floatval($unitConfig->catapult->speed)*60)*1000) }}
                            </td>
                        </tr>
                        @if ($config->game->knight > 0)
                            <tr>
                                <td><img src="{{ \App\Util\Icon::icons(10) }}">{{ __('ui.unit.knight') }}</td>
                                <td id="knightTime">
                                    {{ \App\Util\BasicFunctions::convertTime(round(floatval($unitConfig->knight->speed)*60)*1000) }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td><img src="{{ \App\Util\Icon::icons(11) }}">{{ __('ui.unit.snob') }}</td>
                            <td id="snobTime">
                                {{ \App\Util\BasicFunctions::convertTime(round(floatval($unitConfig->snob->speed)*60)*1000) }}
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
    <script>
        function pad(d) {
            return (d < 10) ? '0' + d.toString() : d.toString();
        }

        function convertTime(input){
            var input1 = Math.round( input * 60);
            var seconds = (input1 % 60);
            var input2 = Math.floor(input1 / 60);
            var minutes = input2 % 60;
            var input3 = Math.floor(input2 / 60);
            var hour = input3 % 24;
            var day = Math.floor(input3 / 24);
            
            //console.log(input, input1, seconds, input2, minutes, input3, hour, day);
            return day + '&nbsp;{{ __('tool.distCalc.days') }}' + '&nbsp;' + pad(hour) + ':' + pad(minutes) + ':' + pad(seconds);
        }

        function calc(){
            var xStart = $('#villageForm #xStart');
            var yStart = $('#villageForm #yStart');
            var xTarget = $('#villageForm #xTarget');
            var yTarget = $('#villageForm #yTarget');
            
            var xDis = xStart.val() - xTarget.val();
            var yDis = yStart.val() - yTarget.val();
            var dis = Math.sqrt(xDis * xDis + yDis * yDis);
            //console.log(dis);
            
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
        
        function checkVillage(x, y, parent) {
            var writeResultTo = $(parent.attr("data-writeTarget"));
            if (x != '' && y != '') {
                var url = '{{ route('api.villageByCoord', [$worldData->id, '%xCoord%', '%yCoord%']) }}';
                axios.get(url.replaceAll('%xCoord%', x).replaceAll('%yCoord%', y), {})
                    .then((response) => {
                        $(".coord-input", parent).removeClass("is-invalid");
                        $(".coord-input", parent).addClass("is-valid");
                        
                        const data = response.data.data;
                        writeResultTo.find('tr:nth-child(1) td').html(data.name.trunc(25) + ' <b>' + data.x + '|' + data.y + '</b>  [' + data.continent + ']');
                        writeResultTo.find('tr:nth-child(2) td').html(numeral(data.points).format('0,0'));
                        writeResultTo.find('tr:nth-child(3) td').html(data.ownerName);
                        writeResultTo.find('tr:nth-child(4) td').html(data.ownerAllyName);
                    })
                    .catch((error) => {
                        $(".coord-input", parent).removeClass("is-valid");
                        $(".coord-input", parent).addClass("is-invalid");
                        
                        writeResultTo.find('tr:nth-child(1) td').html('{{ __('ui.villageNotExist') }} ' + $('.x-coord', parent).val() + '|' + $('.y-coord', parent).val());
                        writeResultTo.find('tr:nth-child(2) td').html('-');
                        writeResultTo.find('tr:nth-child(3) td').html('-');
                        writeResultTo.find('tr:nth-child(4) td').html('-');
                    });

                calc();
            }
        }
        
        $(function () {
            $(".coord-input").on("input", function (e) {
                if (this.value.length == this.maxLength) {
                    var next = $(this).nextAll(".coord-input:first")
                    if(next) {
                        next.focus()
                    }
                }
                var inputs = $(this).parent().children(".coord-input")
                var x = $(inputs[0]).val()
                var y = $(inputs[1]).val()
                checkVillage(x, y, $(this).parent())
            })
            
            $(".coord-input").on('paste', function(e) {
                var pastedData = e.originalEvent.clipboardData.getData('text')
                var match = pastedData.match(/(\d{1,3})\|(\d{1,3})/)
                if(match !== null) {
                    e.preventDefault()
                    x = match[1]
                    y = match[2]

                    var inputs = $(this).parent().children(".coord-input")
                    $(inputs[0]).val(x)
                    $(inputs[1]).val(y)

                    checkVillage(x, y, $(this).parent())
                }
            })
            
            $('.coord-input.x-coord').trigger('input')
        })
    </script>
@endpush
