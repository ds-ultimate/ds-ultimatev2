@extends('layouts.app')

@section('titel', $worldData->display_name,': '.__('tool.distCalc.title'))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="col-12 p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('tool.pointCalc.title')).' ['.$worldData->display_name.']' }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('tool.pointCalc.title')).' ' }}
            </h1>
            <h4>
                {{ '['.$worldData->display_name.']' }}
            </h4>
        </div>
        <!-- ENDE Titel für Mobile Geräte -->
        <!-- Building Card -->
        <?php
        function generateBuildingEntry($name, $conf) {
        ?>
        <tr>
            <th><img src="{{ \App\Util\BuildingUtils::getImage($name) }}"/> {{ ucfirst(__("ui.buildings." . $name)) }}</th>
            <td>
                <select id="{{ $name }}-level" class="input-calc form-control form-control-sm">
                    @for ($i = intval($conf->min_level); $i <= intval($conf->max_level); $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </td>
            {{-- <td><div id="{{ $name }}-time">?????</div></td> --}}
            <td><div id="{{ $name }}-farm" class="text-right">?????</div></td>
            <td><div id="{{ $name }}-points" class="text-right">?????</div></td>
        </tr>
        <?php
        }
        ?>
        <div class="col-12 col-md-6 mt-2">
            <div class="card mb-4 p-3"><p>
                Derzeit gibt es noch keine Werte für die Bauzeiten, weil die Formel nicht bekannt ist.
                Solltest du mithelfen wollen / die Formel kennen so kontaktire uns bitte über <a href="https://discord.gg/JcDAmPm">Discord</a><br>
                <a href="{{ route("tools.collectData") }}">Es gibt die öffentliche Datensammlung, wo wir Bauzeiten in kombination mit Gebäudestufen sammeln.</a>
                Um spam zu vermeiden musst du eingeloggt sein.
            </p></div>
            <div class="card">
                <div class="card-body">
                    <table class="table w-100">
                        <thead>
                        <tr>
                            <th>Geb&auml;ude</th>
                            <th>Stufe</th>
                            {{-- <th>Bauzeit</th> --}}
                            <th class="text-right" style="width: 100px;"> Einwohner</th>
                            <th class="text-right" style="width: 100px;"> Punkte</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($buildConfig as $name => $value)
                            <?php $buildCache = \App\Util\BuildingUtils::$BUILDINGS[$name] ?>
                            @if($buildCache['max_level'] >= 0)
                                {!! generateBuildingEntry($name, $value) !!}
                            @endif
                        @endforeach
                        <tr>
                            <th>Punkte</th>
                            <td></td>
                            {{-- <td></td> --}}
                            <td class="text-right" style="font-weight: bold;" class="" id="ges-farm">0</td>
                            <td class="text-right" style="font-weight: bold;" class="" id="ges-points">0</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- ENDE Building Card -->
    </div>
@endsection

@push('js')
<script>
var buildConf = {
    @foreach ($buildConfig as $name => $value)
        <?php $buildCache = \App\Util\BuildingUtils::$BUILDINGS[$name] ?>
        @if($buildCache['max_level'] >= 0)
        "{{ $name }}": {
            "pop": "{{ $buildCache['pop'] }}",
            "pop_factor": "{{ $buildCache['pop_factor'] }}",
            "build_time": "{{ $buildCache['build_time'] }}",
            "build_time_factor": "{{ $buildCache['build_time_factor'] }}",
            "points": "{{ $buildCache['point']}}",
            "points_factor": "{{ $buildCache['point_factor'] }}",
        },
        @endif
    @endforeach
}

var mainFactor = {{ \App\Util\BuildingUtils::$MAIN_REDUCTION }};

var worldSpeed = {{ $config->speed }};

$(function () {
    $('.input-calc').change(recalculate);
    recalculate();
});

function recalculate() {
    var gesPoints = 0;
    var gesFarm = 0;
    
    $.each(buildConf, function(index, data) {
        /*var buildTime = calculateTimeLevel(data['build_time'],
                data['build_time_factor'], $('#' + index + '-level').val());
        $('#' + index + '-time').text(toTime(buildTime));*/
        
        var farmSpace = calculateExponentialLevel(data['pop'], data['pop_factor'],
                $('#' + index + '-level').val());
        $('#' + index + '-farm').text(farmSpace);
        
        var points = calculateExponentialLevel(data['points'], data['points_factor'],
                $('#' + index + '-level').val());
        $('#' + index + '-points').text(points);
        
        gesPoints += points;
        gesFarm += farmSpace;
    });
    
    $('#ges-farm').text(gesFarm);
    $('#ges-points').text(gesPoints);
}

function calculateTimeLevel(baseVal, factor, level) {
    if(level < 1) return 0;
    if(level < 3)
        return Math.round(baseVal*1.18*Math.pow(factor, -13) * 
                Math.pow(mainFactor, -$('#main-level').val()) / worldSpeed);
    
    return Math.round(baseVal*1.18*Math.pow(factor, level - 1 - 14/(level-1) ) *
            Math.pow(mainFactor, -$('#main-level').val()) / worldSpeed);
}

function calculateExponentialLevel(baseVal, factor, level) {
    if(level < 1) return 0;
    
    return Math.round(Math.pow(factor, level-1) * baseVal);
}

function toTime(seconds) {
    var h = Math.floor(seconds / 3600);
    var m = Math.floor((seconds - 3600*h) / 60);
    var s = Math.floor(seconds - 3600*h - 60*m);
    
    if(h < 10) h = "0" + h;
    if(m < 10) m = "0" + m;
    if(s < 10) s = "0" + s;
    
    return h + ":" + m + ":" + s
}
</script>
@endpush
