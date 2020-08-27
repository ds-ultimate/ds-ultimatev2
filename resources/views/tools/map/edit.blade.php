<?php
    function generateHTMLSelector($type, $id, $defaultContent=null) {
        if($type == 'ally' || $type == 'player') {
            if($defaultContent != null) {
                $defName = $defaultContent['name'];
                $defCol = $defaultContent['colour'];
                $defShowText = ($defaultContent['text'])?('checked="checked"'):("");
                $defHighLight = ($defaultContent['highlight'])?('checked="checked"'):("");
            } else {
                $defName = '';
                $defCol = 'FFFFFF';
                $defShowText = "";
                $defHighLight = "";
            }?>
            <div id="{{ "$type-mark-$id-div" }}" class="input-group mb-2 mr-sm-2">
                <div class="colour-picker-map input-group-prepend">
                    <span class="input-group-text colorpicker-input-addon"><i></i></span>
                    <input name="{{ "mark[$type][$id][colour]" }}" type="hidden" value="{{ $defCol }}"/>
                </div>
                <select id="{{ "$type-mark-$id-id" }}" name="{{ "mark[$type][$id][id]" }}"
                    class="form-control mr-1 data-input-map select2-{{ $type }} select2-single">
                    @if($defaultContent != null)
                    <option value="{{ $defaultContent['id'] }}" selected="selected">{{ $defaultContent['name'] }}</option>
                    @endif
                </select>
                <div class="form-check ml-2 mt-2">
                    <input name="{{ "mark[$type][$id][textHere]" }}" type="hidden" value="true" />
                    <input type="checkbox" class="form-check-input position-static showText-{{ $type }} showTextBox" name="{{ "mark[$type][$id][text]" }}"
                           data-toggle="tooltip" title="{{ ucfirst(__('tool.map.showText')) }}" {{$defShowText}}>
                </div>
                <div class="form-check ml-2 mt-2">
                    <input name="{{ "mark[$type][$id][hLightHere]" }}" type="hidden" value="true" />
                    <input type="checkbox" class="form-check-input position-static highlight-{{ $type }} showTextBox" name="{{ "mark[$type][$id][hLight]" }}"
                           data-toggle="tooltip" title="{{ ucfirst(__('tool.map.highlight')) }}" {{$defHighLight}}>
                </div>
            </div>
            <?php
        } else if($type == 'village') {
            if($defaultContent != null) {
                $defX = $defaultContent['x'];
                $defY = $defaultContent['y'];
                $defCol = $defaultContent['colour'];
                $defHighLight = ($defaultContent['highlight'])?('checked="checked"'):("");
            } else {
                $defX = '';
                $defY = '';
                $defCol = 'FFFFFF';
                $defHighLight = "";
            }?>
            <div id="{{ "$type-mark-$id-div" }}" class="input-group mb-2 mr-sm-2">
                <div class="colour-picker-map input-group-prepend">
                    <span class="input-group-text colorpicker-input-addon"><i></i></span>
                    <input name="{{ "mark[$type][$id][colour]" }}" type="hidden" value="{{ $defCol }}"/>
                </div>
                <input id="{{ "$type-mark-$id-id" }}" name="{{ "mark[$type][$id][id]" }}" type="hidden"/>
                <input id="{{ "$type-mark-$id-x" }}" name="{{ "mark[$type][$id][x]" }}" class="form-control mr-1 coord-data-input checked-data-input-map data-input-map" placeholder="500" type="number" min="0" max="1000" value="{{ $defX }}"/>|
                <input id="{{ "$type-mark-$id-y" }}" name="{{ "mark[$type][$id][y]" }}" class="form-control ml-1 coord-data-input checked-data-input-map data-input-map" placeholder="500" type="number" min="0" max="1000" value="{{ $defY }}"/>
                <div class="form-check ml-2 mt-2">
                    <input name="{{ "mark[$type][$id][hLightHere]" }}" type="hidden" value="true" />
                    <input type="checkbox" class="form-check-input position-static highlight-{{ $type }} showTextBox" name="{{ "mark[$type][$id][hLight]" }}"
                           data-toggle="tooltip" title="{{ ucfirst(__('tool.map.highlight')) }}" {{$defHighLight}}>
                </div>
            </div>
            <?php
        }
    }
?>

<div class="tab-pane fade {{ ($active)? 'show active':'' }}" id="{{ $key }}" role="tabpanel" aria-labelledby="{{ $key }}-tab">
    <div class="col-12 text-center">
        <b id="title-show" class="h3 card-title">{{ ($wantedMap->title === null)? __('ui.noTitle'): $wantedMap->title }}</b>
        <input id="title-input" onfocus="this.select();" class="form-control mb-3" style="display:none" name="title" type="text">
        <a id="title-edit" onclick="titleEdit()" style="cursor:pointer;"><i class="far fa-edit text-muted h5 ml-2"></i></a>
        <a id="title-save" onclick="titleSave()" style="cursor:pointer; display:none"><i class="far fa-save text-muted h5 ml-2"></i></a>
        <hr>
    </div>
    <div class="row pt-3">
        @foreach(['ally', 'player', 'village'] as $type)
            <div id="main-{{$type}}" class="col-lg-4">
                {{ ucfirst(__('tool.map.'.$type)) }}<br>
                @if($type != 'village')
                    <div class="form-check form-check-inline float-right mr-0">
                        <label class="form-check-label mr-2" for="showTextAll-{{ $type }}">{{ ucfirst(__('tool.map.showAllText')) }}</label>
                        /
                        <label class="form-check-label ml-2 mr-2" for="highlightAll-{{ $type }}">{{ ucfirst(__('tool.map.highlightAll')) }}</label>
                        <input class="form-check-input change-all showTextBox mr-2" type="checkbox" aria-for="showText-{{ $type }}"
                               id="showTextAll-{{ $type }}" data-toggle="tooltip" title="{{ ucfirst(__('tool.map.showAllText')) }}">
                        <input class="form-check-input change-all highlightBox ml-2" type="checkbox" aria-for="highlight-{{ $type }}"
                               id="highlightAll-{{ $type }}" data-toggle="tooltip" title="{{ ucfirst(__('tool.map.highlightAll')) }}">
                    </div>
                @else
                    <div class="form-check form-check-inline float-right mr-0">
                        <label class="form-check-label mr-2" for="highlightAll-{{ $type }}">{{ ucfirst(__('tool.map.highlightAll')) }}</label>
                        <input class="form-check-input change-all highlightBox ml-2" type="checkbox" aria-for="highlight-{{ $type }}"
                               id="highlightAll-{{ $type }}" data-toggle="tooltip" title="{{ ucfirst(__('tool.map.highlightAll')) }}">
                    </div>
                @endif
                <br>
                @foreach($defaults[$type] as $num=>$defValues)
                    {!! generateHTMLSelector($type, $num, $defValues) !!}
                @endforeach
            </div>
        @endforeach
        <div class="col-12">
            <input type="submit" class="btn btn-sm btn-success float-right">
        </div>
    </div>
    <div id="model" style="display: none">
        @foreach(['ally', 'player', 'village'] as $type)
            <textarea id="{{ $type }}-mark-model-area">
                {!! generateHTMLSelector($type, "model") !!}
            </textarea>
        @endforeach
    </div>
</div>

@push('js')
<script src="{{ asset('plugin/select2/select2.full.min.js') }}"></script>
<script>
    /**
     * TITLE
     */
    $(function() {
        $('#title-input').on("keypress keyup blur",function (event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                titleSave();
            }
        });
    });
    
    function titleEdit() {
        var input = $('#title-input');
        var title = $('#title-show');
        var edit = $('#title-edit');
        var save = $('#title-save');
        var t = (title.html() === '{{ __('ui.noTitle') }}')? '': title.html();
        title.hide();
        edit.hide();
        input.val(t).show().focus();
        save.show();
    }

    function titleSave() {
        var input = $('#title-input');
        var title = $('#title-show');
        var edit = $('#title-edit');
        var save = $('#title-save');
        var t = (input.val() === '')? '{{ __('ui.noTitle') }}': input.val();
        axios.post('{{ route('index') }}/tools/map/{{ $wantedMap->id }}/title/{{ $wantedMap->edit_key }}/' + t, {
        })
            .then((response) => {
                input.hide();
                save.hide();
                title.html(t).show();
                edit.show();
            })
            .catch((error) => {
                console.log(error);
            });
    }
    
    
    /**
     * ALLY / PLAYER / VILLAGE
     * marker selector
     */
    $(function () {
        $('.data-input-map').each(function() {
            if(this.value != null && this.value != "") {
                addNewParts(this, null);
            }
        });

        $('.checked-data-input-map').each(function() {
            if(this.value != null && this.value != "" && this.id.split('-')[3] != 'x') {
                checkPart(this, null);
            }
        });
        
        $('.change-all').change(function(e) {
            $('.'+this.attributes['aria-for'].nodeValue).prop('checked', this.checked);
        });
        addCustomLibs(null);
        
        @if($wantedMap->cached_at === null)
            $('.data-input-map').change(store);
        @endif
    });
    
    var maxIndex = {
        ally:{{ (count($defaults['ally']) - 1) }},
        player:{{ (count($defaults['player']) - 1) }},
        village:{{ (count($defaults['village']) - 1) }}
    };
    
    /**
     * Function to dynamically generate new Input fields as the user fills them
     * @param Event e
     */
    function addNewParts(that, e) {
        var parts = that.id.split("-");

        if(parts[2] == maxIndex[parts[0]]) {
            maxIndex[parts[0]]++;
            var newElm = $('#'+parts[0]+'-mark-model-area')[0].value;
            $('#main-'+parts[0]).append(newElm.replace(/model/gi, maxIndex[parts[0]]));
            var par = $('#'+parts[0]+'-mark-'+maxIndex[parts[0]]+'-div');
            addCustomLibs(par);
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
            case 'village':
                checkVillage(that, e);
                break;
        }
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

    function addCustomLibs(context) {
        context = (context)?($(context)):($(document));

        $('.select2-player', context).select2({
            ajax: {
                url: '{{ route("api.select2Player", [$worldData->server->code, $worldData->name]) }}',
                data: function (params) {
                    var query = {
                        search: params.term,
                        page: params.page || 1
                    }

                    // Query parameters will be ?search=[term]&page=[page]
                    return query;
                },
                delay: 250
            },
            allowClear: true,
            placeholder: '{{ ucfirst(__('tool.map.playerSelectPlaceholder')) }}',
            theme: "bootstrap4"
        });
        $('.select2-ally', context).select2({
            ajax: {
                url: '{{ route("api.select2Ally", [$worldData->server->code, $worldData->name]) }}',
                data: function (params) {
                    var query = {
                        search: params.term,
                        page: params.page || 1
                    }

                    // Query parameters will be ?search=[term]&page=[page]
                    return query;
                },
                delay: 250
            },
            allowClear: true,
            placeholder: '{{ ucfirst(__('tool.map.allySelectPlaceholder')) }}',
            theme: "bootstrap4"
        });

        $('.colour-picker-map', context).colorpicker({
            useHashPrefix: false,
            extensions: [{
                name: 'swatches',
                options: {
                    colors: {
                        'c11': '#ffffff', 'c12': '#eeece1', 'c13': '#d99694', 'c14': '#c0504d', 'c15': '#f79646', 'c16': '#ffff00', 'c17': '#9bbb59',
                        'c21': '#4bacc6', 'c22': '#548dd4', 'c23': '#1f497d', 'c24': '#8064a2', 'c25': '#f926e5', 'c26': '#7f6000', 'c27': '#000000',
                    },
                    namesAsValues: false
                }
            }]
        });

        $('.data-input-map').change(function() {
            if(this.value != null && this.value != "") {
                addNewParts(this, null);
            }
        });

        $('.checked-data-input-map').change(function() {
            if(this.value != null && this.value != "") {
                checkPart(this, null);
            }
        });
        
        $('.coord-data-input').bind('paste', function(e) {
            var target = this.id.substring(0, this.id.lastIndexOf("-"));
            var pastedData = e.originalEvent.clipboardData.getData('text');
            var coords = pastedData.split("|");
            if (coords.length === 2) {
                e.preventDefault()
                $('#' + target + '-x').val(coords[0].substring(0, 3));
                $('#' + target + '-y').val(coords[1].substring(0, 3));
                $(this).change();
            }
        });
    }
</script>
@endpush
