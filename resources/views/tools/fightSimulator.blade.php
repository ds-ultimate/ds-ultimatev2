@extends('layouts.app')

@section('titel', $worldData->getDistplayName(),': '.__('tool.distCalc.title'))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="col-12 p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('tool.fightSimulator.title')).' ['.$worldData->getDistplayName().']' }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('tool.fightSimulator.title')).' ' }}
            </h1>
            <h4>
                {{ '['.$worldData->getDistplayName().']' }}
            </h4>
        </div>
        <!-- ENDE Titel für Mobile Geräte -->
        <!-- Simulator Input Card -->
        <div class="card col-12">
            <div class="card-body">
                <form id="simulatorForm" class="row">
                    <!-- Attacker -->
                    <div class="col-12 col-md-6 col-xl-4 mt-2">
                        <h2 class="mb-3 text-center">{{ __('tool.fightSimulator.attackerUnitInput') }}</h2>
                        <table class="nowrap">
                            @foreach($worldUnits as $unit)
                                <tr class="w-100" data-toggle="tooltip" title="{{ __("ui.unit.$unit") }}">
                                    <td class="text-right pr-4"><label for="inp-unit-att-{{ $unit }}" class="mb-1"><img class="pr-2" src="{{ \App\Util\Icon::unitIconFromName($unit) }}"></label></td>
                                    <td class="w-100"><input type="number" id="inp-unit-att-{{ $unit }}" name="attacker[{{ $unit }}]" class="w-100 rounded border px-2 py-1" min="0" value="0"></td>
                                </tr>
                            @endforeach
                        </table>
                    </div>

                    <!-- Defender -->
                    <div class="col-12 col-md-6 col-xl-4 mt-2">
                        <h2 class="mb-3 text-center">{{ __('tool.fightSimulator.defenderUnitInput') }}</h2>
                        <table class="nowrap">
                            @foreach($worldUnits as $unit)
                                <tr class="w-100" data-toggle="tooltip" title="{{ __("ui.unit.$unit") }}">
                                    <td class="text-right pr-4"><label for="inp-unit-def-{{ $unit }}" class="mb-1"><img class="pr-2" src="{{ \App\Util\Icon::unitIconFromName($unit) }}"></label></td>
                                    <td class="w-100"><input type="number" id="inp-unit-def-{{ $unit }}" name="defender[{{ $unit }}]" class="w-100 rounded border px-2 py-1" min="0" value="0"></td>
                                </tr>
                            @endforeach
                        </table>
                    </div>

                    <!-- Modifiers -->
                    <div class="col-12 col-md-6 col-xl-4 mt-2">
                        <h2 class="mb-3 text-center">{{ __('tool.fightSimulator.modifierInput') }}</h2>
                        <table class="nowrap">
                            <tr class="w-100">
                                <td class="text-right pr-4"><label for="inp-wall" class="mb-1">{{ __("tool.fightSimulator.wallLevel") }}</label></td>
                                <td class="w-100"><input type="number" id="inp-wall" name="wall" class="w-100 rounded border px-2 py-1" min="0" value="0"></td>
                            </tr>
                            <tr class="w-100">
                                <td class="text-right pr-4"><label for="inp-catapultBuilding" class="mb-1">{{ __("tool.fightSimulator.catapultTarget") }}</label></td>
                                <td class="w-100">
                                    <input type="checkbox" id="inp-catapultTargetsWall" name="catapultTargetsWall" class="rounded border px-2 py-1">
                                    <label for="inp-catapultTargetsWall" class="mb-1">{{ __("tool.fightSimulator.catapultTargetsWall") }}</label><br />
                                    <input type="number" id="inp-catapultBuilding" name="catapultBuilding" class="w-100 rounded border px-2 py-1" min="0" value="0">
                                </td>
                            </tr>
                            <tr class="w-100">
                                <td class="text-right pr-4"><label for="inp-morale" class="mb-1">{{ __("tool.fightSimulator.morale") }} (%)</label></td>
                                <td class="w-100"><input type="number" id="inp-morale" name="morale" class="w-100 rounded border px-2 py-1" min="25" max="100" value="100"></td>
                            </tr>
                            <tr class="w-100">
                                <td class="text-right pr-4"><label for="inp-luck" class="mb-1">{{ __("tool.fightSimulator.luck") }} (%)</label></td>
                                <td class="w-100"><input type="number" id="inp-luck" name="luck" class="w-100 rounded border px-2 py-1" min="0" max="100" value="0"></td>
                            </tr>
                            <tr class="w-100">
                                <td colspan=2 class="text-center pr-4">
                                    <input type="checkbox" id="inp-nightBonus" name="nightBonus" class="rounded border px-2 py-1">
                                    <label for="inp-nightBonus" class="mb-1">{{ __("tool.fightSimulator.nightBonus") }}</label>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-12 mt-2">
                        <input type="submit" class="btn btn-success float-right" value="{{ __("tool.fightSimulator.simulate") }}">
                    </div>
                </form>
            </div>
        </div>
        <!-- Simulator Input Card -->
    </div>
    <div class="row justify-content-center">
        <div class="col-12 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ __('tool.fightSimulator.results') }}:</h4>
                </div>
                
                <div id="liveResultContent" class="card m-4"></div>
                <div id="historyList"></div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="{{ \App\Util\BasicFunctions::asset('js/toolFightSimulator.js') }}"></script>
<script>
    function gatherFormData() {
        let raw = $('#simulatorForm').serializeArray()
        let data = { attacker: {}, defender: {} }

        raw.forEach(field => {
            if (field.name.startsWith("attacker[")) {
                let key = field.name.match(/\[(.*?)\]/)[1]
                data.attacker[key] = +field.value
                if(isNaN(data.attacker[key])) return undefined
            } else if (field.name.startsWith("defender[")) {
                let key = field.name.match(/\[(.*?)\]/)[1]
                data.defender[key] = +field.value
                if(isNaN(data.defender[key])) return undefined
            } else {
                data[field.name] = field.value
            }
        })

        data["wall"] = +data["wall"]
        data["catapultBuilding"] = +data["catapultBuilding"]
        data["morale"] = +data["morale"]
        data["luck"] = +data["luck"]
        if(isNaN(data["wall"])) return undefined
        if(isNaN(data["catapultBuilding"])) return undefined
        if(isNaN(data["morale"])) return undefined
        if(isNaN(data["luck"])) return undefined
        data["nightBonus"] = data["nightBonus"] == "on" ? {{ $config->night->def_factor }} : 1
        data["catapultTargetsWall"] = data["catapultTargetsWall"] == "on"

        // below stuff that is still TODO 
        data["attackerBelieve"] = true
        data["defenderBelieve"] = true
        data["farmLimit"] = {{ $config->game->farm_limit }}
        data["cataChurch"] = false

        return data
    }
    
    function resultTableRow(rowTitle, units) {
        return `
            <tr>
                <td class="text-right pr-2">${rowTitle}</td>
                @foreach($worldUnits as $unit)
                    <td>${units.{{ $unit}}}</td>
                @endforeach
            </tr>`
    }
    
    const resultTableHeader = `
        <tr>
            <td></td>
            @foreach($worldUnits as $unit)
                <td><img class="pr-2" src="{{ \App\Util\Icon::unitIconFromName($unit) }}"></td>
            @endforeach
        </tr>
        `
                    
    const serverUnits = {
        @foreach($unitConfig as $unitName => $unitData)
        {{ $unitName }}: {
            attack: {{ $unitData->attack }},
            defense: {{ $unitData->defense }},
            defense_cavalry: {{ $unitData->defense_cavalry }},
            defense_archer: {{ $unitData->defense_archer }},
        },
        @endforeach
    }
    
    function updateLiveResult() {
        const data = gatherFormData()
        if(data === undefined) {
            return
        }

        data["newSimulator"] = {{ $config->game->archer}} == 1;

        try {
            const result = runSimulation(data, serverUnits)
            let resultHTML = 
            $('#liveResultContent').html(`
                <table class="mt-2">
                    ${resultTableHeader}
                    ${resultTableRow("{{ __('tool.fightSimulator.resultAttacker') }}", result.attacker)}
                    ${resultTableRow("{{ __('tool.fightSimulator.resultLoss') }}", result.attackerLoss)}
                    ${resultTableRow("{{ __('tool.fightSimulator.resultSurvivor') }}", result.attackerSurvivor)}
                </table>
                <table class="mt-2">
                    ${resultTableHeader}
                    ${resultTableRow("{{ __('tool.fightSimulator.resultDefender') }}", result.defender)}
                    ${resultTableRow("{{ __('tool.fightSimulator.resultLoss') }}", result.defenderLoss)}
                    ${resultTableRow("{{ __('tool.fightSimulator.resultSurvivor') }}", result.defenderSurvivor)}
                </table>
                <div class="col-12 text-center mt-2">
                    {{ __("tool.fightSimulator.wallChange") }}: ${result.wallOld} => ${result.wallNew}<br>
                    {{ __("tool.fightSimulator.catapultChange") }}: ${result.catapultOld} => ${result.catapultNew}<br>
                </div>
            `)
        } catch(e) {
            createToast(e.message, "Internal error", 'now', 'fas fa-exclamation-circle text-danger');
            console.log(e)
        }
    }

    // Hook inputs for live update
    $('#simulatorForm').on('input change', 'input, select', updateLiveResult)

    // Simulate button: push live result into history
    $('#simulatorForm').on('submit', function (e) {
        e.preventDefault()

        const liveContent = $('#liveResultContent')

        $('#historyList').prepend(
            liveContent.clone()
        )
    })

    // Init
    updateLiveResult()
</script>
@endpush
