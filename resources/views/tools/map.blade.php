@extends('layouts.temp')

@section('titel', $worldData->displayName(),': '.__('ui.tool.attackPlanner.title'))

@section('style')
    <link href="{{ asset('plugin/bootstrap-colorpicker/css/bootstrap-colorpicker.css') }}" rel="stylesheet">
@stop

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="col-12 p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('ui.tool.map.title')).' ['.$worldData->displayName().']' }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('ui.tool.map.title')).' ' }}
            </h1>
            <h4>
                {{ '['.$worldData->displayName().']' }}
            </h4>
        </div>
        <!-- ENDE Titel für Mobile Geräte -->
        @if($mode == 'edit')
        <div class="col-12 mt-2">
            <div class="card">
                <form id="mapEditForm" action="{{ route('tools.mapToolMode', [$wantedMap->id, 'edit', $wantedMap->edit_key]) }}">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="edit-tab" data-toggle="tab" href="#edit" role="tab" aria-controls="edit" aria-selected="true">{{ ucfirst(__('ui.tool.map.edit')) }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="settings-tab" data-toggle="tab" href="#settings" role="tab" aria-controls="settings" aria-selected="false">{{ ucfirst(__('ui.tool.map.settings')) }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="link-tab" data-toggle="tab" href="#link" role="tab" aria-controls="link" aria-selected="false">{{ ucfirst(__('ui.tool.map.links')) }}</a>
                        </li>
                    </ul>
                    <div class="card-body tab-content">
                        <div class="tab-pane fade show active" id="edit" role="tabpanel" aria-labelledby="edit-tab">
                            <div class="row pt-3">
                                @foreach(['ally', 'player', 'village'] as $type)
                                    <div id="main-{{$type}}" class="col-lg-4">
                                        {{ ucfirst(__('ui.tool.map.'.$type)) }}
                                        @foreach($defaults[$type] as $num=>$defValues)
                                            {!! \App\Http\Controllers\Tools\MapController::generateHTMLSelector($type, $num, $defValues) !!}
                                        @endforeach
                                    </div>
                                @endforeach
                                <div class="col-12">
                                    <input type="submit" class="btn btn-sm btn-success float-right">
                                </div>
                            </div>
                            <div id="model" style="display: none">
                                @foreach(['ally', 'player', 'village'] as $type)
                                    {!! \App\Http\Controllers\Tools\MapController::generateHTMLSelector($type, "model") !!}
                                @endforeach
                            </div>
                        </div>
                        <div class="tab-pane fade" id="link" role="tabpanel" aria-labelledby="link-tab">
                            <div class="row pt-3">
                                <div class="col-12">
                                    <div class="form-group row">
                                        <label class="control-label col-md-2">{{ ucfirst(__('ui.tool.map.editLink')) }}</label>
                                        <div class="col-1">
                                            <a class="btn btn-primary btn-sm" onclick="copy('edit')">{{ ucfirst(__('ui.tool.map.copy')) }}</a>
                                        </div>
                                        <div class="col-9">
                                            <input id="link-edit" type="text" class="form-control-plaintext form-control-sm disabled" value="{{ route('tools.mapToolMode', [$wantedMap->id, 'edit', $wantedMap->edit_key]) }}" />
                                            <small class="form-control-feedback">{{ ucfirst(__('ui.tool.map.editLinkDesc')) }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group row">
                                        <label class="control-label col-md-2">{{ ucfirst(__('ui.tool.map.showLink')) }}</label>
                                        <div class="col-1">
                                            <a class="btn btn-primary btn-sm" onclick="copy('show')">{{ ucfirst(__('ui.tool.map.copy')) }}</a>
                                        </div>
                                        <div class="col-9">
                                            <input id="link-show" type="text" class="form-control-plaintext form-control-sm disabled" value="{{ route('tools.mapToolMode', [$wantedMap->id, 'show', $wantedMap->show_key]) }}" />
                                            <small class="form-control-feedback">{{ ucfirst(__('ui.tool.map.showLinkDesc')) }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                            <div id='default-background-div' class='col-12 input-group mb-2 mr-sm-2'>
                                <div class='colour-picker-map input-group-prepend'>
                                    <span class='input-group-text colorpicker-input-addon'><i></i></span>
                                    <input name='default[background]' type='hidden' value='{{ $wantedMap->getBackgroundColour() }}'/>
                                </div>
                                <label class='form-control'>{{ __('ui.tool.map.defaultBackground') }}</label>
                            </div>
                            <div class="form-inline mb-2">
                                <div class="form-check col-lg-auto ml-auto">
                                    <input id="checkbox-show-player-hid" name="showPlayerHere" type="hidden" value="true" />
                                    <input id="checkbox-show-player" name="showPlayer" type="checkbox" class="form-check-input" {{ ($wantedMap->playerEnabled())?('checked="checked"'):('') }}/>
                                    <label class="form-check-label" for="checkbox-show-player">{{ __('ui.tool.map.showPlayer') }}</label>
                                </div>
                                <div id='default-player-div' class='col-lg-9 input-group'>
                                    <div class='colour-picker-map input-group-prepend'>
                                        <span class='input-group-text colorpicker-input-addon'><i></i></span>
                                        <input name='default[player]' type='hidden' value='{{ $wantedMap->getDefPlayerColour() }}'/>
                                    </div>
                                    <label class='form-control'>{{ __('ui.tool.map.defaultPlayer') }}</label>
                                </div>
                            </div>
                            <div class="form-inline mb-2">
                                <div class="form-check col-lg-auto ml-auto">
                                    <input id="checkbox-show-barbarian-hid" name="showBarbarianHere" type="hidden" value="true" />
                                    <input id="checkbox-show-barbarian" name="showBarbarian" type="checkbox" class="form-check-input" {{ ($wantedMap->barbarianEnabled())?('checked="checked"'):('') }}/>
                                    <label class="form-check-label" for="checkbox-show-barbarian">{{ __('ui.tool.map.showBarbarian') }}</label>
                                </div>
                                <div id='default-barbarian-div' class='col-lg-9 input-group'>
                                    <div class='colour-picker-map input-group-prepend'>
                                        <span class='input-group-text colorpicker-input-addon'><i></i></span>
                                        <input name='default[barbarian]' type='hidden' value='{{ $wantedMap->getDefBarbarianColour() }}'/>
                                    </div>
                                    <label class='form-control'>{{ __('ui.tool.map.defaultBarbarian') }}</label>
                                </div>
                            </div>
                            <div class="form-inline mb-2">
                                <div class="col-lg-6 input-group">
                                    <label for="map-zoom-value" class="col-lg-4">{{ __('ui.tool.map.zoom') }}</label>
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
                                    <label for="center-pos-x" class="col-lg-4">{{ __('ui.tool.map.center') }}</label>
                                    <input id="center-pos-x" name="centerX" class="form-control mr-1" placeholder="500" type="text" value="{{ $mapDimensions['cx'] }}"/>|
                                    <input id="center-pos-y" name="centerY" class="form-control ml-1" placeholder="500" type="text" value="{{ $mapDimensions['cy'] }}"/>
                                </div>
                            </div>
                            <div class="form-group float-right">
                                <input type="submit" class="btn btn-sm btn-success">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif
        <div class="col-12 mt-2">
            <div class="card">
                <ul class="nav nav-tabs" id="mapshowtabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active map-show-tab" id="size-1-tab" data-toggle="tab" href="#size-1" role="tab" aria-controls="size-1" aria-selected="true">{{ '1000x1000' }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link map-show-tab" id="size-2-tab" data-toggle="tab" role="tab" href="#size-2" aria-controls="size-2" aria-selected="false">{{ '700x700' }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link map-show-tab" id="size-3-tab" data-toggle="tab" role="tab" href="#size-3" aria-controls="size-3" aria-selected="false">{{ '500x500' }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link map-show-tab" id="size-4-tab" data-toggle="tab" role="tab" href="#size-4" aria-controls="size-4" aria-selected="false">{{ '200x200' }}</a>
                    </li>
                </ul>
                <div class="card-body tab-content">
                    <div class="tab-pane fade show active map-show-content text-center" id="size-1" role="tabpanel" aria-labelledby="size-1-tab"></div>
                    <div class="tab-pane fade map-show-content text-center" id="size-2" role="tabpanel" aria-labelledby="size-2-tab"></div>
                    <div class="tab-pane fade map-show-content text-center" id="size-3" role="tabpanel" aria-labelledby="size-3-tab"></div>
                    <div class="tab-pane fade map-show-content text-center" id="size-4" role="tabpanel" aria-labelledby="size-4-tab"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('plugin/bootstrap-colorpicker/js/bootstrap-colorpicker.js') }}"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
@if($mode == 'edit')
    <script>
        function copy(type) {
            /* Get the text field */
            var copyText = $("#link-" + type);
            /* Select the text field */
            copyText.select();
            /* Copy the text inside the text field */
            document.execCommand("copy");
        }

        var maxIndex = {
            ally:{{ (count($defaults['ally']) - 1) }},
            player:{{ (count($defaults['player']) - 1) }},
            village:{{ (count($defaults['village']) - 1) }}
        };

        $(function () {
            $('.colour-picker-map').colorpicker({
                useHashPrefix: false
            });

            $('.data-input-map').change(function (e) {
                checkPart(this, e);
                addNewParts(this, e);
            });
            
            $('.data-input-map').each(function() {
                if(this.value != null && this.value != "" && this.id.split('-')[3] != 'x') {
                    checkPart(this, null);
                    addNewParts(this, null);
                }
            });
        });

        /**
         * Function to dynamically generate new Input fields as the user fills them
         * @param Event e
         */
        function addNewParts(that, e) {
            var parts = that.id.split("-");

            if(parts[2] == maxIndex[parts[0]]) {
                maxIndex[parts[0]]++;
                var newElm = $('#'+parts[0]+'-mark-model-div')[0].outerHTML;
                $('#main-'+parts[0]).append(newElm.replace(/model/gi, maxIndex[parts[0]]));
                $('#'+parts[0]+'-mark-'+maxIndex[parts[0]]+'-div').change(function (e) {
                    checkPart(this, e);
                    addNewParts(this, e);
                });
                
                $('.colour-picker-map').colorpicker({
                    useHashPrefix: false
                });
            }
        }

        /**
         * Function to check if the input in a given field is valid.
         * If so it adds the id to the hidden input field
         * @param Event e
         */
        function checkPart(that, e) {
            var parts = that.id.split("-");

            switch(parts[0]) {
                case 'ally':
                    checkAlly(that, e);
                    break;
                case 'player':
                    checkPlayer(that, e);
                    break;
                case 'village':
                    checkVillage(that, e);
                    break;
            }     
        }

        function checkAlly(that, e) {
            axios.get('{{ route('index') }}/api/{{ $worldData->server->code }}/{{ $worldData->name }}/allyName/'+ encodeURI(that.value), {})
                .then((response) =>{
                    const data = response.data.data;
                    var parts = that.id.split("-");
                    
                    $('#'+that.id).removeClass('is-invalid').addClass('is-valid');
                    that.value = data['nameRaw'];
                    $('#'+parts[0]+'-'+parts[1]+'-'+parts[2]+'-id').val(data['allyID']);
                })
                .catch((error) =>{
                    var parts = that.id.split("-");
                    
                    $('#'+that.id).removeClass('is-valid').addClass('is-invalid');
                    $('#'+parts[0]+'-'+parts[1]+'-'+parts[2]+'-id').val('');
                });
        }

        function checkPlayer(that, e) {
            axios.get('{{ route('index') }}/api/{{ $worldData->server->code }}/{{ $worldData->name }}/playerName/'+ encodeURI(that.value), {})
                .then((response) =>{
                    const data = response.data.data;
                    var parts = that.id.split("-");
                    
                    $('#'+that.id).removeClass('is-invalid').addClass('is-valid');
                    $('#'+parts[0]+'-'+parts[1]+'-'+parts[2]+'-id').val(data['playerID']);
                })
                .catch((error) =>{
                    var parts = that.id.split("-");
                    
                    $('#'+that.id).removeClass('is-valid').addClass('is-invalid');
                    $('#'+parts[0]+'-'+parts[1]+'-'+parts[2]+'-id').val('');
                });
        }

        function checkVillage(that, e) {
            var parts = that.id.split("-");
            var x = $('#village-mark-'+parts[2]+'-x').val();
            var y = $('#village-mark-'+parts[2]+'-y').val();
            axios.get('{{ route('index') }}/api/{{ $worldData->server->code }}/{{ $worldData->name }}/villageCoords/'+ x + '/' + y, {
            })
                .then((response) =>{
                    const data = response.data.data;
                    $('#village-mark-'+parts[2]+'-x').removeClass('is-invalid').addClass('is-valid');
                    $('#village-mark-'+parts[2]+'-y').removeClass('is-invalid').addClass('is-valid');
                    $('#'+parts[0]+'-'+parts[1]+'-'+parts[2]+'-id').val(data['villageID']);
                })
                .catch((error) =>{
                    $('#village-mark-'+parts[2]+'-x').removeClass('is-invalid').addClass('is-invalid');
                    $('#village-mark-'+parts[2]+'-y').removeClass('is-invalid').addClass('is-invalid');
                    $('#'+parts[0]+'-'+parts[1]+'-'+parts[2]+'-id').val('');
                });
        }
    </script>
@endif
    <script>
        var sizeRoutes = {
            "size-1": "{{ route('api.map.show.sized', [$wantedMap->id, $wantedMap->show_key, '1000', '1000', 'base64']) }}",
            "size-2": "{{ route('api.map.show.sized', [$wantedMap->id, $wantedMap->show_key, '700', '700', 'base64']) }}",
            "size-3": "{{ route('api.map.show.sized', [$wantedMap->id, $wantedMap->show_key, '500', '500', 'base64']) }}",
            "size-4": "{{ route('api.map.show.sized', [$wantedMap->id, $wantedMap->show_key, '200', '200', 'base64']) }}"
        };
        $('.map-show-tab').click(function (e) {
            var targetID = this.attributes['aria-controls'].nodeValue;
            if($('#'+targetID)[0].innerHTML.length > 0) return;
            
            $.ajax({
                type: "GET",
                url: sizeRoutes[targetID] + "?" + Math.floor(Math.random() * 9000000 + 1000000),
                success: function(data){
                    $('#'+targetID).html('<img id="'+targetID+'-img" class="p-0" src="' + data + '" />');
                },
            });
        });
        
        $(function () {
            $('.active.map-show-tab').trigger('click');
        });
    </script>
@endsection
