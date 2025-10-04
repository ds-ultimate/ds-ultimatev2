@extends('layouts.app')

@section('titel', $worldData->getDistplayName().': '. __('tool.coinCalc.title'))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="col-12 p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('tool.coinCalc.title')).' ['.$worldData->getDistplayName().']' }}</h1>
        </div>
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('tool.coinCalc.title')).' ' }}
            </h1>
            <h4>
                {{ '['.$worldData->getDistplayName().']' }}
            </h4>
        </div>

        <!-- Eingabe-Card -->
        <div class="col-12 col-lg-6 mt-2">
            <div class="card" style="height: 500px">
                <div class="card-body">
                    <h4 class="card-title">{{ __('ui.tabletitel.general') }}:</h4>

                    <form id="coin-calculator-form" method="POST" action="">
                        <table class="table table-bordered table-striped nowrap">
                            <tr>
                                <th><img src="{{ \App\Util\Icon::icons(11) }}"> {{ __('tool.coinCalc.currentAgLimit') }}
                                </th>
                                <td>
                                    <div class="form-inline">
                                        <input type="number" id="current-cap" class="form-control mx-auto col-6"
                                               min="0" step="1">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th><img src="{{ \App\Util\Icon::icons(11) }}"> {{ __('tool.coinCalc.desiredAgLimit') }}
                                </th>
                                <td>
                                    <div class="form-inline">
                                        <input type="number" id="target-cap" class="form-control mx-auto col-6"
                                               min="0" step="1">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th><img src="{{ \App\Util\Icon::icons(48) }}"> {{ __('tool.coinCalc.coinFlag') }}</th>
                                <td>
                                    <div class="form-inline">
                                        <select class="form-select mx-auto col-6" id="flag-percent">
                                            <option value="0">0%</option>
                                            <option value="10">10%</option>
                                            <option value="12">12%</option>
                                            <option value="14">14%</option>
                                            <option value="16">16%</option>
                                            <option value="18">18%</option>
                                            <option value="20">20%</option>
                                            <option value="22">22%</option>
                                            <option value="23">23%</option>
                                            <option value="24">24%</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th><img src="{{ \App\Util\Icon::icons(49) }}"> {{ __('tool.coinCalc.flagBooster') }}
                                </th>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="flag-booster">
                                        <label class="form-check-label"
                                               for="flag-booster">{{ __('tool.coinCalc.flagBooster') }}</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th><img src="{{ \App\Util\Icon::icons(50) }}"> {{ __('tool.coinCalc.nobleDecree') }}
                                </th>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="noble-decree">
                                        <label class="form-check-label"
                                               for="noble-decree">{{ __('tool.coinCalc.nobleDecree') }}</label>
                                    </div>
                                </td>
                            </tr>
                            @if($worldData->win_condition === 5)
                                <tr>
                                    <th><img
                                            src="{{ \App\Util\Icon::icons(51) }}"> {{ __('tool.coinCalc.runenFactor') }}
                                    </th>
                                    <td>
                                        <div class="form-inline">
                                            <select class="form-select mx-auto col-6" id="runeFactor">
                                                <option value="1"><10%</option>
                                                <option value="0.9"><=10% & < 20%</option>
                                                <option value="0.8"><=20% & < 30%</option>
                                                <option value="0.7"><=30% & < 40%</option>
                                                <option value="0.6"><=40% & < 50%</option>
                                                <option value="0.5"><=50% & < 60%</option>
                                                <option value="0.4"><= 60%</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </form>
                </div>
            </div>
        </div>
        <!-- ENDE Eingabe-Card -->

        <!-- Ergebnis-Card -->
        <div class="col-12 col-lg-6 mt-2">
            <div class="card" style="height: 500px">
                <div class="card-body">
                    <h4 class="card-title">{{ __('ui.tabletitel.general') }}:</h4>

                    <table id="resultTable" class="table table-striped table-bordered nowrap">
                        <tr>
                            <th><img src="{{ \App\Util\Icon::icons(47) }}"> {{ __('tool.coinCalc.result.coins') }}</th>
                            <td id="res-coins">-</td>
                        </tr>
                        <tr>
                            <th><img src="{{ \App\Util\Icon::icons(11) }}"> {{ __('tool.coinCalc.result.agLimit') }}
                            </th>
                            <td id="res-cap">-</td>
                        </tr>
                        <tr>
                            <th><img src="{{ \App\Util\Icon::icons(40) }}"> {{ __('tool.coinCalc.result.wood') }}</th>
                            <td id="res-wood">-</td>
                        </tr>
                        <tr>
                            <th><img src="{{ \App\Util\Icon::icons(41) }}"> {{ __('tool.coinCalc.result.clay') }}</th>
                            <td id="res-clay">-</td>
                        </tr>
                        <tr>
                            <th><img src="{{ \App\Util\Icon::icons(42) }}"> {{ __('tool.coinCalc.result.iron') }}</th>
                            <td id="res-iron">-</td>
                        </tr>
                        <tr>
                            <th><img src="{{ \App\Util\Icon::icons(48) }}"> {{ __('tool.coinCalc.coinFlag') }}</th>
                            <td id="res-flag">-</td>
                        </tr>
                        <tr>
                            <th><img src="{{ \App\Util\Icon::icons(49) }}"> {{ __('tool.coinCalc.flagBooster') }}</th>
                            <td id="res-booster">-</td>
                        </tr>
                        <tr>
                            <th><img src="{{ \App\Util\Icon::icons(50) }}"> {{ __('tool.coinCalc.nobleDecree') }}</th>
                            <td id="res-decree">-</td>
                        </tr>
                        @if((int) $worldData->win_condition === 5)
                            <tr>
                                <th><img
                                        src="{{ \App\Util\Icon::icons(51) }}"> {{ __('tool.coinCalc.runenFactor') }}
                                </th>
                                <td id="res-runeFactor">-</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
        <!-- ENDE Ergebnis-Card -->
    </div>
@endsection

@push('js')
    <script>
        function formatDE(n) {
            return Number(n).toLocaleString('de-DE');
        }

        function calculate() {
            const coinWood = {{ $coinCost['wood'] }};
            const coinClay = {{ $coinCost['stone'] }};
            const coinIron = {{ $coinCost['iron'] }};

            const currentCap = parseInt($('#current-cap').val());
            const targetCap = parseInt($('#target-cap').val());
            const flagPercent = parseInt($('#flag-percent').val());
            const hasBooster = $('#flag-booster').is(':checked');
            const hasDecree = $('#noble-decree').is(':checked');


            if (isNaN(currentCap) || isNaN(targetCap) || currentCap >= targetCap) {
                $('#res-coins, #res-cap, #res-wood, #res-clay, #res-iron').text('-');
                $('#res-flag, #res-booster, #res-decree').text('-');
                return;
            }

            let modifier = 1;

            if (flagPercent !== 0) {
                modifier = hasBooster ? (100 - flagPercent * 2) / 100 : (100 - flagPercent) / 100;
            }

            @if((int) $worldData->win_condition === 5)

            const runenBooster = parseFloat($('#runeFactor').val());
            if (isNaN(runenBooster)) {
                $('#res-coins, #res-cap, #res-wood, #res-clay, #res-iron').text('-');
                $('#res-flag, #res-booster, #res-decree,#res-runeFactor').text('-');
                return;
            }
            $('#res-runeFactor').text(formatDE(runenBooster));
            modifier *= runenBooster;
            @endif

            if (hasDecree) {
                modifier *= 0.9;
            }

            if (modifier < 0) modifier = 0;

            const coins = (targetCap - currentCap) * ((currentCap + targetCap) + 1) / 2;
            const wood = Math.round(coins * coinWood * modifier);
            const clay = Math.round(coins * coinClay * modifier);
            const iron = Math.round(coins * coinIron * modifier);

            $('#res-coins').text(formatDE(coins));
            $('#res-cap').html(`${currentCap} &rarr; ${targetCap}`);
            $('#res-wood').text(formatDE(wood));
            $('#res-clay').text(formatDE(clay));
            $('#res-iron').text(formatDE(iron));
            $('#res-flag').text(`${flagPercent}%`);
            $('#res-booster').text(hasBooster ? '{{ __("global.yes") }}' : '{{ __("global.no") }}');
            $('#res-decree').text(hasDecree ? '{{ __("global.yes") }}' : '{{ __("global.no") }}');
        }

        $(function () {
            $('#current-cap, #target-cap, #flag-percent, #flag-booster, #noble-decree, #runeFactor').on('input change', calculate);
            calculate();
        });
    </script>
@endpush
