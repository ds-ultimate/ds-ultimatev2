<div class="tab-pane fade {{ ($active)? 'show active':'' }}" id="{{ $key }}" role="tabpanel" aria-labelledby="{{ $key }}-tab">
    <div id='default-background-div' class='col-12 input-group mb-2 mr-sm-2'>
        <div class='colour-picker-map input-group-prepend'>
            <span class='input-group-text colorpicker-input-addon'><i></i></span>
            <input name='default[background]' id="bg-colour" type='hidden' value='{{ $wantedMap->getBackgroundColour() }}'/>
        </div>
        <label class='form-control'>{{ __('tool.map.defaultBackground') }}</label>
    </div>
    <div class="form-inline mb-2">
        <div class="form-check col-lg-auto ml-auto">
            <input id="checkbox-show-player-hid" name="showPlayerHere" type="hidden" value="true" />
            <input id="checkbox-show-player" name="showPlayer" type="checkbox" class="form-check-input" {{ ($wantedMap->playerEnabled())?('checked="checked"'):('') }}/>
            <label class="form-check-label" for="checkbox-show-player">{{ __('tool.map.showPlayer') }}</label>
        </div>
        <div id='default-player-div' class='col-lg-9 input-group'>
            <div class='colour-picker-map input-group-prepend'>
                <span class='input-group-text colorpicker-input-addon'><i></i></span>
                <input name='default[player]' id="player-colour" type='hidden' value='{{ $wantedMap->getDefPlayerColour() }}'/>
            </div>
            <label class='form-control'>{{ __('tool.map.defaultPlayer') }}</label>
        </div>
    </div>
    <div class="form-inline mb-2">
        <div class="form-check col-lg-auto ml-auto">
            <input id="checkbox-show-barbarian-hid" name="showBarbarianHere" type="hidden" value="true" />
            <input id="checkbox-show-barbarian" name="showBarbarian" type="checkbox" class="form-check-input" {{ ($wantedMap->barbarianEnabled())?('checked="checked"'):('') }}/>
            <label class="form-check-label" for="checkbox-show-barbarian">{{ __('tool.map.showBarbarian') }}</label>
        </div>
        <div id='default-barbarian-div' class='col-lg-9 input-group'>
            <div class='colour-picker-map input-group-prepend'>
                <span class='input-group-text colorpicker-input-addon'><i></i></span>
                <input name='default[barbarian]' type='hidden' id="barbarian-colour" value='{{ $wantedMap->getDefBarbarianColour() }}'/>
            </div>
            <label class='form-control'>{{ __('tool.map.defaultBarbarian') }}</label>
        </div>
    </div>
    <div class="form-inline mb-2">
        <div class="col-lg-6 input-group">
            <label for="map-zoom-value" class="col-lg-4">{{ __('tool.map.zoom') }}</label>
            <select class="form-control col-lg-2" id="map-zoom-value" name="zoomValue">
                <option value="1000"{{ ($mapDimensions['w'] == 1000)?(' selected="selected"'):('') }}>0</option>
                <option value="599"{{ ($mapDimensions['w'] == 599)?(' selected="selected"'):('') }}>1</option>
                <option value="359"{{ ($mapDimensions['w'] == 359)?(' selected="selected"'):('') }}>2</option>
                <option value="215"{{ ($mapDimensions['w'] == 215)?(' selected="selected"'):('') }}>3</option>
                <option value="129"{{ ($mapDimensions['w'] == 129)?(' selected="selected"'):('') }}>4</option>
                <option value="77"{{ ($mapDimensions['w'] == 77)?(' selected="selected"'):('') }}>5</option>
                <option value="46"{{ ($mapDimensions['w'] == 46)?(' selected="selected"'):('') }}>6</option>
                <option value="28"{{ ($mapDimensions['w'] == 28)?(' selected="selected"'):('') }}>7</option>
                <option value="16"{{ ($mapDimensions['w'] == 16)?(' selected="selected"'):('') }}>8</option>
                <option value="10"{{ ($mapDimensions['w'] == 10)?(' selected="selected"'):('') }}>9</option>
            </select>
        </div>
        <div id="center-pos-div" class="input-group col-lg-6 mb-2">
            <label for="center-pos-x" class="col-lg-4">{{ __('tool.map.center') }}</label>
            <input id="center-pos-x" name="centerX" class="form-control mr-1" placeholder="500" type="text" value="{{ $mapDimensions['cx'] }}"/>|
            <input id="center-pos-y" name="centerY" class="form-control ml-1" placeholder="500" type="text" value="{{ $mapDimensions['cy'] }}"/>
        </div>
    </div>
    <div class="form-inline mb-2 col-lg-6">
        <label for="markerFactor" class="col-lg-auto">{{ ucfirst(__('tool.map.markerFactor')) }}</label>
        <input type="range" class="custom-range w-auto flex-lg-fill" min="0" max="0.4" step="0.01" id="markerFactor" value="{{ $wantedMap->makerFactor }}" name="markerFactor">
        <div id="markerFactorText" class="ml-4">{{ intval($wantedMap->markerFactor*100) }}%</div>
    </div>
    <div class="form-inline mb-2">
        <div class="form-check col-lg-4">
            <input id="checkbox-continent-numbers-hid" name="continentNumbersHere" type="hidden" value="true" />
            <input id="checkbox-continent-numbers" name="continentNumbers" type="checkbox" class="form-check-input" {{ ($wantedMap->continentNumbersEnabled())?('checked="checked"'):('') }}/>
            <label class="form-check-label" for="checkbox-continent-numbers">{{ __('tool.map.showContinentNumbers') }}</label>
        </div>
        <div id="checkbox-auto-update-container" class="form-check col-lg-4 position-relative">
            <input id="checkbox-auto-update-hid" name="autoUpdateHere" type="hidden" value="true" />
            <input id="checkbox-auto-update" name="autoUpdate" type="checkbox" class="form-check-input" {{ ($wantedMap->shouldUpdate)?('checked="checked"'):('') }}/>
            <label class="form-check-label" for="checkbox-auto-update" data-toggle="tooltip" title="{{ __('tool.map.autoUpdateHelp') }}" data-container="checkbox-auto-update-container">{{ __('tool.map.autoUpdate') }}</label>
        </div>
    </div>
    <div class="form-group float-right">
        <input type="submit" class="btn btn-sm btn-success">
    </div>
</div>

@push('js')
<script>
    $(function () {
        @if($wantedMap->cached_at === null)
            $('#checkbox-show-player').change(store);
            $('#checkbox-show-barbarian').change(store);
            $('#checkbox-continent-numbers').change(store);
            $('#checkbox-auto-update').change(store);
            $('#map-zoom-value').change(store);
            $('#center-pos-x').change(store);
            $('#center-pos-y').change(store);
            $('.showTextBox').change(store);
            $('.highlightBox').change(store);
            $('#markerFactor').change(store);
            $('#markerFactor').on("input", function(slideEvt) {
                $("#markerFactorText").text(parseInt(slideEvt.target.value*100) + "%");
            });
        @endif
    });
</script>
@endpush
