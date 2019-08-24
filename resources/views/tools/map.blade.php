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
                            </div>
                            <div class="col-12">
                                <input type="submit" class="btn btn-sm btn-success float-right">
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
                                        <label class="control-label col-2">{{ ucfirst(__('ui.tool.map.editLink')) }}</label>
                                        <div class="col-1">
                                            <button class="btn btn-primary btn-sm" onclick="copy('edit')">{{ ucfirst(__('ui.tool.map.copy')) }}</button>
                                        </div>
                                        <div class="col-9">
                                            <input id="link-edit" type="text" class="form-control-plaintext form-control-sm disabled" value="{{ route('tools.mapToolMode', [$wantedMap->id, 'edit', $wantedMap->edit_key]) }}" />
                                            <small class="form-control-feedback ml-2">{{ ucfirst(__('ui.tool.map.editLinkDesc')) }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group row">
                                        <label class="control-label col-2">{{ ucfirst(__('ui.tool.map.showLink')) }}</label>
                                        <div class="col-1">
                                            <button class="btn btn-primary btn-sm" onclick="copy('show')">{{ ucfirst(__('ui.tool.map.copy')) }}</button>
                                        </div>
                                        <div class="col-9">
                                            <input id="link-show" type="text" class="form-control-plaintext form-control-sm disabled" value="{{ route('tools.mapToolMode', [$wantedMap->id, 'show', $wantedMap->show_key]) }}" />
                                            <small class="form-control-feedback ml-2">{{ ucfirst(__('ui.tool.map.showLinkDesc')) }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif
        <div class="col-12 mt-2">
            <div class="card">
                <div class="card-body">
                    <img class="container-fluid" src="{{ route('api.map.show', [$wantedMap->id, $wantedMap->show_key, 'png']).'?'.Str::random(10) }}" />
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('plugin/bootstrap-colorpicker/js/bootstrap-colorpicker.js') }}"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
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
                    that.value = data['name'];
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
@endsection
