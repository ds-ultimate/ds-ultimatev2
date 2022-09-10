<div class="tab-pane fade {{ ($active)? 'show active':'' }}" id="{{ $key }}" role="tabpanel" aria-labelledby="{{ $key }}-tab">
    <div id='default-background-div' class='col-12 input-group mb-2 mr-sm-2'>
        <div class='colour-picker-map input-group-prepend'>
            <span class='input-group-text colorpicker-input-addon'><i></i></span>
            <input name='default[background]' id="bg-colour" type='hidden' value='{{ $wantedMap->getBackgroundColour() }}'/>
        </div>
        <label class='form-control'>
            <i class="fas fa-undo mr-2 map-reset" newValue="{{ $wantedMap->getDefResetBackgroundColour() }}" data-toggle="tooltip" title="{{ __('tool.map.resetDefault') }}"></i>
            {{ __('tool.map.defaultBackground') }}
        </label>
    </div>
    <div class="form-inline mb-2">
        <div class="form-check col-lg-auto ml-auto">
            <input id="checkbox-show-player-hid" name="showPlayerHere" type="hidden" value="1" />
            <input id="checkbox-show-player" name="showPlayer" type="checkbox" defVal="{{ $wantedMap->playerEnabledDefault() }}" class="form-check-input resetable" @checked($wantedMap->playerEnabled()) />
            <label class="form-check-label" for="checkbox-show-player">{{ __('tool.map.showPlayer') }}</label>
        </div>
        <div id='default-player-div' class='col-lg-9 input-group'>
            <div class='colour-picker-map input-group-prepend'>
                <span class='input-group-text colorpicker-input-addon'><i></i></span>
                <input name='default[player]' id="player-colour" type='hidden' value='{{ $wantedMap->getDefPlayerColour() }}'/>
            </div>
            <label class='form-control'>
                <i class="fas fa-undo mr-2 map-reset" newValue="{{ $wantedMap->getDefResetPlayerColour() }}" data-toggle="tooltip" title="{{ __('tool.map.resetDefault') }}"></i>
                {{ __('tool.map.defaultPlayer') }}
            </label>
        </div>
    </div>
    <div class="form-inline mb-2">
        <div class="form-check col-lg-auto ml-auto">
            <input id="checkbox-show-barbarian-hid" name="showBarbarianHere" type="hidden" value="1" />
            <input id="checkbox-show-barbarian" name="showBarbarian" type="checkbox" defVal="{{ $wantedMap->barbarianEnabledDefault() }}" class="form-check-input resetable" @checked($wantedMap->barbarianEnabled()) />
            <label class="form-check-label" for="checkbox-show-barbarian">{{ __('tool.map.showBarbarian') }}</label>
        </div>
        <div id='default-barbarian-div' class='col-lg-9 input-group'>
            <div class='colour-picker-map input-group-prepend'>
                <span class='input-group-text colorpicker-input-addon'><i></i></span>
                <input name='default[barbarian]' type='hidden' id="barbarian-colour" value='{{ $wantedMap->getDefBarbarianColour() }}'/>
            </div>
            <label class='form-control'>
                <i class="fas fa-undo mr-2 map-reset" newValue="{{ $wantedMap->getDefResetBarbarianColour() }}" data-toggle="tooltip" title="{{ __('tool.map.resetDefault') }}"></i>
                {{ __('tool.map.defaultBarbarian') }}
            </label>
        </div>
    </div>
    <div class="form-inline mb-2">
        <div class="col-lg-3 input-group">
            <input id="map-zoom-auto-hid" name="zoomAutoHere" type="hidden" value="1" />
            <input id="map-zoom-auto" name="zoomAuto" type="checkbox" defVal="true" class="form-check-input resetable" @checked($wantedMap->autoDimensions) />
            <label for="map-zoom-auto">{{ __('tool.map.autoZoom') }}</label>
        </div>
        <div class="col-lg-3 input-group">
            <label for="map-zoom-value" class="mr-2">{{ __('tool.map.zoom') }}</label>
            <select class="form-control resetable" defVal="{{ $defMapDimensions['w'] }}" id="map-zoom-value" name="zoomValue">
                <option value="1000" @selected(($mapDimensions['w'] == 1000)) >0</option>
                <option value="599" @selected(($mapDimensions['w'] == 599)) >1</option>
                <option value="359" @selected(($mapDimensions['w'] == 359)) >2</option>
                <option value="215" @selected(($mapDimensions['w'] == 215)) >3</option>
                <option value="129" @selected(($mapDimensions['w'] == 129)) >4</option>
                <option value="77" @selected(($mapDimensions['w'] == 77)) >5</option>
                <option value="46" @selected(($mapDimensions['w'] == 46)) >6</option>
                <option value="28" @selected(($mapDimensions['w'] == 28)) >7</option>
                <option value="16" @selected(($mapDimensions['w'] == 16)) >8</option>
                <option value="10" @selected(($mapDimensions['w'] == 10)) >9</option>
            </select>
        </div>
        <div id="center-pos-div" class="input-group col-lg-6 mb-2">
            <label for="center-pos-x" class="col-lg-4">{{ __('tool.map.center') }}</label>
            <input id="center-pos-x" name="centerX" class="form-control mr-1 resetable" defVal="{{ $defMapDimensions['cx'] }}" placeholder="{{ $defMapDimensions['cx'] }}" type="text" value="{{ $mapDimensions['cx'] }}"/>|
            <input id="center-pos-y" name="centerY" class="form-control ml-1 resetable" defVal="{{ $defMapDimensions['cy'] }}" placeholder="{{ $defMapDimensions['cy'] }}" type="text" value="{{ $mapDimensions['cy'] }}"/>
        </div>
    </div>
    <div class="form-inline mb-2 col-lg-6">
        <label for="markerFactor" class="col-lg-auto">{{ ucfirst(__('tool.map.markerFactor')) }}</label>
        <input type="range" class="custom-range w-auto flex-lg-fill resetable" defVal="{{ $wantedMap->makerFactorDefault() }}" min="0" max="0.4" step="0.01" id="markerFactor" value="{{ $wantedMap->makerFactor }}" name="markerFactor">
        <div id="markerFactorText" class="ml-4">{{ intval($wantedMap->markerFactor*100) }}%</div>
    </div>
    <div class="form-inline mb-2">
        <div class="form-check col-lg-4">
            <input id="checkbox-continent-numbers-hid" name="continentNumbersHere" type="hidden" value="1" />
            <input id="checkbox-continent-numbers" name="continentNumbers" type="checkbox" class="form-check-input resetable" defVal="true" @checked($wantedMap->continentNumbersEnabled()) />
            <label class="form-check-label" for="checkbox-continent-numbers">{{ __('tool.map.showContinentNumbers') }}</label>
        </div>
        @if($mapType == "map")
            <div id="checkbox-auto-update-container" class="form-check col-lg-4 position-relative">
                <input id="checkbox-auto-update-hid" name="autoUpdateHere" type="hidden" value="1" />
                <input id="checkbox-auto-update" name="autoUpdate" type="checkbox" class="form-check-input resetable" defVal="false" @checked($wantedMap->shouldUpdate) />
                <label class="form-check-label" for="checkbox-auto-update" data-toggle="tooltip" title="{{ __('tool.map.autoUpdateHelp') }}" data-container="checkbox-auto-update-container">{{ __('tool.map.autoUpdate') }}</label>
            </div>
        @endif
    </div>
    <div class="form-group float-right">
        <button id="resetAll" context="{{ $key }}" class="btn btn-sm btn-warning mr-4">{{ __('tool.map.resetDefault') }}</button>
        <input type="submit" class="btn btn-sm btn-success">
    </div>
</div>

@push('js')
<script>
    $(function () {
        @if($wantedMap->quickChangesAllowed())
            $('#checkbox-show-player').change(store);
            $('#checkbox-show-barbarian').change(store);
            $('#checkbox-continent-numbers').change(store);
            @if($mapType == "map")
                $('#checkbox-auto-update').change(store);
            @endif
            $('#map-zoom-auto').change(store);
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
        
        $('.colour-picker-map').on('colorpickerCreate', function(e) {
            $('.map-reset', $(this).parent()).click(function(ein) {
                e.colorpicker.setValue($(this).attr("newValue"));
                
                @if($wantedMap->quickChangesAllowed())
                    store();
                @endif
            });
        });
        
        $('#resetAll').click(function(e) {
            var context = $("#" + $(this).attr("context"));
            $(".map-reset", context).trigger("click");
            
            $(".resetable").each(function() {
                var def = $(this).attr("defVal");
                if($(this).attr("type") == "checkbox") {
                    this.checked = def;
                } else {
                    $(this).val(def);
                    $(this).trigger("input");
                }
            })
        });
    });
</script>
@endpush
